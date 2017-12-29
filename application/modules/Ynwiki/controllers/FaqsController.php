<?php

class Ynwiki_FaqsController extends Core_Controller_Action_Standard {
	
	public function init() {
	}

	public function indexAction() {
		$Table = new Ynwiki_Model_DbTable_Faqs;
		$select = $Table->select()->where('status=?','show')->order('ordering asc');
		$this->view->items = $items =  $Table->fetchAll($select);
	}

}
