<?php

class Ultimatenews_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ultimatenews_admin_main', array(), 'ultimatenews_admin_main_settings');

		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Global();

		if ($this -> getRequest() -> isPost())
		{
			if (!($form -> isValid($this -> _getAllParams())))
			{
				return;
			}

			$values = $form -> getValues();
			foreach ($values as $key => $value)
			{
				Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
			}
			$form -> addNotice('Your changes have been saved.');
		}
	}

}
