<?php
class Ynmember_MenuController extends Core_Controller_Action_Standard
{
	public function renderAction()
	{
		$id = $this->_getParam('id');
		$user = Engine_Api::_()->user()->getUser($id);
		Engine_Api::_()->core()->clearSubject();
		Engine_Api::_()->core()->setSubject($user);
		$this->view->navigation = Engine_Api::_()
      		->getApi('menus', 'core')
      		->getNavigation('user_profile');
	}
}