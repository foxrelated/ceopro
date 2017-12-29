<?php

class Ynmultilisting_Controller_Plugin_Dispatch extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $request -> getModuleName();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		
		if ($module == 'ynmultilisting') {
            //for set current listing type
			
            $current_listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
            $listingtype_id = $request->getParam('listingtype_id', 0);
			$newListingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
            if ($listingtype_id && $newListingtype && $newListingtype->show && ($current_listingtype_id != $listingtype_id)) {
                Engine_Api::_()->ynmultilisting()->setCurrentListingType($listingtype_id);
            }
			
			$toAdmin = (strpos($controller, 'admin-') === 0);
			$listingType = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
			if (!$listingType && !$toAdmin) {
				$view = Zend_Registry::get('Zend_View');
				$url = $view->url(array(), 'user_general', true);
				header('location:' . $url);
				exit;
			}
        }
		
		$key = 'ynmultilisting_predispatch_url:' . $module . '.' . $controller . '.' . $action;
		if (isset($_SESSION[$key]) && $_SESSION[$key]) {
			$url = $_SESSION[$key];
			header('location:' . $url);
			unset($_SESSION[$key]);
			@session_write_close();
			exit ;
		}
	}
}
