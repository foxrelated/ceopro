<?php

class Ynadvsearch_Widget_ModulesListController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
	  	$searchModulesTable = new Ynadvsearch_Model_DbTable_Modules;
    	$modulesObject = $searchModulesTable->getEnabledModules();
    	$this->view->modules = $modulesObject;
    	$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    	if (isset($params['is_all']) && $params['is_all'] == 'all') {
    		$this->view->is_all = 1;
    	}
    	if (isset($params['query']) && $params['query'] != '') {
    		$this->view->query = $params['query'];
    	}
    	elseif (isset($params['phrase']) && $params['phrase'] != '') {
    		$this->view->query = $params['phrase'];
    	}
		if (isset($params['listmods']) && $params['listmods'] != '') {
			$listmods = $params['listmods'];
			$mods_array = explode(',',$listmods);
			if($mods_array)
		      {
		        $this->view->mods_array = $mods_array;
		      }
		      else{
		        $this->view->mods_array = array();
		      }
			$this->view->listmods = $listmods;
			$this->view->search = $params['search'];
		}
    	elseif (isset($params['search']) && $params['search'] != '') {
    		$this->view->search = $params['search'];
    		$this->view->listmods = $params['search'];
    	}
    	else {
    		$this->view->search = 'all';

    	}

	}
}
