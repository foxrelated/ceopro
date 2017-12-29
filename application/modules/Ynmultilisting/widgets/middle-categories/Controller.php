<?php
class Ynmultilisting_Widget_MiddleCategoriesController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
    	$listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        $view_mode = $listingtype->category_widget;
        $listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$params = $request->getParams();
		
		$from = $request-> getParam('from', 0);
		$from = intval($from);
		
		$fullParams = array_merge($params, $this->_getAllParams());
		$limit = (!empty($fullParams['itemCountPerPage'])) ? $fullParams['itemCountPerPage'] : 10;
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting') -> getListingTypeCategoriesLevel1($listingtype_id, $from, $limit);
        $this->view->categories = $categories;
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
		
		$this->view->view_mode = $view_mode;
		$this->view->from = $from;
		$this->view->limit = $limit;

        $session = new Zend_Session_Namespace('mobile');
        if ($session -> mobile) {
            $this->setNoRender();
            return;
        }
    }
}

