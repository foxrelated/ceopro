<?php

class Ynmobileview_Widget_MobiMenuMiniController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Menu Mini
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		if ($viewer -> getIdentity())
		{
			$this -> view -> notificationCount = Engine_Api::_() -> getDbtable('notifications', 'activity') -> hasNotifications($viewer);
		}

		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$this -> view -> notificationOnly = $request -> getParam('notificationOnly', false);
		$this -> view -> updateSettings = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.general.notificationupdate');
	}

	public function getCacheKey()
	{
		//return true;
	}

}
