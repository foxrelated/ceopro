<?php
class Ynadvsearch_Widget_SearchResultsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$values = Zend_Controller_Front::getInstance ()->getRequest ()->getParams ();
		$this->view->values = $values;
		$text = '';
		$search = '';
        
		if (isset ( $values ['submit'] ) && isset ( $values ['query'] ) && $values ['query'] != '') {
			$text = $values ['query'];
			$this->view->query = $text;
		} elseif (isset ( $values ['is_search'] ) && $values ['is_search'] == 1 && isset ( $values ['query'] ) && $values ['query'] != '') {
			if (isset ( $values ['is_hashtag'] ) && $values ['is_hashtag'] == 1) {

				$text = str_replace ( '#', '', $values ['query'] );
			} else {
				$text = $values ['query'];
			}
			$this->view->query = $text;
		} elseif (isset ( $values ['phrase'] ) && $values ['phrase'] != '') {
			$text = $values ['phrase'];
			$this->view->query = $text;
		}
		// $text = $values['query'];
		if ($text == '') {
			$this->view->noSearchQuery = 1;
			return;
		}
		if (isset ( $values ['search'] ) && $values ['search'] != '' && ! (isset ( $values ['listmods'] ) && $values ['listmods'] != '')) {
			$search = $values ['search'];
		} elseif (isset ( $values ['listmods'] ) && $values ['listmods'] != '') {
			$listmods = explode ( ',', $values ['listmods'] );
		} else {
			$search = 'all';
		}
		$this->view->search = $search;
		$searchModulesTable = new Ynadvsearch_Model_DbTable_Modules ();
		if (isset ( $values ['from'] ) && $values ['from'] != '') {
			$from = $values ['from'];
		} else {
			$from = '0';
		}
		if (isset ( $values ['type'] ) && $values ['type'] == 'ajax') {
			$search = $values ['search'];
		}
		$limit = 10;
		$this->view->limit = $limit;
		$results = array ();
		if ($search == 'all') {
			$type = $searchModulesTable->getAllEnabledTypes ();
			$modules = $searchModulesTable->getEnabledModules ();
			foreach ( $modules as $module ) {
				$moduleType = $searchModulesTable->getTypes ( $module->name );
				$search_results = Engine_Api::_()->getApi('search', 'ynadvsearch')->getResults ( $text, $moduleType, $from, $limit );
				if (count ( $search_results ) > 0) {
					$results [$module->name] = $search_results;
				}
			}
		}
		else {
			$type = $searchModulesTable->getTypes ( $search );
			$this->view->mod = $search;
			$type = $searchModulesTable->getTypes ( $search );
			$search_results = Engine_Api::_()->getApi('search', 'ynadvsearch')->getResults ( $text, $type, $from, $limit );
			if (count ( $search_results ) > 0) {
				$results [$search] = $search_results;
			}
			if (count ( $search_results ) < 11) {
				$this->view->endResult = 1;
			}
		}
		if (@$listmods && count ( $listmods ) > 0 && $search != 'all') {
			$results = array ();
			foreach ( $listmods as $mod ) {
				$moduleType = $searchModulesTable->getTypes ( $mod );
				$search_results = Engine_Api::_()->getApi('search', 'ynadvsearch')->getResults ( $text, $moduleType, $from, $limit );
				if (count ( $search_results ) > 0) {
					$results [$mod] = $search_results;
				}
			}
		}
        
		$this->view->from = $from;
		if (count ( $results ) <= 0) {
			$this->view->noResult = 1;
			$this->view->endResult = 1;
		}
        
		$this->view->results = $results;
		if (isset($values['isViewMore']) && $values['isViewMore'] == 1) {
			$this->view->isViewMore = $values['isViewMore'];
		}
	}
}
