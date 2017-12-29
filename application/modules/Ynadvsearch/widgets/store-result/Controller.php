<?php

class Ynadvsearch_Widget_StoreResultController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() {
		
		$viewer = Engine_Api::_() -> user() -> getViewer();	
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.store.page', 10);
		$this->view->items_per_page = $items_per_page;
		$values = Zend_Controller_Front::getInstance() -> getRequest()->getParams();
		$values['view_status'] = 'show';
		$values['approve_status'] = 'approved';
 		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		$this -> view -> paginator = $paginator = Engine_Api::_()->getApi('store','Socialstore')->getStoresPaginator($values);
		$paginator->setItemCountPerPage($items_per_page);
		$this->view->values = $values;
		$this->view->className = "layout_socialstore_listing_stores";
		
	}

}
