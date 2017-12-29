<?php
class Advalbum_AlbumController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$album_id = 0;
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'view') -> isValid())
			return;

		if (0 !== ($photo_id = ( int )$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('advalbum_photo', $photo_id)))
		{
			Engine_Api::_() -> core() -> setSubject($photo);
		}
		
		else
		if (0 !== ($album_id = ( int )$this -> _getParam('album_id')) && null !== ($album = Engine_Api::_() -> getItem('advalbum_album', $album_id)))
		{
			Engine_Api::_() -> core() -> setSubject($album);
		}
	}

	/*
	 * Download all images in album
	 *
	 */
	public function downloadAction()
	{
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> content -> setNoRender();
		Engine_Api::_() -> getApi('createzipfile', 'advalbum') -> downloadAlbum(( int )$this -> _getParam('album_id'));
	}

	public function editAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_main');

		// Hack navigation
		foreach ($navigation->getPages () as $page)
		{
			if ($page -> route != 'album_general' || $page -> action != 'manage')
				continue;
			$page -> active = true;
		}

		// Prepare data
		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Album_Edit();
		if ($album->virtual)
		{
			$form->removeElement('auth_add_photo');
			$form->removeElement('auth_tag');
		}
		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($album -> toArray());
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone'
			);
			foreach ($roles as $role)
			{
				if (1 === $auth -> isAllowed($album, $role, 'view'))
				{
					$form -> auth_view -> setValue($role);
				}
				if (1 === $auth -> isAllowed($album, $role, 'comment'))
				{
					$form -> auth_comment -> setValue($role);
				}
				if (!$album->virtual)
				{
					if (1 === $auth -> isAllowed($album, $role, 'addphoto'))
					{
						$form -> auth_add_photo -> setValue($role);
					}
					if (1 === $auth -> isAllowed($album, $role, 'tag'))
					{
						$form -> auth_tag -> setValue($role);
					}
				}				
			}

			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
			return;
		}

		// Process
		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			$album -> setFromArray($values);
			$album -> save();

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone'
			);
			if ($values['auth_view'])
				$auth_view = $values['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
			}
			
			if ($values['auth_comment'])
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($values['auth_comment'], $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
			}
			if (!$album->virtual)
			{
				if ($values['auth_add_photo'])
					$auth_add_photo = $values['auth_add_photo'];
				else
					$auth_add_photo = "everyone";
				$addphotoMax = array_search($values['auth_add_photo'], $roles);
				foreach ($roles as $i => $role)
				{
					$auth -> setAllowed($album, $role, 'addphoto', ($i <= $addphotoMax));
				}
				
				if ($values['auth_tag'])
					$auth_tag = $values['auth_tag'];
				else
					$auth_tag = "everyone";
				$tagMax = array_search($values['auth_tag'], $roles);
				foreach ($roles as $i => $role)
				{
					$auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
				}
			}
			$db -> commit();
		}
		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$db -> beginTransaction();
		try
		{
			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject ( $album ) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			$db -> commit();
		}
		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'), 'album_general', true);
	}

	public function orderAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		$album = Engine_Api::_() -> core() -> getSubject();

		$order = $this -> _getParam('order');
		if (!$order)
		{
			$this -> view -> status = false;
			return;
		}

		// Get a list of all photos in this album, by order
		$photoTable = Engine_Api::_() -> getItemTable('advalbum_photo');
		$currentOrder = $photoTable -> select() -> from($photoTable, 'photo_id') -> where('album_id = ?', $album -> getIdentity()) -> order('order ASC') -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);

		// Find the starting point?
		$start = null;
		$end = null;
		for ($i = 0, $l = count($currentOrder); $i < $l; $i++)
		{
			if (in_array($currentOrder[$i], $order))
			{
				$start = $i;
				$end = $i + count($order);
				break;
			}
		}

		if (null === $start || null === $end)
		{
			$this -> view -> status = false;
			return;
		}

		for ($i = 0, $l = count($currentOrder); $i < $l; $i++)
		{
			if ($i >= $start && $i <= $end)
			{
				$photo_id = $order[$i - $start];
			}
			else
			{
				$photo_id = $currentOrder[$i];
			}
			$photoTable -> update(array('order' => $i), array('photo_id = ?' => $photo_id));
		}

		$this -> view -> status = true;
	}

	public function viewAction()
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;

		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();
		if (@!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> isValid())
			return;
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> playlist = $playlist = $this -> _getParam('playlist');
		if($this -> _getParam('slideshow'))
		{
			$this -> view -> body_class = 'slideshow-active';
		}
		else 
		{
			$this -> _helper -> content -> setEnabled();
		}
		$this -> view -> slideshow = $slideshow = $this -> _getParam('slideshow');
		$this -> view -> rating_count = Engine_Api::_() -> advalbum() -> countRating($album -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		$this -> view -> is_rated = Engine_Api::_() -> advalbum() -> checkRated($album -> getIdentity(), $viewer -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$tableName = $table -> info('name');
		if ($album->virtual)
		{
			$virtualPhotoTbl = Engine_Api::_() -> getDbtable('virtualphotos', 'advalbum');
			$virtualPhotoTblName = $virtualPhotoTbl -> info('name');
			$db = $virtualPhotoTbl->getAdapter();
			$virtualPhotoIds = $db
					-> select()
					-> from($virtualPhotoTblName)
					-> where("album_id = ? ", $album -> getIdentity())
					-> query()
					-> fetchAll(Zend_Db::FETCH_COLUMN, 1);
			$virtualPhotoIds = array_unique($virtualPhotoIds);
			if (!count($virtualPhotoIds))
			{
				$virtualPhotoIds = "";
			}
			$select = $table -> select() -> from($tableName) -> where("photo_id IN (?)", $virtualPhotoIds) -> order("order");
			$photo_list = $table -> fetchAll($select);
		}
		else
		{
			$select = $table -> select() -> from($tableName) -> where("album_id = ?", $album -> album_id) -> order("order");
			$photo_list = $table -> fetchAll($select);
		}

		$this -> view -> rating_count = Engine_Api::_() -> advalbum() -> countRating($album -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		$this -> view -> is_rated = Engine_Api::_() -> advalbum() -> checkRated($album -> getIdentity(), $viewer -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		$session = new Zend_Session_Namespace('mobile');

		if ($slideshow || $playlist)
		{
			// get the photos (all)
			if ($playlist)
			{
				$this -> _helper -> layout -> disableLayout();
				$this -> view -> html_full = $this -> view -> partial('_playlist.tpl', array(
					'album' => $album,
					'photo_list' => $photo_list
				));
				$response = Zend_Controller_Front::getInstance() -> getResponse();
				$response -> setHeader('Content-Type', 'text/xml', TRUE);
			}
			else
			{
				$themes   = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
        		$activeTheme = $themes->getRowMatching('active', 1);
        		$name_theme = $activeTheme -> name;
				$this -> _helper -> layout -> disableLayout();
				$this -> view -> html_full = $this -> view -> partial('_slideshow.tpl', array(
					'album' => $album,
					'photo_list' => $photo_list,
					'effect' => $this -> _getParam('effect'),
					'theme' => $name_theme
				));
			}
		}
		else
		{
			// Prepare params
			$this -> view -> page = $page = $this -> _getParam('page');

			// Prepare data
			if ($album->virtual)
			{
				$this -> view -> paginator = $paginator = Zend_Paginator::factory($photo_list);
			}
			else
			{
				$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
			}

			// Do other stuff
			$this -> view -> mine = true;
			$this -> view -> can_add_photo= $can_add_photo = $this -> _helper -> requireAuth() -> setAuthParams($album, null, 'addphoto') -> checkRequire();

			$this -> view -> can_edit = $this -> _helper -> requireAuth() -> setAuthParams($album, null, 'edit') -> checkRequire();
			if (!$album -> getOwner() -> isSelf(Engine_Api::_() -> user() -> getViewer()))
			{
				$album -> view_count++;
				$album -> save();
				$this -> view -> mine = false;
			}

			$photo_listing_id = 'photo_listing_in_album_view';
			$no_photos_message = $this -> view -> translate('There is no photo.');
			if ($this -> view -> mine || $this -> view -> can_edit)
			{
				$sortable = 1;
			}
			else
			{
				$sortable = 0;
			}
			$photo_listing_id = 'photo_listing_in_album_' . $album -> getIdentity();
			$no_photos_message = $this -> view -> translate('There is no photo in this album.');
			$strRand = rand(1, 100) . rand(1, 100);
			$this -> view -> rand = $strRand;
			$auto_show = 0;
			if ($this -> _getParam('id') && $this -> _getParam('id') != "")
			{
				$auto_show = $this -> _getParam('id');
			}
		}

        // Set meta
        $og = '<meta property="og:image" content="' . $this->finalizeUrl($album->getPhotoUrl()) . '" />';
        $og .= '<meta property="og:image:width" content="640" />';
        $og .= '<meta property="og:image:height" content="442" />';
        $og .= '<meta property="og:title" content="' . $album->getTitle() . '" />';
        $og .= '<meta property="og:description" content="' . $this->view->string()->striptags($album->getDescription()) . '" />';
        $og .= '<meta property="og:url" content="' . $this->finalizeUrl($album->getHref()) . '" />';
        $og .= '<meta property="og:updated_time" content="' . strtotime($album->modified_date) . '" />';
        $og .= '<meta property="og:type" content="website" />';
        $this->view->layout()->headIncludes .= $og;
	}

	private function finalizeUrl($url)
    {
        if ($url)
        {
            if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
            {
                $pageURL = 'http';
                if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
                {
                    $pageURL .= "s";
                }
                $pageURL .= "://";
                $pageURL .= $_SERVER["SERVER_NAME"];
                $url = $pageURL . '/'. ltrim( $url, '/');
            }
        }

        return $url;
    }

	public function deleteAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> getItem('advalbum_album', $this -> getRequest() -> getParam('album_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'delete') -> isValid())
			return;

		// print_r($album);die;
		$this -> view -> form = $form = new Advalbum_Form_Album_Delete();

		if (!$album)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Album doesn't exists or not authorized to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> delete();
			$db -> commit();
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Album has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'album_general', true),
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'messages' => Array($this -> view -> message)
		));
	}

	public function editphotosAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		// Prepare data
		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> max_color = Engine_Api::_()->getDbTable("settings", "core")->getSetting("advalbum.maxcolor", 1);
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage($paginator -> getTotalItemCount());

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Album_Photos();
		$this -> view -> colors = $colorAll = Engine_Api::_()->getDbTable("colors", "advalbum")->getColorArray();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// this variable contain all posted date, including cover value
		$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
//		echo '<pre>',print_r($p);die;
		//get array value of photos

		// this variable contain all data of photos in an array with photo id is the key
		$values = $p['advalbum_photo'];

		$table = $album -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			if (!empty($p['cover']))
			{
				$album -> photo_id = $p['cover'];
				$album -> save();
			}

			$viewer = Engine_Api::_()->user()->getViewer();
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$defaultTagParams = array('x'=>'10','y'=>'10','w'=>'48','h'=>'48');
			// Process
			foreach ($paginator as $photo)
			{
				if (!isset($values[$photo->getIdentity()]))
					continue;
				$photoValues = $values[$photo->getIdentity()];
				if (!empty($photoValues['day']) && !empty($photoValues['month']) && !empty($photoValues['year']))
					$photoValues['taken_date'] = $photoValues['year'] .'-'. $photoValues['month'] .'-'. $photoValues['day'];
				$photo -> setFromArray($photoValues);

				if (isset($photoValues['color']) && !empty($photoValues['color']))
				{
					$colorsArray = explode(',', $photoValues['color']);
					$photo -> saveColors($colorsArray);
				}

				if (isset($photoValues['name'])) {
					// process tag
					$currentTagIds = array();
					foreach ($photo->tags()->getTagMaps() as $tagmap)
					{
						$currentTagIds[] = $tagmap->tag_id;
					}

					$tagIds = explode(',', $photoValues['name']);
					
					$tagAddIds = array_diff($tagIds, $currentTagIds);
					$tagRemoveIds = array_diff($currentTagIds, $tagIds);
					
					foreach ($tagAddIds as $tagId) {
						$tag = Engine_Api::_()->getItem('user', $tagId);
						$tagmap = $photo->tags()->addTagMap($viewer, $tag, $defaultTagParams);

						if (is_null($tagmap))
							continue;
						
						if( $tag instanceof User_Model_User && !$photo->isOwner($tag) && !$viewer->isSelf($tag) )
						{
							// Add activity
							$action = $activityApi->addActivity(
								$viewer,
								$tag,
								'tagged',
								'',
								array(
									'label' => str_replace('_', ' ', $photo->getShortType())
								)
							);
							if( $action ) $action->attach($photo);

							// Add notification
							$type_name = $this->view->translate(str_replace('_', ' ', $photo->getShortType()));
							$notifyApi->addNotification(
								$tag,
								$viewer,
								$photo,
								'tagged',
								array(
									'object_type_name' => $type_name,
									'label'            => $type_name,
								)
							);
						}

					}

					foreach ($tagRemoveIds as $tagId) {
						$tag = Engine_Api::_()->getItem('user', $tagId);
						$tagmap = $photo->tags()->getTagMap($tag);
						$tagmap->delete();
					}
				}

				// end processing tags
				$photo -> save();
			}

			$db -> commit();
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'album_id' => $album -> album_id
		), 'album_specific', true);
	}

	public function loadMorePhotosAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		$currentPage = $this -> _getParam('page', 1);
		// Prepare data
		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$this -> view -> colors = $colorAll = Engine_Api::_()->getDbTable("colors", "advalbum")->getColorArray();
		$this->view->current_year = $currentYear = date('Y');
		$this->view->smallest_year = $currentYear - 99;
		$paginator -> setCurrentPageNumber($currentPage);
		$paginator -> setItemCountPerPage(9);
	}
	
	public function deletePhotosAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		// In smooth box
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();
		$photoIds = explode(',', $params['photo_ids']);

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Album_DeletePhotos();
		$form -> setDescription('Are you sure that you want to delete '.count($photoIds).' photo(s)?');

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		foreach ($photoIds as $photoId) {
			$photos = Engine_Api::_()->getItem('advalbum_photo', $photoId);
			if ($photos) {
				$photos->delete();
			}
		}

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Delete successfully'))
		));
	}
	
	public function movePhotosAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		// In smooth box
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();
		$photoIds = explode(',', $params['photo_ids']);

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Album_MovePhotos();
		
		// generate album list
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$currentAlbumId = $params['album_id']; 
		$albumTbl = Engine_Api::_()->getDbTable("albums", "advalbum");
		$select = $albumTbl->select();
		$select -> where('owner_id = ?', $viewer -> getIdentity())
			->where('`virtual` = ?', 0)
			->where('`album_id` <> ?', $currentAlbumId);
		$albums = $albumTbl->fetchAll($select);
		
		// we have other albums to move photos to

		if (count($albums)) {
			$albumIdElement = $form->getElement('album_id');
			$albumList = array();
			foreach ($albums as $album)
			{
				$albumList[$album->getIdentity()] = $album->getTitle();
			}
			$albumIdElement->addMultiOptions($albumList);
			$msg = $this->view->translate('Select the album to move %s photo(s) to', count($photoIds));
			$form -> setDescription($msg);
		} else {
			$form -> setDescription('You don\'t have any other albums to move these photo(s) to');
			$form->removeElement('album_id');
			$form->removeElement('submit');
			$form->removeElement('cancel');
			$form->addElement('Cancel', 'cancel',
				array(
					'label' => 'cancel',
					'link' => true,
					'href' => '',
					'onclick' => 'parent.Smoothbox.close();',
					'decorators' => array(
						'ViewHelper'
					)
				));
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$values = $form -> getValues();
		$targetAlbumId = $values['album_id'];

		foreach ($photoIds as $photoId) {
			$photos = Engine_Api::_()->getItem('advalbum_photo', $photoId);
			if ($photos) {
				$photos->album_id = $targetAlbumId;
				$photos->save();
			}
		}

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Move successfully'))
		));
	}

	protected function order($colorAll, $mainColors)
	{
		$temp = array();
		foreach ($mainColors  as $color)
		{
			$temp[$color] = $color;
		}
		foreach ($colorAll as $key => $value)
		{
			if (!in_array($key, $mainColors))
			{
				$temp[$key] = $value;
			}
		}
		return $temp;
	}
	
	public function composeUploadAction()
	{
		if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			$this -> _redirect('login');
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid method');
			return;
		}

		if (empty($_FILES['Filedata']))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
			return;
		}

		$max_fileSizes = Engine_Api::_()->getApi('settings', 'core')->getSetting('album_max_file_size', 0);
		if ($max_fileSizes && ($_FILES['Filedata']['size'] > $max_fileSizes * 1024)) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate')->_('File size must be smaller than') . ' ' . $max_fileSizes . ' KBs';
			return;
		}

		// Get album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$type = $this -> _getParam('type', 'wall');

			if (empty($type))
				$type = 'wall';

			$album = $table -> getSpecialAlbum($viewer, $type);

			$photo = Engine_Api::_() -> advalbum() -> createPhoto(array(
				'owner_type' => 'user',
				'owner_id' => Engine_Api::_() -> user() -> getViewer() -> getIdentity()
			), $_FILES['Filedata']);

			if ($type === 'message')
			{
				$photo -> title = Zend_Registry::get('Zend_Translate') -> _('Attached Image');
			}

			$photo -> album_id = $album -> album_id;
			$photo -> order = $photo -> photo_id;
			$photo -> save();
			$photo -> parseColor();

			if ($type === 'message')
			{
				Engine_Api::_() -> getApi('search', 'core') -> unindex($photo);
			}

			if (!$album -> photo_id)
			{
				$album -> photo_id = $photo -> getIdentity();
				$album -> save();
			}

			if ($type != 'message')
			{
				// Authorizations
				$auth = Engine_Api::_() -> authorization() -> context;
				$auth -> setAllowed($photo, 'everyone', 'view', true);
				$auth -> setAllowed($photo, 'everyone', 'comment', true);
			}

			$db -> commit();

			$this -> view -> status = true;
			$this -> view -> photo_id = $photo -> photo_id;
			$this -> view -> album_id = $album -> album_id;
			$this -> view -> src = $photo -> getPhotoUrl();
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Photo saved successfully');
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			// throw $e;
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
		}
	}

	public function exportSlideshowAction()
	{
		$server_array = explode ( "/", $_SERVER ['PHP_SELF'] );
		$server_array_mod = array_pop ( $server_array );
		if ($server_array [count ( $server_array ) - 1] == "admin") {
			$server_array_mod = array_pop ( $server_array );
		}
		$server_info = implode ( "/", $server_array );

		$selfURL =  "http://" . $_SERVER ['HTTP_HOST'] . $server_info . "/";
		$serverURL = "http://" . $_SERVER ['HTTP_HOST'];

		$mode = $this->_getParam('mode');
		// Get embed code
		$url = '';

		$albumId = $this -> getRequest() -> getParam('album_id');
		$url = $selfURL . 'albums/view/' . $albumId . '/slideshow/1/effect/kenburns';
		$title = 'Share slideshow';

		$this -> view -> title = $title;
		$this -> view -> htmlCode = '<iframe src="' . $url . '" width="720" frameborder="0" height="572"></iframe>';
	}
}
