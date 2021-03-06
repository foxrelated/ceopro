<?php
class Ynmobileview_Widget_LoginOrSignupController extends Engine_Content_Widget_Abstract
{
	private $_mode;
	public function getMode()
	{
		if (null === $this -> _mode)
		{
			$this -> _mode = 'page';
		}
		return $this -> _mode;
	}

	public function indexAction()
	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		//die;
		//Facebook Connect
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		if ('none' != $settings -> getSetting('core_facebook_enable', 'none') && $settings -> core_facebook_secret)
		{
			$this -> view -> fblogin = new Engine_Form_Element_Dummy('facebook', array(
				'content' => User_Model_DbTable_Facebook::loginButton(),
				'decorators' => array('ViewHelper'),
			));
			$this -> view -> fbLoginEnabled = true;
		}
		else
		{
			$this -> view -> fbLoginEnabled = false;
			$this -> view -> fblogin = new Engine_Form_Element_Dummy('facebook', array(
				'content' => 'fblogin here',
				'decorators' => array('ViewHelper'),
			));
		}
		//Facebook Connect end

		//Twitter Connect
		// Init twitter login link
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		if ('none' != $settings -> getSetting('core_twitter_enable', 'none') && $settings -> core_twitter_secret)
		{
			$this -> view -> twlogin = new Engine_Form_Element_Dummy('twitter', array(
				'content' => User_Model_DbTable_Twitter::loginButton(),
				'decorators' => array('ViewHelper'),
			));
			$this -> view -> TwLoginEnabled = true;
		}
		else
		{
			$this -> view -> TwLoginEnabled = false;
			$this -> view -> twlogin = new Engine_Form_Element_Dummy('twitter', array(
				'content' => 'twlogin here',
				'decorators' => array('ViewHelper'),
			));
		}

		//Twitter Connect end

		//Janrain Connect
		// Init janrain login link
		if ('none' != $settings -> getSetting('core_janrain_enable', 'none') && $settings -> core_janrain_key)
		{
			$this -> view -> jrlogin = new Engine_Form_Element_Dummy('janrain', array(
				'content' => User_Model_DbTable_Janrain::loginButton(''),
				'decorators' => array('ViewHelper'),
			));
			$this -> view -> JrLoginEnabled = true;
		}
		else
		{
			$this -> view -> JrLoginEnabled = false;
			$this -> view -> jrlogin = new Engine_Form_Element_Dummy('janrain', array(
				'content' => 'janrain here',
				'decorators' => array('ViewHelper'),
			));
		}
		//Janrain Connect end
	}

	public function getCacheKey()
	{
		return false;
	}

}
