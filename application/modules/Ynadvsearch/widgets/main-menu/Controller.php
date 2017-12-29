<?php
class Ynadvsearch_Widget_MainMenuController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->navigation = Engine_Api::_()
		->getApi('menus', 'core')
		->getNavigation('ynadvsearch_main', array());
		if (count($this->view->navigation) == 1) {
			$this->view->navitigation = null;
		}
	}
}
