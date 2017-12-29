<?php

class Advalbum_Widget_AlbumsQuickLinksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advalbum_main');
		// Get quick navigation
		$this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advalbum_quick');

		if (!$quickNavigation) {
			$this->setNoRender();
		}
	}
}
