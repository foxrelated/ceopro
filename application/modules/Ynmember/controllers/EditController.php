<?php

class Ynmember_EditController extends Core_Controller_Action_User
{
	  public function init()
	  {
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      // Can specifiy custom id
	      $id = $this->_getParam('id', null);
	      $subject = null;
	      if( null === $id ) {
	        $subject = Engine_Api::_()->user()->getViewer();
	        Engine_Api::_()->core()->setSubject($subject);
	      } else {
	        $subject = Engine_Api::_()->getItem('user', $id);
	        Engine_Api::_()->core()->setSubject($subject);
	      }
	    }
	    
	    if( !empty($id) ) {
	      $params = array('params' => array('id' => $id));
	    } else {
	      $params = array();
	    }
	    // Set up navigation
	    $this->view->navigation = $navigation = Engine_Api::_()
	      ->getApi('menus', 'core')
	      ->getNavigation('user_edit', $params);
	
	    $allParams = $this->_getAllParams();
	    // Set up require's
	    if ($allParams['action'] != 'get-my-location')
	    {
	    	$this->_helper->requireUser();
		    $this->_helper->requireSubject('user');
		    $this->_helper->requireAuth()->setAuthParams(
		      null,
		      null,
		      'edit'
		    );	
	    }
	  }		
	  
	  public function relationshipAction()
	  {
	  	$settings = Engine_Api::_()->getApi('settings', 'core');
	  	$allow_update_relationship = $settings->getSetting('ynmember_allow_update_relationship', 1);
		if(!$allow_update_relationship)
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
	  	 $this->view->user = $userObject =  Engine_Api::_()->core()->getSubject();
    	 $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    	 $linkageTbl = Engine_Api::_()->getItemTable('ynmember_linkage');
    	 
		 //check if admin edit
		 if(!($viewer -> isSelf($userObject) && $viewer -> isAdmin()))
		 {
		 	$viewer = $userObject;
		 }
		 
    	 //Init relation ship with member array
	  	 $tableRelationship = Engine_Api::_() -> getItemTable('ynmember_relationship');
		 $this -> view -> relationships = $relationships = $tableRelationship -> getAllRelationships();
		 $relationshipArr = array();
		 $relationshipArr[] = array(
		 		'rel_id' => 0,
		 		'rel_with' => 0
		 );
		 foreach ($relationships as $rel){
		 	$relationshipArr[] = array(
		 		'rel_id' => $rel->relationship_id,
		 		'rel_with' => $rel->with_member
		 	);
		 }
		 $this -> view -> relationshipStr = Zend_Json::encode($relationshipArr);
		 $this -> view -> form = $form = new Ynmember_Form_Relationship_Addstatus();
		 $this -> view -> linkage = $oldLinkageRow = $linkageRow = $linkageTbl -> getLinkage ($userObject, null, true);
		 if (!is_null($linkageRow))
		 {
		 	if ($linkageRow -> user_id == $userObject->getIdentity() && $linkageRow->user_approved)
		 	{
		 		$dateObject = null;
		 		if ($linkageRow->anniversary)
		 		{
		 			$dateObject = new Zend_Date(strtotime($linkageRow->anniversary));	
		 		}
			 	$form->populate(array(
			 		'relationship' => $linkageRow->relationship_id,
			 		'anniversary' => (!is_null($dateObject)) ? $dateObject->toString('y-MM-dd') : '',
			 		'toValues' => ($linkageRow->resource_id) ? $linkageRow->resource_id : '',
			 	));
			 	$this->view->isPopulated = true;
			 	$this->view->toObject = ($linkageRow->resource_id) 
			 		? Engine_Api::_()->user()->getUser($linkageRow->resource_id)
			 		: null;
		 	}
		 }
		 
		 $this -> view -> request = $request = $linkageTbl -> getRequest ($userObject);
		 if (count($request))
		 {
		 	$this -> view -> confirm  = true;
		 }
		 
	  	if( !$this->getRequest()->isPost() ) {
			return;
		 }
	  	if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		 // PROCESS SAVING
		 $values = $form->getValues();
		 $resourceUser = null;
		 if ($values['toValues'])
		 {
		 	$resourceUser = Engine_Api::_()->user()->getUser( $values['toValues']);
		 }
		 $relationship = Engine_Api::_()->getItem('ynmember_relationship', $values['relationship']);
		 
		 // LINKAGE FROM USER AND RESOURCE
		 $linkageRow = $linkageTbl -> addLinkage ($viewer, $resourceUser);
		 
		 //check if user save changes or change relationship type
		 $resource_approved = (isset($linkageRow -> user_approved) && $linkageRow -> user_approved)? 1 : 0;
		 if(isset($linkageRow -> relationship_id))
		 {
			if($values['relationship'] != $linkageRow -> relationship_id)
			{
				$resource_approved = 0;
			}
		 }
		 
		 $linkageRow -> setFromArray(array(
		 	'relationship_id' => $values['relationship'],
		 	'user_id' => $viewer->getIdentity(),
	      	'user_approved' => 1,
		 	'resource_approved' => $resource_approved,
		 ));
		 if ($values['anniversary'])
		 {
		 	$anniversaryDateObject = new Zend_Date(strtotime($values['anniversary'])); 
		 	$linkageRow -> anniversary = $anniversaryDateObject->get('yyyy-MM-dd');
		 } else {
			 $linkageRow -> anniversary = NULL;
		 }
	     if ($relationship->with_member == '0')
		 {
		 	$linkageRow -> resource_id =  null;
		 }
		 $linkageRow->save();
		 
		 // LINKAGE FROM RESOURCE AND USER
		 if (!is_null($resourceUser) && $relationship->with_member == '1')
		 {
		 	// UPDATE FIRST LINKAGE
		 	$linkageRow -> resource_id = $resourceUser->getIdentity();
			
			if ($relationship -> user_approved == '0') // NO NEED TO BE APPROVED
			{
			 	$linkageRow -> resource_approved = 1;
			 	$linkageRow -> active = 1;  
			}
		 	$linkageRow->save();
		 	
		 	// CREATE SECOND LINKAGE
		 	$linkageRow1 = $linkageTbl -> addLinkage ($resourceUser, $viewer);
			
			//check if user save changes or change relationship type
			$user_approved = (isset($linkageRow1 -> user_approved) && $linkageRow1 -> user_approved)? 1 : 0;
			if(isset($linkageRow1 -> relationship_id))
			{
				if($values['relationship'] != $linkageRow1 -> relationship_id)
				{
					$user_approved = 0;
				}
			}
			
		 	$linkageRow1 -> setFromArray(array(
			 	'relationship_id' => $values['relationship'],
		 		'user_id' => $resourceUser->getIdentity(),
		      	'user_approved' => $user_approved,
			 	'resource_id' => $viewer->getIdentity(),
			 	'resource_approved' => 1,
			 ));
			 if ($values['anniversary'])
			 {
			 	$linkageRow1 -> anniversary = $anniversaryDateObject->get('yyyy-MM-dd');
			 }
		 	 if ($relationship -> user_approved == '0') // NO NEED TO BE APPROVED
			 {
			 	$linkageRow1 -> user_approved = 1;
			 	$linkageRow1 -> active = 1;  
			 }
			 
			 $isNew = false;
		     try {
		   		$idlinkageRow1 = $linkageRow1 -> getIdentity();
				if($idlinkageRow1 == 0) {
					$isNew = true;
				}
             }
             catch(Exception $e) {
            	$isNew = true;
			 }
			 $linkageRow1 -> save();
			 
			 if($isNew) {
			 	 $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			 	 $editProfileLink = $this->view->url(array('controller' => 'edit', 'action' => 'relationship'), 'ynmember_extended');
			 	 $linkTitle = Zend_Registry::get("Zend_Translate")->_("has added new relationship with you.");
			 	 $notifyApi->addNotification($resourceUser, $viewer, $viewer, 'ynmember_notification_linkage', array(
						'text' => "<a href='{$editProfileLink}'>{$linkTitle}</a>",
				 ));
			 }
		}
		
		// Notify for got notification members
		if ( is_null($oldLinkageRow) || ($oldLinkageRow -> relationship_id != $linkageRow -> relationship_id) )
		{
			$notificationTbl = Engine_Api::_()->getDbTable('notifications', 'ynmember');
			$receivers = $notificationTbl -> getAllUsers($viewer);
			if (count($receivers))
			{
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				foreach ($receivers as $toUser)
				{
					if (!is_null($resourceUser) && ($toUser -> getIdentity() == $resourceUser -> getIdentity()))
					{
						continue;
					}
					$notifyApi->addNotification($toUser, $viewer, $viewer, 'ynmember_notification_change_relationship', array(
						'text' => Zend_Registry::get("Zend_Translate")->_("has changed the relationship.")
					));
				}
			}
		}
		
		
		// SET PRIVACY FOR LINKAGE
		if ($_POST['auth_view'] && $_POST['auth_view'] != '')
		{
			$user = Engine_Api::_()->core()->getSubject();
			$auth = Engine_Api::_()->authorization()->context;
		    $roles = array('owner', 'owner_member', 'registered', 'everyone');
			  switch ($_POST['auth_view']) {
				  case 'everyone':
					  $viewPrivacy = 'everyone';
					  break;
				  case 'registered':
					  $viewPrivacy = 'registered';
					  break;
				  case 'friends':
					  $viewPrivacy = 'owner_member';
					  break;
				  case 'self':
					  $viewPrivacy = 'owner';
					  break;	  
			  }
		      if( empty($viewPrivacy) ) {
		        	$viewPrivacy = 'everyone';
		      }
		      $viewMax = array_search($viewPrivacy, $roles);
		
		      foreach( $roles as $i => $role ) {
		        	$auth->setAllowed($linkageRow, $role, 'view', ($i <= $viewMax));
		      }
		      
			  // Rebuild privacy
		      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
		      foreach( $actionTable->getActionsByObject($linkageRow) as $action ) {
					$actionTable->resetActivityBindings($action);
		      }
		}
		
	    if ($relationship->appear_feed == '1')
	    {
		    $feedContent = $linkageTbl -> getLinkageAsString($viewer);
			if ($feedContent != '')
			{
				$api = Engine_Api::_()->getDbtable('actions', 'activity');
				$linkage = $linkageTbl -> getLinkage($viewer);
				// DELETE OLD ACTIONS
				$select = $api -> select()
					-> where('type = ?', 'ynmember_relationship')
					-> where('subject_type = ?', 'user')
					-> where('subject_id = ?', $viewer->getIdentity())
					-> where('object_type = ?', 'ynmember_linkage')
					-> where('object_id = ?', $linkage->getIdentity());

				$actions = $api -> fetchAll($select);
				foreach ($actions as $action) {
					$action -> deleteItem();
				}

				$api->addActivity($viewer, $linkage , 'ynmember_relationship', $feedContent, array());
			}	
	    }
			
		 // DONE
		 $form->addNotice('Your changes have been saved.'); 
		 return $this->_helper->redirector->gotoRoute(); 
	  }
	  
	  public function placeAction()
	  {
	  	$settings = Engine_Api::_()->getApi('settings', 'core');
	  	 $this->view->user = $user = Engine_Api::_()->core()->getSubject();
    	 $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		 $tableWork = Engine_Api::_() -> getItemTable('ynmember_workplace');
		 $tableLive = Engine_Api::_() -> getItemTable('ynmember_liveplace');
		 $tableStudy = Engine_Api::_() -> getItemTable('ynmember_studyplace');
		 $this -> view -> workplaces = $workplaces = $tableWork -> getWorkPlacesByUserId($user -> getIdentity());
		 $this -> view -> studyplaces = $studyplaces = $tableStudy -> getStudyPlacesByUserId($user -> getIdentity());
	  	 $this -> view -> currentliveplaces = $currentliveplaces = $tableLive -> getLiveCurrentPlacesByUserId($user -> getIdentity());
	  	 $this -> view -> pastliveplaces = $pastliveplaces = $tableLive -> getLivePastPlacesByUserId($user -> getIdentity());
	 	 $can_add_place =  $settings->getSetting('ynmember_allow_add_workplace', 1);
		 if(!$can_add_place)
		 {
		 	$this->_helper->requireSubject()->forward();
		 }
	  }
	  
	  public function editPrivacyRelationshipAction()
	  {
	  	$user = Engine_Api::_()->core()->getSubject();
	  	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$linkage = Engine_Api::_() -> getItem('ynmember_linkage', $this->_getParam('id_linkage'));
	  	$params = $this->_getAllParams();
		// Auth
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'registered', 'everyone');
		  switch ($params['auth_view']) {
			  case 'everyone':
				  $values['auth_view'] = 'everyone';
				  break;
			  case 'registered':
				   $values['auth_view'] = 'registered';
				  break;
			  case 'friends':
				   $values['auth_view'] = 'owner_member';
				  break;
			  case 'self':
				   $values['auth_view'] = 'owner';
				  break;	  
		  }
	      if( empty($values['auth_view']) ) {
	        $values['auth_view'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['auth_view'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($linkage, $role, 'view', ($i <= $viewMax));
	      }
	      
		  // Rebuild privacy
	      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
	      foreach( $actionTable->getActionsByObject($linkage) as $action ) {
	        $actionTable->resetActivityBindings($action);
	      }
	  }
	  
	  public function editPrivacyStudyPlaceAction()
	  {
	  	$user = Engine_Api::_()->core()->getSubject();
	  	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$place = Engine_Api::_() -> getItem('ynmember_studyplace', $this->_getParam('id_studyplace'));
	  	$params = $this->_getAllParams();
		// Auth
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'registered', 'everyone');
		  switch ($params['auth_view']) {
			  case 'everyone':
				  $values['auth_view'] = 'everyone';
				  break;
			  case 'registered':
				   $values['auth_view'] = 'registered';
				  break;
			  case 'friends':
				   $values['auth_view'] = 'owner_member';
				  break;
			  case 'self':
				   $values['auth_view'] = 'owner';
				  break;	  
		  }
	      if( empty($values['auth_view']) ) {
	        $values['auth_view'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['auth_view'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($place, $role, 'view', ($i <= $viewMax));
	      }
	  }
	  
	  public function editPrivacyWorkPlaceAction()
	  {
	  	$user = Engine_Api::_()->core()->getSubject();
	  	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$place = Engine_Api::_() -> getItem('ynmember_workplace', $this->_getParam('id_workplace'));
	  	$params = $this->_getAllParams();
		// Auth
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'registered', 'everyone');
		  switch ($params['auth_view']) {
			  case 'everyone':
				  $values['auth_view'] = 'everyone';
				  break;
			  case 'registered':
				   $values['auth_view'] = 'registered';
				  break;
			  case 'friends':
				   $values['auth_view'] = 'owner_member';
				  break;
			  case 'self':
				   $values['auth_view'] = 'owner';
				  break;	  
		  }
	      if( empty($values['auth_view']) ) {
	        $values['auth_view'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['auth_view'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($place, $role, 'view', ($i <= $viewMax));
	      }
	  }
	  
	  public function editPrivacyLivePlaceAction()
	  {
	  	$user = Engine_Api::_()->core()->getSubject();
	  	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$place = Engine_Api::_() -> getItem('ynmember_liveplace', $this->_getParam('id_liveplace'));
	  	$params = $this->_getAllParams();
		// Auth
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'registered', 'everyone');
		  switch ($params['auth_view']) {
			  case 'everyone':
				  $values['auth_view'] = 'everyone';
				  break;
			  case 'registered':
				   $values['auth_view'] = 'registered';
				  break;
			  case 'friends':
				   $values['auth_view'] = 'owner_member';
				  break;
			  case 'self':
				   $values['auth_view'] = 'owner';
				  break;	  
		  }
	      if( empty($values['auth_view']) ) {
	        $values['auth_view'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['auth_view'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($place, $role, 'view', ($i <= $viewMax));
	      }
	  }
	  
	   public function addStudyPlaceAction()
	  {
	  	
	  	$this->view->user = $user = Engine_Api::_()->core()->getSubject();
	  	$this -> view -> form = $form = new Ynmember_Form_StudyPlace_Create();
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			return;
		}
		$values = $form -> getValues();
		$tableStudy = Engine_Api::_() -> getItemTable('ynmember_studyplace');
		$place = $tableStudy -> createRow();
		$place -> name = $values['name'];
		//$place -> location = $values['location_address'];
		$place -> location = (isset($_POST['location']) && $_POST['location'] != '') ? $_POST['location'] : $values['location_address'];
		$place -> longitude = $values['long'];
		$place -> latitude = $values['lat'];
		$place -> current = $values['current'];
		$place -> creation_date =  date("Y-m-d H:i:s");
		$place -> modified_date =  date("Y-m-d H:i:s");
		$place -> user_id = $user -> getIdentity();
		$place -> save();
		
		if($values['current'] == 1)
		{
			$select = $tableStudy -> select() -> where('studyplace_id <> ?', $place -> getIdentity()) -> where('user_id = ?', $user->getIdentity());
			$results = $tableStudy -> fetchAll($select);
			foreach($results as $row)
			{
				$row -> current = 0;
				$row -> save();
			}
		}
		
	      // Auth
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'registered', 'everyone');
	
	      if( empty($values['auth_view']) ) {
	        $values['auth_view'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['auth_view'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($place, $role, 'view', ($i <= $viewMax));
	      }
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Study place added.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	  }
	  
	   public function editStudyPlaceAction()
	   {
	  	$place = Engine_Api::_() -> getItem('ynmember_studyplace', $this->_getParam('id_studyplace'));
		if(!$place)
		{
			return;
		}
	  	$this->view->user = $user = Engine_Api::_()->core()->getSubject();
	  	$this -> view -> form = $form = new Ynmember_Form_StudyPlace_Edit(array('location' => $place->location));
		$values = $place -> toArray();
		$values['location_address'] = $place -> location;
		$values['long'] = $place -> longitude;
		$values['lat'] = $place -> latitude;
		$form -> populate($values);
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			return;
		}
		$values = $form -> getValues();
		$place -> name = $values['name'];
		$place -> location = $values['location_address'];
		$place -> longitude = $values['long'];
		$place -> latitude = $values['lat'];
		$place -> current = $values['current'];
		$place -> modified_date =  date("Y-m-d H:i:s");
		$place -> save();
		
		if($values['current'] == 1)
		{
			$tableStudy= Engine_Api::_()->getItemTable('ynmember_studyplace');
			$select = $tableStudy -> select() -> where('studyplace_id <> ?', $place -> getIdentity()) -> where('user_id = ?', $user->getIdentity());
			$results = $tableStudy -> fetchAll($select);
			foreach($results as $row)
			{
				$row -> current = 0;
				$row -> save();
			}
		}
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Study place edited.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	  }
	  
	 public function deleteStudyPlaceAction()
	 {
		$id = $this->_getParam('id_studyplace');
		$this->view->form = $form = new Ynmember_Form_StudyPlace_Delete();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$studyplace = Engine_Api::_()->getItem('ynmember_studyplace', $id);
        if ($studyplace) {
            $studyplace->delete();
        }
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Study place deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	 }
	  
	  public function checkStudyPlaceAction()
	  {
	  	$user = Engine_Api::_()->core()->getSubject();
	  	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$tableStudyPlace = Engine_Api::_()->getItemTable('ynmember_studyplace');
        $id = $this->_getParam('id_check');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
		$studyplace = Engine_Api::_()->getItem('ynmember_studyplace', $id);
		if($value == 1)
		{
			$select = $tableStudyPlace -> select() -> where('studyplace_id <> ?', $id) -> where('user_id = ?', $user->getIdentity());
			$results = $tableStudyPlace -> fetchAll($select);
			foreach($results as $row)
			{
				$row -> current = 0;
				$row -> save();
			}
			$studyplace -> current = $value;
			$studyplace -> save();
		}
		else 
		{
			$studyplace -> current = $value;
			$studyplace -> save();
		}
		
	  }

	   public function addWorkPlaceAction()
	  {
	  	
	  	$this->view->user = $user = Engine_Api::_()->core()->getSubject();
	  	$this -> view -> form = $form = new Ynmember_Form_WorkPlace_Create();
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			return;
		}
		$values = $form -> getValues();
		$tableWork = Engine_Api::_() -> getItemTable('ynmember_workplace');
		$place = $tableWork -> createRow();
		$place -> company = $values['company'];
		//$place -> location = $values['location_address'];
		$place -> location = (isset($_POST['location']) && $_POST['location'] != '') ? $_POST['location'] : $values['location_address'];
		$place -> longitude = $values['long'];
		$place -> latitude = $values['lat'];
		$place -> current = $values['current'];
		$place -> creation_date =  date("Y-m-d H:i:s");
		$place -> modified_date =  date("Y-m-d H:i:s");
		$place -> user_id = $user -> getIdentity();
		$place -> save();
		
		if($values['current'] == 1)
		{
			$select = $tableWork -> select() -> where('workplace_id <> ?', $place -> getIdentity()) -> where('user_id = ?', $user->getIdentity());
			$results = $tableWork -> fetchAll($select);
			foreach($results as $row)
			{
				$row -> current = 0;
				$row -> save();
			}
		}
		
	      // Auth
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'registered', 'everyone');
	
	      if( empty($values['auth_view']) ) {
	        $values['auth_view'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['auth_view'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($place, $role, 'view', ($i <= $viewMax));
	      }
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Work place added.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	  }
	  
	   public function editWorkPlaceAction()
	   {
	  	$place = Engine_Api::_() -> getItem('ynmember_workplace', $this->_getParam('id_workplace'));
		if(!$place)
		{
			return;
		}
	  	$this->view->user = $user = Engine_Api::_()->core()->getSubject();
	  	$this -> view -> form = $form = new Ynmember_Form_WorkPlace_Edit(array('location' => $place->location));
		$values = $place -> toArray();
		$values['location_address'] = $place -> location;
		$values['long'] = $place -> longitude;
		$values['lat'] = $place -> latitude;
		$form -> populate($values);
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			return;
		}
		$values = $form -> getValues();
		$place -> company = $values['company'];
		$place -> location = $values['location_address'];
		$place -> longitude = $values['long'];
		$place -> latitude = $values['lat'];
		$place -> current = $values['current'];
		$place -> modified_date =  date("Y-m-d H:i:s");
		$place -> save();
		
		if($values['current'] == 1)
		{
			$tableWork = Engine_Api::_()->getItemTable('ynmember_workplace');
			$select = $tableWork -> select() -> where('workplace_id <> ?', $place -> getIdentity()) -> where('user_id = ?', $user->getIdentity());
			$results = $tableWork -> fetchAll($select);
			foreach($results as $row)
			{
				$row -> current = 0;
				$row -> save();
			}
		}
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Work place edited.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	  }
	  
	 public function deleteWorkPlaceAction()
	 {
		$id = $this->_getParam('id_workplace');
		$this->view->form = $form = new Ynmember_Form_WorkPlace_Delete();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$workplace = Engine_Api::_()->getItem('ynmember_workplace', $id);
        if ($workplace) {
            $workplace->delete();
        }
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Work place deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	 }
	  
	  public function checkPlaceAction()
	  {
	  	$user = Engine_Api::_()->core()->getSubject();
	  	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$tableWorkPlace = Engine_Api::_()->getItemTable('ynmember_workplace');
        $id = $this->_getParam('id_check');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
		$workplace = Engine_Api::_()->getItem('ynmember_workplace', $id);
		if($value == 1)
		{
			$select = $tableWorkPlace -> select() -> where('workplace_id <> ?', $id) -> where('user_id = ?', $user->getIdentity());
			$results = $tableWorkPlace -> fetchAll($select);
			foreach($results as $row)
			{
				$row -> current = 0;
				$row -> save();
			}
			$workplace -> current = $value;
			$workplace -> save();
		}
		else 
		{
			$workplace -> current = $value;
			$workplace -> save();
		}
		
	  }
	  
	   public function addLivePlaceAction()
	  {
	  	
	  	$this->view->user = $user = Engine_Api::_()->core()->getSubject();
	  	$this -> view -> form = $form = new Ynmember_Form_LivePlace_Create();
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			return;
		}
		$values = $form -> getValues();
		$tableLive = Engine_Api::_() -> getItemTable('ynmember_liveplace');
		$place = $tableLive -> createRow();
		//$place -> location = $values['location_address'];
		$place -> location = (isset($_POST['location']) && $_POST['location'] != '') ? $_POST['location'] : $values['location_address'];
		$place -> longitude = $values['long'];
		$place -> latitude = $values['lat'];
		$place -> current = $this->_getParam('current', 0);
		$place -> creation_date =  date("Y-m-d H:i:s");
		$place -> modified_date =  date("Y-m-d H:i:s");
		$place -> user_id = $user -> getIdentity();
		$place -> save();
		
	      // Auth
	      $auth = Engine_Api::_()->authorization()->context;
	      $roles = array('owner', 'owner_member', 'registered', 'everyone');
	
	      if( empty($values['auth_view']) ) {
	        $values['auth_view'] = 'everyone';
	      }
	
	      $viewMax = array_search($values['auth_view'], $roles);
	
	      foreach( $roles as $i => $role ) {
	        $auth->setAllowed($place, $role, 'view', ($i <= $viewMax));
	      }
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Living place added.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	  }

	  public function editLivePlaceAction()
	   {
	  	$place = Engine_Api::_() -> getItem('ynmember_liveplace', $this->_getParam('id_liveplace'));
		if(!$place)
		{
			return;
		}
	  	$this->view->user = $user = Engine_Api::_()->core()->getSubject();
	  	$this -> view -> form = $form = new Ynmember_Form_LivePlace_Edit(array('location' => $place->location));
		$values_populate = $place -> toArray();
		$values_populate['location_address'] = $place -> location;
		$values_populate['long'] = $place -> longitude;
		$values_populate['lat'] = $place -> latitude;
		$form -> populate($values_populate);
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($posts))
		{
			return;
		}
		$values = $form -> getValues();
		$place -> location = $values['location_address'];
		$place -> longitude = $values['long'];
		$place -> latitude = $values['lat'];
		$place -> modified_date =  date("Y-m-d H:i:s");
		$place -> save();
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Living place edited.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	  }
	  
	 public function deleteLivePlaceAction()
	 {
		$id = $this->_getParam('id_liveplace');
		$this->view->form = $form = new Ynmember_Form_LivePlace_Delete();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$liveplace = Engine_Api::_()->getItem('ynmember_liveplace', $id);
        if ($liveplace) {
            $liveplace->delete();
        }
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Living place deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	 }
	  
    public function editPhotoAction()
    {
        $this->view->user = $user = Engine_Api::_()->core()->getSubject();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        // Get form
        $this->view->form = $form = new Ynmember_Form_Edit_Photo();

        if( empty($user->photo_id) ) {
            $form->removeElement('remove');
        }

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        // Uploading a new photo
        if( $form->Filedata->getValue() !== null ) {
            $db = $user->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $fileElement = $form->Filedata;

                $user->setPhoto($fileElement);

                $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

                // Insert activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
                    '{item:$subject} added a new profile photo.');

                // Hooks to enable albums to work
                if( $action ) {
                    $event = Engine_Hooks_Dispatcher::_()
                        ->callEvent('onUserProfilePhotoUpload', array(
                            'user' => $user,
                            'file' => $iMain,
                        ));

                    $attachment = $event->getResponse();
                    if( !$attachment ) $attachment = $iMain;

                    // We have to attach the user himself w/o album plugin
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                }

                $db->commit();
            }

                // If an exception occurred within the image adapter, it's probably an invalid image
            catch( Engine_Image_Adapter_Exception $e )
            {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            }

                // Otherwise it's probably a problem with the database or the storage system (just throw it)
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }
        }

        // Resizing a photo
        else if( $form->getValue('coordinates') !== '' ) {
            $storage = Engine_Api::_()->storage();

            $iProfile = $storage->get($user->photo_id, 'thumb.profile');
            $iSquare = $storage->get($user->photo_id, 'thumb.icon');

            // Read into tmp file
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);

            list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));

            $image = Engine_Image::factory();
            $image->open($pName)
                ->resample($x+.1, $y+.1, $w-.1, $h-.1, 48, 48)
                ->write($iName)
                ->destroy();

            $iSquare->store($iName);

            // Remove temp files
            @unlink($iName);
        }

        $isDone = $this->_getParam('done');

        if ( isset($isDone)) {
            return $this -> _forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Edit photo successfully.')),
                'format' => 'smoothbox',
                'smoothboxClose' => true,
                'parentRefresh' => true,
            ));
        }
    }

    public function editCoverPhotoAction()
    {
        $this->view->form = $form = new Ynmember_Form_EditCover();
        $this->view->user = $user = Engine_Api::_()->core()->getSubject();

        $viewer = Engine_Api::_() -> user() -> getViewer();

        // Check method and data validity.
        if( !$this->getRequest()->isPost() ) {
            return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        $values = $form -> getValues();
        // Add Cover photo
        if (!empty($values['cover_thumb'])) {
            $user = Engine_Api::_() -> ynmember() -> setCoverPhoto($user, $form -> cover_thumb);
            $user->cover_top = 0;
            $user->save();
        }

        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Edit cover photo successfully.')),
            'format' => 'smoothbox',
            'smoothboxClose' => true,
            'parentRefresh' => true,
        ));
    }

    public function removePhotoAction()
    {
        // Get form
        $this->view->form = $form = new Ynmember_Form_Edit_RemovePhoto();

        if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) )
        {
            return;
        }


        $user = Engine_Api::_()->core()->getSubject();
        $user->photo_id = 0;
        $user->save();

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.');

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.'))
        ));
    }
	  public function getMyLocationAction()
	  {
		$latitude = $this -> _getParam('latitude');
		$longitude = $this -> _getParam('longitude');
		$values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
		echo $values;
		die ;
	  }
	  
	  public function repositionCoverAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $user = Engine_Api::_()->core()->getSubject();
        if (!$user) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The user can not be found.')));
        }
        
		$viewer = Engine_Api::_()->user()->getViewer();
        if (!$user->authorization()->isAllowed($viewer, 'edit')) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have permission to do this.')));
        }
        
        $position = $this->_getParam('position', null);
        if (is_null($position)) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The request is invalid.')));
        }
        
        $user->cover_top = $position;
        $user->save();
        
        echo Zend_Json::encode(array('status' => true));
    }
}
