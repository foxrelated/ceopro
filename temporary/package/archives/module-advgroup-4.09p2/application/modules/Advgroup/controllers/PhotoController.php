<?php
class Advgroup_PhotoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('advgroup_photo', $photo_id)))
			{
				Engine_Api::_() -> core() -> setSubject($photo);
			}
			elseif (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}

		$this -> _helper -> requireUser -> addActionRequires(array(
			'upload',
			'upload-photo', // Not sure if this is the right
			'edit',
		));

		$this -> _helper -> requireSubject -> setActionRequireTypes(array(
			'list' => 'group',
			'upload' => 'group',
			'view' => 'advgroup_photo',
			'edit' => 'advgroup_photo',
		));
	}

	/**
	 * @method
	 * @param
	 */
	public function listAction()
	{
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $group -> getSingletonAlbum();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($group -> is_subgroup)
		{
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view'))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
			elseif (!$group -> authorization() -> isAllowed($viewer, 'view'))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}
		elseif (!$group -> authorization() -> isAllowed($viewer, 'view'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		/**
		 * TODO: some thing lese.
		 */
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		//		if ($group -> is_subgroup) {
		//			$parent_group = $group -> getParentGroup();
		//			if ($parent_group -> authorization() -> isAllowed(null, 'photo')) {
		//				$canUpload = $group -> authorization() -> isAllowed(null, 'photo');
		//			} else {
		//				$canUpload = false;
		//			}
		//		} else {
		$canUpload = $group -> authorization() -> isAllowed(null, 'photo');
		//		}
		$levelUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'photo');

		if ($canUpload && $levelUpload)
		{
			$this -> view -> canUpload = true;
		}
		else
		{
			$this -> view -> canUpload = false;
		}
	}

	public function viewAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $photo -> getCollection();
		$this -> view -> group = $group = $photo -> getGroup();
		$this -> view -> canEdit = $photo -> canEdit(Engine_Api::_() -> user() -> getViewer());

		if ($group -> is_subgroup)
		{
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view'))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
			elseif (!$group -> authorization() -> isAllowed($viewer, 'view'))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}
		elseif (!$group -> authorization() -> isAllowed($viewer, 'view'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		if (!$viewer || !$viewer -> getIdentity() || $photo -> user_id != $viewer -> getIdentity())
		{
			$photo -> view_count = new Zend_Db_Expr('view_count + 1');
			$photo -> save();
		}
	}

	public function uploadAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$group = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'photo') -> isValid())
		{
			return;
		}

		$canUpload = $group -> authorization() -> isAllowed(null, 'photo');
		$levelUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'photo');
		if (!$canUpload || !$levelUpload)
		{
			$this -> renderScript("_error.tpl");
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$album_id = $this -> _getParam('album_id'))
			$album = $group -> getSingletonAlbum();
		else
			$album = Engine_Api::_() -> getItem('advgroup_album', $album_id);
		$max = Engine_Api::_() -> advgroup() -> getNumberValue('group', $viewer -> level_id, 'numberPhoto');
		if ($max > 0 && $album -> getPhotoCount($viewer -> getIdentity()) >= $max)
		{
			$this -> renderScript('/photo/max.tpl');
			return;
		}
		$this -> view -> album = $album;
		$this -> view -> group = $group;
		$this -> view -> form = $form = new Advgroup_Form_Photo_Upload();
		$session = new Zend_Session_Namespace('mobile');
		if (!$session -> mobile)
		{
			$form -> group_id -> setValue($group -> getIdentity());
			$form -> album_id -> setValue($album -> getIdentity());
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('advgroup_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$params = array(
				'group_id' => $group -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
			);

			// mobile upload photos
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
						$temp_file = array(
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'name' => $files['name'][$key]
						);
						$photoTable = Engine_Api::_() -> getItemTable('advgroup_photo');
						$photo = $photoTable -> createRow();
						$photo -> setFromArray($params);
						$photo -> save();

						$photo -> setPhoto($temp_file);

						$arr_photo_id[] = $photo -> getIdentity();
					}

					catch ( Exception $e )
					{
						throw $e;
						return;
					}
				}
			}
			$values = $form -> getValues();
			if (isset($values['html5uploadfileids']))
			{
				$values['file'] = explode(' ', trim($values['html5uploadfileids']));
			}
			if ($arr_photo_id)
			{
				$values['file'] = $arr_photo_id;
			}
			// Add action and attachments
			$api = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $group, 'advgroup_photo_upload', null, array('count' => count($values['file'])));

			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id)
			{
				
				if (!$photo_id)
					continue;
				$photo = Engine_Api::_() -> getItem("advgroup_photo", $photo_id);
				
				
				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;
				$photo -> collection_id = $album -> album_id;
				$photo -> album_id = $album -> album_id;
				$photo -> group_id = $group -> group_id;
				$photo -> save();

				if ($action instanceof Activity_Model_Action && $count < 8)
				{
					$api -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> _redirectCustom($album);
	}

	public function uploadPhotoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}
		$group = Engine_Api::_() -> getItem('group', $_POST['group_id']);
		if (!$group -> authorization() -> isAllowed(null, 'photo'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// @todo check auth
		//$group

		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $error
					)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if (!$album_id = $_POST['album_id'])
			{
				$album = $group -> getSingletonAlbum();
			}
			else
				$album = Engine_Api::_() -> getItem('advgroup_album', $album_id);

			$params = array(
				// We can set them now since only one album is allowed
				'user_id' => $viewer -> getIdentity(), );

			$photoTable = Engine_Api::_() -> getItemTable('advgroup_photo');
			$photo = $photoTable -> createRow();
			$photo -> setFromArray($params);
			$photo -> save();

			$temp_file = array(
				'type' => $_FILES['files']['type'][0],
				'tmp_name' => $_FILES['files']['tmp_name'][0],
				'name' => $_FILES['files']['name'][0]
			);

			$photo -> setPhoto($temp_file);
			$db -> commit();
			
			$status = true;
			$name = $_FILES['files']['name'][0];
			$photo_id = $photo -> photo_id;
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $name,
						'photo_id' => $photo_id
					)))));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}
	}

	public function editAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();
		$group = $photo -> getParent('group');
		$viewer = Engine_Api::_()->user()->getViewer();

		//Check edit authorization
		$canEdit = $group -> authorization() -> isAllowed(null, 'photo.edit');

		if (!$canEdit && !$photo -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentParent($viewer))
		{
			return $this -> renderScript('_private.tpl');
		}

		$this -> view -> form = $form = new Advgroup_Form_Photo_Edit();

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> setFromArray($form -> getValues()) -> save();

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Changes saved')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
			'closeSmoothbox' => true,
		));
	}

	public function deleteAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();
		$album = $photo -> getCollection();
		$group = $photo -> getParent('group');
		$viewer = Engine_Api::_()->user()->getViewer();

		//Check edit authorization
		$canEdit = $group -> authorization() -> isAllowed(null, 'photo.edit');

		if (!$canEdit && !$photo -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentParent($viewer))
		{
			return $this -> renderScript('_private.tpl');
		}

		$this -> view -> form = $form = new Advgroup_Form_Photo_Delete();

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Photo deleted')),
			'layout' => 'default-simple',
			'parentRedirect' => $album -> getHref(),
			'closeSmoothbox' => true,
		));
	}

	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('advgroup_photo', $this -> getRequest() -> getParam('photo_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($photo, null, 'edit') -> isValid())
		{
			return;
		}
		if (!$photo)
		{
			$translate = Zend_Registry::get("Zend_Translate");
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}
	public function setSlideshowAction()
	{
		$photo_id = $this ->_getParam('photo_id');
		$photo = Engine_Api::_() -> getItem('advgroup_photo', $photo_id);
		
		
		$photo->is_featured = !$photo->is_featured;
		$photo->save();
		
		$arr =  array('photo_id' => $photo->getIdentity(), 'status' => $photo->is_featured);
		
		echo Zend_Json::encode($arr);
		exit();
	}

}
