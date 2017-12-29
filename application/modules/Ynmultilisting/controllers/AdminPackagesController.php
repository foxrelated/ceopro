<?php
class Ynmultilisting_AdminPackagesController extends Core_Controller_Action_Admin {

	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_packages');
	}

	public function indexAction() {
		$this->view->form = $form = new Ynmultilisting_Form_Admin_Package_Search();
		$form->isValid($this->_getAllParams());
	    $params = $form->getValues();
	    $this->view->formValues = $params;
	    $this -> view -> page = $page = $this->_getParam('page',1);
	    $tablePackage = Engine_Api::_() -> getItemTable('ynmultilisting_package');
		$this -> view -> currency =  $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
		$this -> view -> paginator = $paginator = $tablePackage -> getPackagesPaginator($params);
	    $this->view->paginator->setItemCountPerPage(10);
	    $this->view->paginator->setCurrentPageNumber($page);
	}

	public function createAction() {
		
		$this->view->form = $form = new Ynmultilisting_Form_Admin_Package_Create();
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$db = Engine_Api::_()->getItemTable('ynmultilisting_package')->getAdapter();
    	$db->beginTransaction();
	    $viewer = Engine_Api::_() -> user() -> getViewer();
		try
		{
			  $package = Engine_Api::_()->getItemTable('ynmultilisting_package')->createRow();
			  $values = $form->getValues();
			  $values['user_id'] = $viewer->getIdentity();
			  $values['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
			  $package -> setFromArray($values);
			  $package->save();
			  
			  $db->commit();
						  
			 $levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			 $auth = Engine_Api::_() -> authorization() -> context;
			 $auth -> setAllowed($package, 'everyone', 'view', false);
			 foreach ($levels as $level) {
				$auth -> setAllowed($package, $level, 'view', false);
			 }
	
			 // Add permissions view package
			 if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($package, 'everyone', 'view', true);
			 } else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($package, $level, 'view', true);
				}
			 }
		}
		catch( Exception $e )
		{
		  $db->rollBack();
		  throw $e;
		}
		$this->_helper->redirector->gotoRoute(array('module'=>'ynmultilisting','controller'=>'packages', 'action' => 'index'), 'admin_default', true);
	}

	public function editAction()
	{
		$package = Engine_Api::_() -> getItem('ynmultilisting_package', $this->_getParam('id'));
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this->view->form = $form = new Ynmultilisting_Form_Admin_Package_Edit(array('package' => $package));
		$form -> populate($package->toArray());
		
		$auth = Engine_Api::_() -> authorization() -> context;
		$allowed = array();
		
		// populate permission view package 
		if ($auth -> isAllowed($package, 'everyone', 'view')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if (Engine_Api::_() -> authorization() -> context -> isAllowed($package, $level, 'view')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		
		if (!empty($allowed)) {
			$form -> populate(array('levels' => $allowed, ));
		}
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		
		$db = Engine_Db_Table::getDefaultAdapter();
    	$db->beginTransaction();
		
		$values = $form->getValues();
		try
		{
			$values['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
			$package -> setFromArray($values);
			$package->save();
			
		    // Handle permissions
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
	
			// Clear permissions view package by level
			$auth -> setAllowed($package, 'everyone', 'view', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($package, $level, 'view', false);
			}
	
			// Add permissions view package
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($package, 'everyone', 'view', true);
			} else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($package, $level, 'view', true);
				}
			}
			
			//send notification to listing owner
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$listings = $package -> getAllListings();
			foreach($listings as $listing)
			{
				$notifyApi -> addNotification($listing -> getOwner(), $package, $listing, 'ynmultilisting_listing_package_change', array('status' => 'changed'));
			}
			$db->commit();
		}
		catch( Exception $e )
		{
		  $db->rollBack();
		  throw $e;
		}
		$this->_helper->redirector->gotoRoute(array('module'=>'ynmultilisting','controller'=>'packages', 'action' => 'index'), 'admin_default', true);
	}
	
	public function deleteAction()
   {
    // In smoothbox
    $this->view->form = $form = new Ynmultilisting_Form_Admin_Package_Delete();
    $id = $this->_getParam('id');
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $package = Engine_Api::_()->getItem('ynmultilisting_package', $id);
		if($package -> getIdentity())
		{
			$package->deleted =  1;
			$package->save();
		}	
		
		//send notification to listing owner
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$listings = $package -> getAllListings();
		foreach($listings as $listing)
		{
			$notifyApi -> addNotification($listing -> getOwner(), $package, $listing, 'ynmultilisting_listing_package_change', array('status' => 'deleted'));
		}
		$db->commit();
		
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Package deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
    }
  }
	
	public function multiselectedAction() {
		$action = $this -> _getParam('select_action', 'Delete');
		$this -> view -> action = $action;
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			switch ($action) {
				case 'Delete' :
					foreach ($ids_array as $id) {
						$package = Engine_Api::_() -> getItem('ynmultilisting_package', $id);
						$package -> deleted = true;
						$package -> save();
					}
					break;
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}
	
	public function sortAction()
  	{
		$packages = Engine_Api::_()->getItemTable('ynmultilisting_package')->getPackagesPaginator($params);
	    $order = explode(',', $this->getRequest()->getParam('order'));
	    foreach( $order as $i => $item ) {
	      $package_id = substr($item, strrpos($item, '_')+1);
	      foreach( $packages as $package ) {
	        if( $package->package_id == $package_id ) {
	          $package->order = $i;
	          $package->save();
	        }
	    	}
    	}
	}
}
