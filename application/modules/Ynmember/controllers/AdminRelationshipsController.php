<?php

class Ynmember_AdminRelationshipsController extends Core_Controller_Action_Admin
{
	public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmember_admin_main', array(), 'ynmember_admin_main_relationships');
    }
	
    public function indexAction() 
    {
	    $this->view->relationships = Engine_Api::_()->getItemTable('ynmember_relationship')->getAllRelationships();
    }
	
	public function deleteAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$relationship = Engine_Api::_() -> getItem('ynmember_relationship', $this ->_getParam('id'));
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynmember_Form_Admin_Relationship_Delete();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$db = $relationship -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$relationship -> delete();
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Status Deleted.')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}
	
	public function createAction()
	{
		// Get form
		$this -> view -> form = $form = new Ynmember_Form_Admin_Relationship_Create();
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		
		// Save
		 $values = $form->getValues();
	     $relationship = Engine_Api::_() -> getItemTable('ynmember_relationship') -> createRow();
		 $relationship-> status = $values['status'];
		 $relationship-> with_member = $values['with_member'];
		 $relationship-> appear_feed  = $values['appear_feed'];
		 $relationship-> user_approved = $values['user_approved'];
	     $relationship->save();
    
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Status Added.')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}
	
	public function editAction()
	{
		$relationship = Engine_Api::_() -> getItem('ynmember_relationship', $this->_getParam('id'));
		// Get form
		$this -> view -> form = $form = new Ynmember_Form_Admin_Relationship_Edit();
		$form -> populate($relationship -> toArray());
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Save
		 $values = $form->getValues();
		 if(!empty($values['status']))
			 $relationship-> status = $values['status'];
		 if(!empty($values['with_member']))
		 	$relationship-> with_member  = $values['with_member'];
		 if(!empty($values['appear_feed']))
			 $relationship-> appear_feed = $values['appear_feed'];
		 if(!empty($values['user_approved']))
			 $relationship-> user_approved = $values['user_approved'];
	     $relationship->save();
    
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Status Edited.')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}
	
	public function withAction() 
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
       	 $value = $this->_getParam('value');
        if ($value == null) return;
        	$relationship = Engine_Api::_()->getItem('ynmember_relationship', $id);
        if ($relationship) {
            $relationship->with_member = $value;
            $relationship->save();
        }
   }
	
	public function appearAction() 
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
       	 $value = $this->_getParam('value');
        if ($value == null) return;
        	$relationship = Engine_Api::_()->getItem('ynmember_relationship', $id);
        if ($relationship) {
            $relationship->appear_feed = $value;
            $relationship->save();
        }
   }
	
	public function approveAction() 
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
       	 $value = $this->_getParam('value');
        if ($value == null) return;
        	$relationship = Engine_Api::_()->getItem('ynmember_relationship', $id);
        if ($relationship) {
            $relationship->user_approved = $value;
            $relationship->save();
        }
   }
	
}
