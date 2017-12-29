<?php
class Ultimatenews_Widget_MenuUltimatenewsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		 // Get navigation
	    $this->view->navigation = $navigation = Engine_Api::_()
	    	->getApi('menus', 'core')
	    	->getNavigation('ultimatenews_main', array());
		  
	}
}
