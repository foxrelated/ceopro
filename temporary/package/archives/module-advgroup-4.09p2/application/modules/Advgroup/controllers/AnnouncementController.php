<?php
class Advgroup_AnnouncementController extends Core_Controller_Action_Standard {
   public function manageAction()
  {
  	
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->group = $group = Engine_Api::_()->getItem('group', $this->_getParam("group_id"));
   
   	$canManage = $group -> authorization() -> isAllowed(null, 'announcement');
		$allow_manage = "";
		$levelManage = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'announcement');
		if ($canManage && $levelManage) {
			$allow_manage = true;
		} else {
			$allow_manage = false;
	}
   
    if(!$viewer || (!$group->isOwner($viewer) && !$allow_manage)) {
      $this->renderScript("_error.tpl");
      return;
    }
    $this->view->formFilter = $formFilter = new Advgroup_Form_Announcement_Filter();
    $page = $this->_getParam('page', 1);
    $table = Engine_Api::_()->getItemtable('advgroup_announcement');
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
      $select = $table->select()->where('group_id = ?',$group->group_id)->order( !empty($values['orderby']) ? $values['orderby'].' '.$values['orderby_direction'] : 'announcement_id DESC' )->limit(10);
      $paginator = Zend_Paginator::factory($select);
      if ($values['orderby'] && $values['orderby_direction'] != 'DESC') {
        $this->view->orderby = $values['orderby'];
      }
    } else {
       $select = $table->select()->order( 'announcement_id DESC' )->limit(10);
       $paginator = Zend_Paginator::factory($select);
    }

    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }

 public function createAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->_getParam("group_id"));
    $allow_manage = Engine_Api::_()->authorization()->getAdapter("levels")->getAllowed('group', $viewer, 'announcement');
    if(!$viewer || (!$group->isOwner($viewer) && !$allow_manage && !$group->isParentGroupOwner($viewer))) {
      $this->renderScript("_error.tpl");
      return;
    }
    $this->view->form = $form = new Advgroup_Form_Announcement_Create();
    $group_id = $this->_getParam('group_id');
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $params = $form->getValues();
      $params['group_id'] = $this->_getParam('group_id');
      $announcement = Engine_Api::_()->getDbtable('announcements', 'advgroup')->createRow();
      $announcement->setFromArray($params);
      $announcement->save();

      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  public function editAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->_getParam("group_id"));
    $allow_manage = Engine_Api::_()->authorization()->getAdapter("levels")->getAllowed('group', $viewer, 'announcement');
    if(!$viewer || (!$group->isOwner($viewer) && !$allow_manage && !$group->isParentGroupOwner($viewer))) {
      $this->renderScript("_error.tpl");
      return;
    }
    $id = $this->_getParam('id', null);
    $announcement = Engine_Api::_()->getItem('advgroup_announcement', $id);

    $this->view->form = $form = new Advgroup_Form_Announcement_Edit();
    $form->populate($announcement->toArray());

    // Save values
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $params = $form->getValues();
      $announcement->setFromArray($params);
      $announcement->save();
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  public function deleteAction()
  {
    //$this->view->form = $form = new Announcement_Form_Admin_Edit();
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->_getParam("group_id"));
    $allow_manage = Engine_Api::_()->authorization()->getAdapter("levels")->getAllowed('group', $viewer, 'announcement');
    if(!$viewer || (!$group->isOwner($viewer)&& !$allow_manage && !$group->isParentGroupOwner($viewer))) {
      $this->renderScript("_error.tpl");
      return;
    }
    $this->view->id = $id = $this->_getParam('id', null);
    $announcement = Engine_Api::_()->getItem('advgroup_announcement', $id);

    // Save values
    if( $this->getRequest()->isPost() )
    {
      $announcement->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Announcement is deleted successfully.')
      ));
    }
  }

  public function deleteselectedAction()
  {
    //$this->view->form = $form = new Announcement_Form_Admin_Edit();
    $viewer = Engine_Api::_()->user()->getViewer();
    $group = Engine_Api::_()->getItem('group', $this->_getParam("group_id"));
    if(!$viewer || (!$group->isOwner($viewer) && !$viewer->isAdmin() && !$group->isParentGroupOwner($viewer))) {
      $this->renderScript("_error.tpl");
      return;
    }
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $announcement = Engine_Api::_()->getItem('advgroup_announcement', $id);
        if( $announcement ) $announcement->delete();
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }
  
  public function markAction()
	{	    // In smoothbox
	
    $this->_helper->layout->setLayout('default-simple');  
    $this->view->form = $form = new Advgroup_Form_Announcement_Mark();
		
	if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
	
	$params = $this -> _getAllParams();
	$result = Engine_Api::_() -> getDbtable('marks', 'advgroup') -> markAnnouncement($params);
	if($result != "true")
	{
		die($result);
	}
	
	$group = Engine_Api::_() -> getItem('group', $params['group_id']);
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Announcement has beed marked as read')),
					'layout' => 'default-simple',
					'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
						'controller' => 'profile',
						'action' => 'index',
						'id' => $group -> getIdentity(),
					), 'group_profile', true),
					'closeSmoothbox' => true,
	));
	
	}
}
?>
