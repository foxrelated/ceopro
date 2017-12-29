<?php
class Ynmember_Widget_MembersListingController extends Engine_Content_Widget_Abstract
{
 	public function indexAction()
 	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
 		$params = $this -> _getAllParams();
 		$mode_list = $mode_grid = $mode_pin = $mode_map = 1;
		$mode_enabled = array();
		$view_mode = 'list';
		if(isset($params['mode_list']))
		{
			$mode_list = $params['mode_list'];
		}
		if($mode_list)
		{
			$mode_enabled[] = 'list';
		}
		
		if(isset($params['mode_grid']))
		{
			$mode_grid = $params['mode_grid'];
		}
		if($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		
		if(isset($params['mode_pin']))
		{
			$mode_pin = $params['mode_pin'];
		}
		if($mode_pin)
		{
			$mode_enabled[] = 'pin';
		}
		
		if(isset($params['mode_map']))
		{
			$mode_map = $params['mode_map'];
		}
		if($mode_map)
		{
			$mode_enabled[] = 'map';
		}
		
		if(isset($params['view_mode']))
		{
			$view_mode = $params['view_mode'];
		}
		
		if($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}
		$this -> view -> mode_enabled = $mode_enabled;
		switch ($view_mode)
		{
			case 'grid':
				$class_mode = "ynmember-browse-viewmode-grid";
				break;
			case 'pin':
				$class_mode = "ynmember-browse-viewmode-pinterest";
				break;	
			case 'map':
				$class_mode = "ynmember-browse-viewmode-maps";
				break;
			default:
				$class_mode = "ynmember-browse-viewmode-list";
				break;
		}

	  	//Setup params
	  	$request = Zend_Controller_Front::getInstance()->getRequest();
	    $params = $request->getParams();
 		if (!isset($params['page']) || $params['page'] == '0')
			$page = 1;
		else
			$page = (int)$params['page'];
		$form = new Ynmember_Form_Search(array(
			'type' => 'user'
		));
		if ($form->isValid($params)) {
			$params = $form -> getValues();
		}
		else {
			$params = array();
		}
		$this->view->formValues = array_filter($params);
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		$key = $controller.'_'.$action;
		if ($key == 'member_myfriend')
		{
			$params['show'] = 'friend';
			if (!$viewer->getIdentity())
			{
				return $this->setNoRender();
			}
		}
		//Set curent page
		$paginator = Engine_Api::_()->ynmember()->getMemberPaginator($params);
		$limit = $this->_getParam('itemCountPerPage', 15);
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page );
 		$userIds = array();
		foreach ($paginator as $u){
			$userIds[] = $u -> getIdentity();
		}
		
		// Load fields view helpers
		$view = $this->view;
		$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this->view->class_mode = $class_mode;
		$this->view->paginator = $paginator;
		$this->view->userIds = implode("_", $userIds);
	    $this->view->totalUsers = $paginator->getTotalItemCount();
	    $this->view->userCount = $paginator->getCurrentItemCount();
	}
}