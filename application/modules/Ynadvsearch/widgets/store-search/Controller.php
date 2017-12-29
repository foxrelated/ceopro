<?php

class Ynadvsearch_Widget_StoreSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('socialstore')) {
            $this->setNoRender();
        }
	    $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form = $form = new Socialstore_Form_Search();
		$location_id = $form->getValue('location_id');
		$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
		$this->view->route = $route;		
		if ($location_id != '') {
			$location = new Socialstore_Model_DbTable_Locations();
			$select = $location -> select() -> where('pid = ?', $location_id);
			$count = $location -> fetchRow($select);
			if ($count) {
				$row = $location->find($location_id)->current();
				$this->view->level = $row->level - 1;
				
			}
		}
	}
}
