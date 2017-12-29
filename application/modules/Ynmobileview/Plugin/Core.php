<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
	public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
		$module = $request -> getModuleName();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		$session = new Zend_Session_Namespace('mobile');
		if($controller == 'error' && $action == 'requireuser' && $session -> mobile)
		{
			$request -> setModuleName('ynmobileview');
			$request -> setControllerName('index');
			$request -> setActionName('login');
		}
	}
	public function onItemCreateAfter($event)
	{
		$session = new Zend_Session_Namespace('mobile');
		if($session -> mobile)
		{
			unset($_SESSION['social_publisher_resource']);
		}
	}
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		// CHECK IF ADMIN
		if (substr($request -> getPathInfo(), 1, 5) == "admin")
		{
			return;
		}

		$mobile = $request -> getParam("mobile", "");
		$session = new Zend_Session_Namespace('mobile');

		if ($mobile == '1')
		{
			$mobile = true;
			$session -> mobile = true;
		}
		elseif ($mobile == '0')
		{
			$mobile = false;
			$session -> mobile = false;
		}
		else
		{
			if (isset($session -> mobile))
			{
				$mobile = $session -> mobile;
			}
			else
			{
				// CHECK TO SEE IF MOBILE
				if (Engine_Api::_() -> ynmobileview() -> isMobile())
				{
					$mobile = true;
					$session -> mobile = true;
				}
				else
				{
					$mobile = false;
					$session -> mobile = false;
				}
			}
		}

		if (!$mobile)
		{
			return;
		}

		$module = $request -> getModuleName();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		if ($action == 'login')
		{
			$request -> setModuleName('ynmobileview');
			$request -> setControllerName('index');
			$request -> setActionName('login');
		}
		elseif ($module == "core")
		{
			if ($controller == "index" && $action == "index")
			{
				$request -> setModuleName('ynmobileview');
				$request -> setControllerName('index');
				$request -> setActionName('index');
			}
		}
		elseif ($module == "user")
		{
			if ($controller == "index" && $action == "home")
			{
				$request -> setModuleName('ynmobileview');
				$request -> setControllerName('index');
				$request -> setActionName('userhome');
			}
			elseif ($controller == "profile" && $action == "index")
			{
				$request -> setModuleName('ynmobileview');
				$request -> setControllerName('index');
				$request -> setActionName('profile');
			}

		}
		elseif ($module == "group")
		{
			if ($controller == "profile" && $action == "index")
			{
				$request -> setModuleName('ynmobileview');
				$request -> setControllerName('group');
				$request -> setActionName('profile');
			}

		}
		elseif ($module == "advgroup")
		{
			if ($controller == "profile" && $action == "index")
			{
				$request -> setControllerName('mobiprofile');
			}

		}
		elseif ($module == "event")
		{
			if ($controller == "profile" && $action == "index")
			{
				$request -> setModuleName('ynmobileview');
				$request -> setControllerName('event');
				$request -> setActionName('profile');
			}
		}
		elseif ($module == "ynevent")
		{
			if ($controller == "profile" && $action == "index")
			{
				$request -> setControllerName('mobiprofile');
			}
		}
		elseif ($module == "ynlistings")
		{
			if ($controller == "index" && $action == "view")
			{
				$request -> setActionName('mobileview');
			}
		}
		elseif ($module == "music")
		{
			if ($controller == "playlist" && $action == "view")
			{
				$request -> setModuleName('ynmobileview');
				$request -> setControllerName('music');
				$request -> setActionName('profile');
			}
		}
		elseif ($module == "activity")
		{
			if ($controller == "notifications")
			{
				$request -> setModuleName('ynmobileview');
				$request -> setControllerName('index');
				$request -> setActionName('notifications');
			}
		}
		elseif ($module == "ynmultilisting")
		{
			if ($controller == "profile" && $action == "index")
			{
				$request -> setActionName('mobile');
			}
		}
		// Create layout
		$layout = Zend_Layout::startMvc();
		// Set options
		$layout -> setViewBasePath(APPLICATION_PATH . "/application/modules/Ynmobileview/layouts", 'Core_Layout_View') -> setViewSuffix('tpl') -> setLayout(null);
	}

}
