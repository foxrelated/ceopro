<?php

class Ynadvsearch_Widget_SearchMiniController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$params = $this->_getAllParams();
		$this->view->maxRe =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynadvsearch_num_searchitem', 10);

		$session = new Zend_Session_Namespace('mobile');
        $this->view->isMobile = $session->mobile;
	}
}
