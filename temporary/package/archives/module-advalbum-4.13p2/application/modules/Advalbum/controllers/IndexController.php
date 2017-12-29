<?php
class Advalbum_IndexController extends Core_Controller_Action_Standard
{

	public function browseAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'view') -> isValid())
			return;
		$subject = Engine_Api::_() -> getItem('advalbum_param', 1);
		$search = $sort = $category_id = null;
		$search = $this -> getRequest() -> getPost('search');
		$sort = $this -> getRequest() -> getPost('sort');
		$category_id = $this -> getRequest() -> getPost('category_id');

		$subject -> search = $search;
		$subject -> category_id = $category_id;
		$subject -> sort = $sort;
		$subject -> save();
		if ($search != '' || $sort != '' || $category_id != '')
		{
			$this -> _redirect('albums/listing');
		}
		else
		{
			$obj = new Advalbum_Api_Core();
			if ($obj -> checkVersionSE())
			{
				$this -> _helper -> content -> setNoRender() -> setEnabled();
			}
			else
				$this -> _helper -> content -> render();
		}
	}

	public function browsebyuserAction()
	{
		$id = $this -> _getParam('id');
		$page = $this -> _getParam('page');
		if ($id == "" || $id == 0)
		{
			$id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		}
		// get your photos
		$table = Engine_Api::_() -> getItemTable('advalbum_album');
		$select = $table -> select() -> where("search = 1") -> where('owner_id = ?', $id) -> order('album_id DESC');
		$user = Engine_Api::_() -> getItem('user', $id);
		$this -> view -> user = $user;
		$this -> view -> id = $id;
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('album_page', 24);
		$paginator = $this -> view -> paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($limit);
		$paginator -> setCurrentPageNumber($page);
		// get photos of you
		$table = Engine_Api::_() -> getItemTable('advalbum_photo');
		$select = $table -> select() -> from('engine4_album_photos') -> join('engine4_core_tagmaps', 'engine4_album_photos.photo_id = engine4_core_tagmaps.resource_id', '') -> where('tag_id = ?', $id) -> where('resource_type = ?', 'advalbum_photo') -> where('tag_type = ?', 'user') -> order('photo_id DESC') -> limit(15);
		$paginatorPhotos = $this -> view -> paginatorPhotos = Zend_Paginator::factory($select);
		$paginatorPhotos -> setItemCountPerPage($limit);
	}

	public function tagphotouserAction()
	{
		$id = $this -> _getParam('id');
		$page = $this -> _getParam('page');
		if ($id == "" || $id == 0)
		{
			$id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		}
		// get photos of you
		$table = Engine_Api::_() -> getItemTable('advalbum_photo');
		$select = $table -> select() -> from('engine4_album_photos') -> join('engine4_core_tagmaps', 'engine4_album_photos.photo_id = engine4_core_tagmaps.resource_id', '') -> where('tag_id = ?', $id) -> where('resource_type = ?', 'advalbum_photo') -> where('tag_type = ?', 'user') -> order('photo_id DESC');
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('album_page', 24);
		$paginatorPhotos = $this -> view -> paginatorPhotos = Zend_Paginator::factory($select);
		$paginatorPhotos -> setItemCountPerPage($limit);
		$paginatorPhotos -> setCurrentPageNumber($page);
		$user = Engine_Api::_() -> getItem('user', $id);
		$this -> view -> user = $user;
		$this -> view -> id = $id;
	}

	public function listingAction()
	{
		$params = $this->_getAllParams(); 
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'view') -> isValid())
			return;
		if ($this -> _getParam('category_id') != '' || $this -> _getParam('page') != '' || $this -> _getParam('sort') != '')
		{
			$subject = Engine_Api::_() -> getItem('advalbum_param', 1);
			$subject -> search = '';
			$subject -> category_id = $this -> _getParam('category_id');
			$subject -> page = $this -> _getParam('page');
			$subject -> sort = $this -> _getParam('sort');
			$subject -> save();
		}
		$obj = new Advalbum_Api_Core();
		if ($obj -> checkVersionSE())
		{
			$this -> _helper -> content -> setNoRender() -> setEnabled();
		}
		else
			$this -> _helper -> content -> render();
	}
	
	public function listingPhotoAction()
	{
		$params = $this->_getAllParams();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'view') -> isValid())
			return;
		if ($this -> _getParam('category_id') != '' || $this -> _getParam('page') != '' || $this -> _getParam('sort') != '')
		{
			$subject = Engine_Api::_() -> getItem('advalbum_param', 1);
			$subject -> search = '';
			$subject -> category_id = $this -> _getParam('category_id');
			$subject -> page = $this -> _getParam('page');
			$subject -> sort = $this -> _getParam('sort');
			$subject -> save();
		}
		$obj = new Advalbum_Api_Core();
		if ($obj -> checkVersionSE())
		{
			$this -> _helper -> content -> setNoRender() -> setEnabled();
		}
		else
			$this -> _helper -> content -> render();
	}
	
	public function manageAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		// Render
		$this->_helper->content->setNoRender()->setEnabled();
	}

	public function uploadAction()
	{
		// upload photos with mobile
		$session = new Zend_Session_Namespace('mobile');
		$arr_photo_id = array();
		if ($session -> mobile && !empty($_FILES['photos']))
		{
			$files = $_FILES['photos'];
			foreach ($files['name'] as $key => $value)
			{
				$type = explode('/', $files['type'][$key]);
				if ($type[0] != 'image' || !is_uploaded_file($files['tmp_name'][$key]))
				{
					continue;
				}
				try
				{
					$viewer = Engine_Api::_() -> user() -> getViewer();
					$params = array(
						'owner_type' => 'user',
						'owner_id' => $viewer -> getIdentity(),
					);
					$temp_file = array(
						'type' => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'name' => $files['name'][$key]
					);
					$photo_id = Engine_Api::_() -> advalbum() -> createPhoto($params, $temp_file) -> photo_id;
					$arr_photo_id[] = $photo_id;
				}

				catch ( Exception $e )
				{
					throw $e;
					return;
				}
			}
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'create') -> isValid())
			return;

		// Render
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Get form
		$addPhoto = FALSE;
		
		if($this -> _getParam('album_id'))
		{
			$album = Engine_Api::_() -> getItem('advalbum_album', $this -> _getParam('album_id'));
			$can_add_photo = $this -> _helper -> requireAuth() -> setAuthParams($album, null, 'addphoto') -> checkRequire();
			if($can_add_photo && !$album -> getOwner() -> isSelf($viewer))
			{
				$addPhoto = true;
			}
		}
		
		$this -> view -> form = $form = new Advalbum_Form_Album(array('addPhoto' => $addPhoto,'albumId' => $this -> _getParam('album_id')));
		
		if (!$this -> getRequest() -> isPost())
		{
			if (null !== ($album_id = $this -> _getParam('album_id')))
			{
				$form -> populate(array('album' => $album_id));
			}
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$db = Engine_Api::_() -> getItemTable('advalbum_album') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album = $form -> saveValues($arr_photo_id);
			$db -> commit();
		}
		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		if($addPhoto)
		{
			$this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'album_id' => $album -> album_id
			), 'album_specific', true);
		}
		else {
			$this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'editphotos',
			'album_id' => $album -> album_id
			), 'album_specific', true);
		}
	}

	public function uploadPhotoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'create') -> isValid())
			return;

		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}

		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

		$max_fileSizes = Engine_Api::_()->getApi('settings', 'core')->getSetting('album_max_file_size', 0);
		if ($max_fileSizes && ($_FILES['files']['size'][0] > $max_fileSizes * 1024)) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate')->_('File size must be smaller than') . ' ' . $max_fileSizes . ' KBs';
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}
		$db = Engine_Api::_() -> getDbtable('photos', 'advalbum') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();

			$params = array(
				'owner_type' => 'user',
				'owner_id' => $viewer -> getIdentity()
			);
			$temp_file = array(
						'type' => $_FILES['files']['type'][0],
						'tmp_name' => $_FILES['files']['tmp_name'][0],
						'name' => $_FILES['files']['name'][0]
					);
			$photo = Engine_Api::_() -> advalbum() -> createPhoto($params, $temp_file);
			$photo_id = $photo  -> photo_id;

			$status = true;
			$name = $_FILES['files']['name'][0];
			$photo_id = $photo_id;
			$db -> commit();
			$photo -> parseColor();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $name, 'photo_id' => $photo_id)))));
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

	}

	/*
	 * process AddTo menu for each photo
	 */
	public function addToAction()
	{
		$this -> _helper -> layout -> disableLayout();

		$values = $this -> getRequest() -> getParams();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// only turn on some options if user is album owner
		$is_login = true;
		if (isset($values['photo_id']))
		{
			$this -> view -> photo_id = $values['photo_id'];
		}
		$album = Engine_Api::_() -> getItem('advalbum_album', $values['album_id']);
		if (!$album)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if (!$album -> isOwner($viewer))
		{
			$is_login = false;
		}
		$userVirtualAlbums = Engine_Api::_()->getDbTable("albums", "advalbum")->getVirtualAlbumsAssoc($viewer);
		$this -> view -> has_virtual_album = (count($userVirtualAlbums)) ? true : false;
		$this -> view -> album_id = $values['album_id'];
		$this -> view -> is_login = $is_login;
		$this -> view -> is_virtual = $album->virtual;
	}

	public function rateAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		$values = $this -> getRequest() -> getParams();
		if (!isset($values['subject_id']) || !is_numeric($values['subject_id']))
		{
			return;
		}

		$subject_id = (int)$this -> _getParam('subject_id');
		if (isset($values['is_album']))
		{
			$subject = Engine_Api::_() -> getItem('advalbum_album', $subject_id);
			$type = Advalbum_Plugin_Constants::RATING_TYPE_ALBUM;
		}
		else
		{
			$subject = Engine_Api::_() -> getItem('advalbum_photo', $subject_id);
			$type = Advalbum_Plugin_Constants::RATING_TYPE_PHOTO;
		}
		if (!$subject)
		{
			return;
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams($subject, null, 'view') -> isValid())
		{
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();
		$rating = (int)$values['rating'];
		$ratingTbl = Engine_Api::_() -> getDbtable('ratings', 'advalbum');
		// save to rating table
		Engine_Api::_() -> advalbum() -> setRating($subject_id, $user_id, $rating, $type);

		$subject -> rating = Engine_Api::_() -> advalbum() -> getRating($subject_id, $type);
		$subject -> save();
		$total = Engine_Api::_() -> advalbum() -> countRating($subject_id, $type);

		$data = array(
			'total' => $total,
			'rating' => $subject -> rating,
		);
		return $this -> _helper -> json($data);
	}
	
	public function createVirtualAlbumAction()
	{
		$this -> _helper -> content -> setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_main', array(), 'advalbum_add_virtualalbum');

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Album_Add();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
			return;
		}

		// Process
		$viewer = Engine_Api::_()->user()->getViewer();
		$albumTbl = Engine_Api::_()->getDbTable("albums", "advalbum");
		$album = $albumTbl->createRow();
		$db = $albumTbl -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			$values['virtual'] = 1;
			$values['owner_type'] = 'user';
			$values['owner_id'] = $viewer->getIdentity();
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

			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone'
			);
			if ($values['auth_comment'])
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($values['auth_comment'], $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
			}

			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone'
			);
			if ($values['auth_tag'])
				$auth_tag = $values['auth_tag'];
			else
				$auth_tag = "everyone";
			$tagMax = array_search($values['auth_tag'], $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
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

	public function suggestTagAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer->getIdentity() ) {
			$data = null;
		} else {
			$data = array();
			$table = Engine_Api::_()->getItemTable('user');

			$usersAllowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $viewer->level_id, 'auth');

			if( (bool)$this->_getParam('message') && $usersAllowed == "everyone" ) {
				$select = Engine_Api::_()->getDbtable('users', 'user')->select();
				$select->where('user_id <> ?', $viewer->user_id);
			}
			else {
				$select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();
			}

			if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
				if (strpos($viewer->getTitle(), $text) !== false) {
			$data[] = array(
				'type' => 'user',
				'id' => $viewer->getIdentity(),
				'guid' => $viewer->getGuid(),
				'label' => $viewer->getTitle(),
				'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
				'url' => $viewer->getHref(),
			);
				}
			}

			if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
				$select->limit($limit);
			}

			if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
				$select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
			}

			$ids = array();
			foreach( $select->getTable()->fetchAll($select) as $friend ) {
				$data[] = array(
					'type'  => 'user',
					'id'    => $friend->getIdentity(),
					'guid'  => $friend->getGuid(),
					'label' => $friend->getTitle(),
					'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
					'url'   => $friend->getHref(),
				);
				$ids[] = $friend->getIdentity();
				$friend_data[$friend->getIdentity()] = $friend->getTitle();
			}

			// first get friend lists created by the user
			$listTable = Engine_Api::_()->getItemTable('user_list');
			$lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
			$listIds = array();
			foreach( $lists as $list ) {
				$listIds[] = $list->list_id;
				$listArray[$list->list_id] = $list->title;
			}

			// check if user has friend lists
			if( $listIds ) {
				// get list of friend list + friends in the list
				$listItemTable = Engine_Api::_()->getItemTable('user_list_item');
				$uName = Engine_Api::_()->getDbtable('users', 'user')->info('name');
				$iName  = $listItemTable->info('name');

				$listItemSelect = $listItemTable->select()
					->setIntegrityCheck(false)
					->from($iName, array($iName.'.listitem_id', $iName.'.list_id', $iName.'.child_id',$uName.'.displayname'))
					->joinLeft($uName, "$iName.child_id = $uName.user_id")
					//->group("$iName.child_id")
					->where('list_id IN(?)', $listIds);

				$listItems = $listItemTable->fetchAll($listItemSelect);

				$listsByUser = array();
				foreach( $listItems as $listItem ) {
					$listsByUser[$listItem->list_id][$listItem->user_id]= $listItem->displayname ;
				}

				foreach ($listArray as $key => $value){
					if (!empty($listsByUser[$key])){
						$data[] = array(
							'type' => 'list',
							'friends' => $listsByUser[$key],
							'label' => $value,
						);
					}
				}
			}
		}

		if( $this->_getParam('sendNow', true) ) {
			return $this->_helper->json($data);
		} else {
			$this->_helper->viewRenderer->setNoRender(true);
			$data = Zend_Json::encode($data);
			$this->getResponse()->setBody($data);
		}
	}
}
