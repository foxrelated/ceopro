<?php

class Ynmobileview_Widget_MobiMenuLogoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		// Logo
		$this -> view -> logo = $this -> _getParam('logo');

	}

	public function getCacheKey()
	{
		//return true;
	}

}
