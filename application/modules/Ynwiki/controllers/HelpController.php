<?php

class Ynwiki_HelpController extends Core_Controller_Action_Standard {
	
	public function init() {
	}
	
	public function indexAction(){
		$id =  $this->_getParam('id',0);
		$table = new Ynwiki_Model_DbTable_HelpPages;
		$item =  $table->find($id)->current();
		
		if(!is_object($item)){
			$select = $table->select()->where('status=?','show')->order('ordering asc');
			$item = $table->fetchRow($select);
		}
		
		if(!is_object($item)){
			return $this->_forward('empty');
		}
		
		$this->view->item = $item;
		
		Zend_Registry::set('ACTIVE_HELP_PAGE', $item->getIdentity());
	}
	
	public function emptyAction(){
		
	}
}
