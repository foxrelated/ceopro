<?php
class Advgroup_AlbumController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($album_id = (int)$this -> _getParam('album_id')) && null !== ($album = Engine_Api::_() -> getItem('advgroup_album', $album_id)))
			{
				Engine_Api::_() -> core() -> setSubject($album);
			}
			else
			if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
	}

	public function createAction()
	{
		//Check viewer and subject requirement
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity())
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		if (!Engine_Api::_() -> core() -> hasSubject('group'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$group = Engine_Api::_() -> core() -> getSubject();
		$albumFeature = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.albumFeature', 1);
		if ($albumFeature == 0)
		{
			$this -> renderScript("_error.tpl");
			return;
		}

		$canUpload = $group -> authorization() -> isAllowed(null, 'photo');

		//Check Photo Album Create Authorization
		$levelPhotoUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'photo');
		$levelAlbumUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'album');

		if (!$levelPhotoUpload)
			$levelUpload = false;
		else
		{
			if (!$levelAlbumUpload)
				$levelUpload = false;
			else
				$levelUpload = true;
		}

		//Check Full Upload Authorization:
		if (!$canUpload || !$levelUpload)
		{
			$this -> renderScript("_error.tpl");
			return;
		}

		//		$max = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'numberAlbum');
		$max = Engine_Api::_() -> advgroup() -> getNumberValue('group', $viewer -> level_id, 'numberAlbum');
		if ($max > 0 && $group -> getAlbumCount($viewer -> getIdentity()) >= $max)
		{
			$this -> renderScript('/album/max.tpl');
			return;
		}
		$this -> view -> form = $form = new Advgroup_Form_Album_Create();

		//Return if no right to upload photo
		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'photo') -> isValid())
		{
			return;
		}
		//Return if no post action
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		//Return if invalid input found
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		$table = Engine_Api::_() -> getItemTable('advgroup_album');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$album = $table -> createRow();
			$album -> group_id = $group -> group_id;
			$album -> user_id = $viewer -> user_id;
			$album -> title = $values['title'];
			$album -> description = $values['description'];

			$album -> save();
			$db -> commit();
			$this -> _helper -> redirector -> gotoRoute(array(
				'controller' => 'photo',
				'action' => 'upload',
				'subject' => $group -> getGuid(),
				'album_id' => $album -> album_id
			), 'group_extended', true);
		}
		catch(Exception $e)
		{
			$db -> rollBack();
			throw ($e);
		}
	}

	public function viewAction()
	{
		$albumFeature = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.albumFeature', 1);
		if ($albumFeature == 0)
		{
			$this -> renderScript("_error.tpl");
			return;
		}
		$this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		$params = $this -> _getAllParams();

		$this -> view -> group = $group = Engine_Api::_() -> getItem('group', $params['group_id']);
		$this -> view -> album = $album = Engine_Api::_() -> getItem('advgroup_album', $params['album_id']);
		
		if(!$album || !$group)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		
		if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer))
		{
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
			else
			if (!$group -> authorization() -> isAllowed($viewer, "view"))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}
		else
		if (!$group -> authorization() -> isAllowed($viewer, 'view'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		if ($album -> user_id != 0)
			$album_owner_id = $album -> user_id;
		else
			$album_owner_id = $group -> user_id;

		if ($viewer -> getIdentity() == 0)
		{
			$canEdit = false;
		}
		else
		{
			if ($viewer -> isAdmin() || $viewer -> getIdentity() == $album_owner_id || $group -> isOwner($viewer) || $group -> isParentGroupOwner($viewer))
			{
				$canEdit = true;
			}
			else
			{
				$canEdit = false;
			}
		}
		$this -> view -> canEdit = $canEdit;
		
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 24));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
	}

	public function editAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$albumFeature = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.albumFeature', 1);
		if ($albumFeature == 0)
		{
			$this -> renderScript("_error.tpl");
			return;
		}
		$values = $this -> _getAllParams();
		$group = Engine_Api::_() -> getItem('group', $values['group_id']);
		$album = Engine_Api::_() -> getItem('advgroup_album', $values['album_id']);
		$this -> view -> form = $form = new Advgroup_Form_Album_Edit();

		if ($album -> user_id != 0)
			$album_owner_id = $album -> user_id;
		else
			$album_owner_id = $group -> user_id;

		if ($viewer -> getIdentity() == 0)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		else
		{
			if (!$viewer -> isAdmin() && $viewer -> getIdentity() != $album_owner_id && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate(array(
				'album_id' => $album -> album_id,
				'title' => $album -> title,
				'description' => $album -> description,
			));
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('albums', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> setFromArray($form -> getValues()) -> save();
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

	public function listAction()
	{
		//Check album mode enable( don't use anymore)
		$albumFeature = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.albumFeature', 1);
		if ($albumFeature == 0)
		{
			$this -> renderScript("_error.tpl");
			return;
		}

		//Get Viewer, Group and Search Form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_Album_Search;

		if ($viewer -> getIdentity() == 0)
			$form -> removeElement('view');

		if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer))
		{
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
			else
			if (!$group -> authorization() -> isAllowed($viewer, "view"))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}
		else
		if (!$group -> authorization() -> isAllowed($viewer, 'view'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		//Get search condition
		$params = array();
		$params['group_id'] = $group -> getIdentity();
		$params['user_id'] = null;
		$params['search'] = $this -> _getParam('search', '');
		$params['view'] = $this -> _getParam('view', 0);
		$params['order'] = $this -> _getParam('order', 'recent');
		if ($params['view'] == 1)
		{
			$params['user_id'] = $viewer -> getIdentity();
		}
		//Populate Search Form
		$form -> populate(array(
			'search' => $params['search'],
			'view' => $params['view'],
			'order' => $params['order'],
			'page' => $this -> _getParam('page', 1)
		));
		$this -> view -> formValues = $form -> getValues();

		//Get Album paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_album') -> getAlbumsPaginator($params);
	
		
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 20));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		//Check Photo Album Create Authorization
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
		$levelPhotoUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'photo');
		$levelAlbumUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'album');

		if (!$levelPhotoUpload)
			$levelUpload = false;
		else
		{
			if (!$levelAlbumUpload)
				$levelUpload = false;
			else
				$levelUpload = true;
		}

		//Check Full Upload Authorization:
		if ($canUpload && $levelUpload)
		{
			$this -> view -> canUpload = true;
		}
		else
		{
			$this -> view -> canUpload = false;
		}
	}

	public function deleteAction()
	{
		$albumFeature = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.albumFeature', 1);
		if ($albumFeature == 0)
		{
			$this -> renderScript("_error.tpl");
			return;
		}
		$params = $this -> _getAllParams();
		$group = Engine_Api::_() -> getItem('group', $params['group_id']);
		$album = Engine_Api::_() -> getItem('advgroup_album', $params['album_id']);
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($album -> user_id != 0)
		{
			$album_owner_id = $album -> user_id;
		}
		else
		{
			$album_owner_id = $group -> user_id;
		}

		$this -> view -> form = $form = new Advgroup_Form_Album_Delete();

		if ($viewer -> getIdentity() == 0)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		else
		{
			if (!$viewer -> isAdmin() && $viewer -> getIdentity() != $album_owner_id && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$db = Engine_Api::_() -> getDbtable('albums', 'advgroup') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$album -> delete();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Album deleted')),
			'layout' => 'default-simple',
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'controller' => 'album',
				'action' => 'list',
				'subject' => $group -> getGuid(),
				'album_id' => $album -> getIdentity()
			), 'group_extended', true),
			'closeSmoothbox' => true,
		));
	}

}
?>
