<?php
class Ynmember_Widget_BrowseMembersController extends Engine_Content_Widget_Abstract
{
 	public function indexAction()
 	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
	  	//Setup params
	  	$request = Zend_Controller_Front::getInstance()->getRequest();
	    $params = $request->getParams();
		$form = new Ynmember_Form_Search(array(
			'type' => 'user'
		));
		if ($form->isValid($params)) {
			$params = $form -> getValues();
		}
		else
			$params = array();
		$this -> view -> formValues = array_filter($params);
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
		else if ($key == 'member_feature')
		{
			$params['show'] = 'featured';
		}
		else if ($key == 'member_rating')
		{
			$params['order'] = 'most_rating';
		}

		$paginator = Engine_Api::_()->ynmember()->getMemberPaginator($params);
		$limit = $this->_getParam('itemCountPerPage', 15);
		if (!isset($params['page']) || $params['page'] == '0')
		{
			$page = 1;
		}
		else
		{
			$page = (int)$params['page'];
		}
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page);
		
		// Load fields view helpers
		$view = $this->view;
		$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this -> view -> paginator = $paginator;
	}

}