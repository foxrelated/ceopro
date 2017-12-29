<?php 
class Advgroup_InviteManageController extends Core_Controller_Action_Standard {
  
	public function init()
  {
    $group_id = (int)$this -> _getParam('group_id');
    $group = Engine_Api::_() -> getItem('group', $group_id);
		if ($group)
    {
			Engine_Api::_() -> core() -> setSubject($group);
		}

    // Checking user log-in and group existance
		if (!$this -> _helper -> requireUser() -> isValid()) return;
		if (!$this -> _helper -> requireSubject('group') -> isValid()) return;
	}
  
	public function manageAction()
  {
    
		$viewer = Engine_Api::_()->user()->getViewer();	
		$this->view->group = $group = Engine_Api::_()->core()->getSubject('group');
		
		// Checking invitation manage permission
		 if(!$viewer->isAdmin() && !$group->isOwner($viewer)  &&!$group->isParentGroupOwner($viewer) ) {
	      $this->renderScript("_error.tpl");
	      return;
	    }

		// Get waiting/un-responsed/ignored members and other properties
		$this->view->waitingMembers = $paginator = Zend_Paginator::factory($group->membership()->getInvitedMembers());
		$this->view->page = $page = $this->_getParam('page', 1);

		// Set item count per page and current page number
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page', $page));
	}
	
	public function reinviteAction() {
		// Prepare data
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();

    // Get user
    $user_id = (int)$this -> _getParam('user_id');
    $user = Engine_Api::_() -> getItem('user', $user_id); 
		if (!$user) {
			return $this -> _helper -> requireSubject -> forward();
		}
    
    // Return if user is already a member of group
		if ($group -> membership() -> isMember( $user, true)) {
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Can not invite a member of group')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
		// Make form
	
		
		$this -> view -> form = $form = new Advgroup_Form_Reinvite( array(
				'group' => $group -> getIdentity(),
				 'user' => $user_id, ));
		
		
		// Not posting
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
	
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
	
		// Process
		$table = $group -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		
		try {
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$group -> membership()-> setReinvite($user);
			$notifyApi -> addNotification($user, $viewer, $group, 'advgroup_invite');
				
			$db -> commit();
		}
		
		catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));
	}
	public function ajaxReinviteAction() {
		// Prepare data
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();	
		
		$user_id = (int)$this -> _getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);
		
	
		// Process
		$table = $group -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();
	
		try {
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$group -> membership()-> setReinvite($user);
			$notifyApi -> addNotification($user, $viewer, $group, 'advgroup_invite');
	
			$db -> commit();
		}
	
		catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		$content_id = (int)$this -> _getParam('content_id');
		$this->_forward('index', 'widget', 'core', array('content_id' =>$content_id) );
	}
	
	public function reinviteSelectedAction() {
  	// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		
		// Get user
		$this->view->ids = $ids = $this->_getParam('ids', null);
		$this->view->count = count(explode(",", $ids));
		$confirm = $this->_getParam('confirm', false);
		// Check post
		// Not posting
		if( $this->getRequest()->isPost() && $confirm == true )
		{
		// Process
			$table = $group -> getTable();
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			
			try {
				$ids_array = explode(",", $ids);
				
				foreach( $ids_array as $id ){
					// Get user
          $user = Engine_Api::_() -> getItem('user', $id);
          
					if (!$user || $group -> membership() -> isMember($user,true)) {
						continue;
					}
					
					$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
					$group -> membership()-> setReinvite($user);
					$notifyApi -> addNotification($user, $viewer, $group, 'advgroup_invite');
				}
				$db -> commit();
			}
			
			catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this->_helper->redirector->gotoRoute(array('controller'=>'invite-manage', 'action' => 'manage'));
		}
	}
}
?>