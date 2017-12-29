<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     MinhNC
 */
class Ynforum_AnnouncementController extends Core_Controller_Action_Standard {

	public function init() 
	{
		if (0 !== ($forum_id = (int)$this -> _getParam('forum_id')) && null !== ($forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id)))
		{
			if (!Engine_Api::_() -> core() -> hasSubject($forum -> getType()))
			{
				Engine_Api::_() -> core() -> setSubject($forum);
			}
			$this -> view -> forum = $forum;
			$viewer = Engine_Api::_()->user()->getViewer();
			if(!$forum -> checkPermission($viewer ,'forum', 'ynannoun.edit'))
			{
				return $this -> _helper -> requireSubject -> forward();
			}
			$this -> view -> navigationForums = $forum -> getForumNavigations();
			$list = $forum -> getModeratorList();
			$moderators = $list -> getAllChildren();
			$arr_temp = array();
			foreach($moderators as $moderator)
			{
				if($moderator -> getIdentity())
					$arr_temp[] = $moderator;
			}
			$this -> view -> moderators = $arr_temp;
			
			$categoryTable = Engine_Api::_() -> getItemTable('ynforum_category');
			$cats = $categoryTable -> fetchAll($categoryTable -> select() -> order('order ASC'));
			$categories = array();
			foreach ($cats as $cat)
			{
				$categories[$cat -> getIdentity()] = $cat;
			}
			$curCat = $categories[$forum -> category_id];
			$linkedCategories = array();
			do
			{
				$linkedCategories[] = $curCat;
				if (!$curCat -> parent_category_id)
				{
					break;
				}
				$curCat = $categories[$curCat -> parent_category_id];
			}
			while (true);
			$this -> view -> linkedCategories = $linkedCategories;
		}
		else {
			return $this -> _helper -> requireSubject -> forward();
		}
	}
	public function manageAction()
	{
		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!$forum -> checkPermission($viewer ,'forum', 'ynannoun.edit'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this -> view -> forum = $forum;
		// get announcement
		$page = $this->_getParam('page', 1);
		$table = Engine_Api::_() -> getItemTable('ynforum_announcement');
		$select = $table -> select() -> where("forum_id = ?", $forum -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity());
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);
	}
	public function createAction()
  	{
  		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}
    	$this->view->form = $form = new Ynforum_Form_Announcement_Create();
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!$forum -> checkPermission($viewer ,'forum', 'ynannoun.create'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
	    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) 
	    {
	      $params = $form->getValues();
	      $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
		  $params['forum_id'] = $forum -> getIdentity();
	      $announcement = Engine_Api::_()->getDbtable('announcements', 'ynforum')->createRow();
	      $announcement->setFromArray($params);
	      $announcement->save();
	      return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'forum_id' => $forum -> getIdentity()));
	    }
  	}
	public function deleteAction() 
	{
		if (!$this -> _helper -> requireUser() -> isValid()) 
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum') -> isValid()) 
		{
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$id = $this->_getParam('announcement_id', null);
    	$announcement = Engine_Api::_()->getItem('ynforum_announcement', $id);
		if(!$announcement->isOwner($viewer) && !$forum -> checkPermission($viewer ,'forum', 'ynannoun.delete'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> form = $form = new Ynforum_Form_Announcement_Delete();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynforum_announcement');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try 
		{
			$announcement -> delete();
			$db -> commit();
			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array('Delete announcement successfully.')
				));
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function editAction() 
	{
		if (!$this -> _helper -> requireUser() -> isValid()) 
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum') -> isValid()) 
		{
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$id = $this->_getParam('announcement_id', null);
    	$announcement = Engine_Api::_()->getItem('ynforum_announcement', $id);
		if(!$announcement->isOwner($viewer) && !$forum -> checkPermission($viewer ,'forum', 'ynannoun.edit'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this->view->form = $form = new Ynforum_Form_Announcement_Edit();
		$form -> populate($announcement -> toArray());

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		// Process
		$table = Engine_Api::_() -> getItemTable('ynforum_announcement');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try 
		{
			$values = $form -> getValues();
			$announcement -> body = $values['body'];
			$announcement -> title = $values['title'];
			$announcement -> user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			$announcement -> save();
			$db -> commit();
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'forum_id' => $forum -> getIdentity()));
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}
	public function highlightAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid()) 
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum') -> isValid()) 
		{
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if (!$this -> getRequest() -> isPost())
			return;
		$id = $this -> getRequest() -> getPost('announcement_id', null);
    	$announcement = Engine_Api::_()->getItem('ynforum_announcement', $id);
		if(!$announcement->isOwner($viewer) && !$forum -> checkPermission($viewer ,'forum', 'ynannoun.hlight'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$db = Engine_Api::_() -> getDbTable('announcements', 'ynforum') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$announcement -> setProfile();
			$db -> commit();
			$this -> view -> success = true;
			$this -> view -> enabled = $announcement -> highlight;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
		}
	}
}
