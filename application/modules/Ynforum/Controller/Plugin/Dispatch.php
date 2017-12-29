<?php

class Ynforum_Controller_Plugin_Dispatch extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $request -> getModuleName();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();

		$key = 'forum_predispatch_url:' . $module . '.' . $controller . '.' . $action;
		
		if (isset($_SESSION[$key]) && $_SESSION[$key])
		{
			$url = $_SESSION[$key];
			header('location:' . $url);
			unset($_SESSION[$key]);
			unset($_SESSION['ynforum']['parent_id']);	
			
			@session_write_close();
			exit ;
		}
		
	}

}
