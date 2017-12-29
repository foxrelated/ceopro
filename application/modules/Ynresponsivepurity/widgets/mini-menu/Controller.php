<?php
class Ynresponsivepurity_Widget_MiniMenuController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if(substr(YNRESPONSIVE_ACTIVE, 0, 18) != 'ynresponsivepurity')
		{
			return $this -> setNoRender(true);
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('core_mini');

		//Logo
		$this->view->logo = $this->_getParam('logo', false);
		$this->view->logo_link = $this->_getParam('logo_link', false);
		$this->view->site_name = $this->_getParam('site_name', false);
		$this->view->site_link = $this->_getParam('site_link', false);

		//Search
		$require_check = Engine_Api::_() -> getApi('settings', 'core') -> core_general_search;
		if (!$require_check)
		{
			if ($viewer -> getIdentity())
			{
				$this -> view -> search_check = true;
			}
			else
			{
				$this -> view -> search_check = false;
			}
		}
		else
			$this -> view -> search_check = true;
	}

}
