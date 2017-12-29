<?php

class Ynmultilisting_CompareController extends Core_Controller_Action_Standard {
    public function indexAction() {
        $this -> _helper -> content -> setEnabled();
        if (Engine_Api::_()->ynmultilisting()->isMobile()) {
            $this -> _helper -> content -> setNoRender();
        }
        $category_id = $this->_getParam('category_id', 0);
        $category = Engine_Api::_()->getItem('ynmultilisting_category', $category_id);
		$listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if (!$category_id || !$category || !$listingtype->hasCategory($category_id)) {
            return $this->_helper->requireSubject()->forward();
        }
        Engine_Api::_()->ynmultilisting()->addCompareCategory($category_id);
        $this->view->category = $category;
        $this->view->comparison = $comparison = $category->getComparison();
        $this->view->listings = Engine_Api::_()->ynmultilisting()->getCompareListingsOfCategory($category_id);
        $this->view->prevCategory = Engine_Api::_()->ynmultilisting()->getPrevCategory($category_id);
        $this->view->nextCategory = Engine_Api::_()->ynmultilisting()->getNextCategory($category_id);
		
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
    }
    
    public function removeListingAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        $category_id = $this->_getParam('category_id');
        if (!$id) {
            echo Zend_Json::encode(array('status' => false, 'count' => 0));
            return true;
        }
        $count = Engine_Api::_()->ynmultilisting()->removeComparelisting($id, $category_id);
        if ($count === false) {
            echo Zend_Json::encode(array('status' => false, 'count' => 0));
            return true;
        }
        else {
            echo Zend_Json::encode(array('status' => true, 'count' => $count));
            return true;
        }
    }
    
    public function removeCategoryAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if (!$id) {
            echo Zend_Json::encode(array('status' => false, 'count' => 0));
            return true;
        }
        $count = Engine_Api::_()->ynmultilisting()->removeCompareCategory($id);
        echo Zend_Json::encode(array('status' => true, 'count' => $count));
        return true;
    }
    
    public function sortAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $order = explode(',', $this->getRequest()->getParam('order'));
        $category_id = $this->getRequest()->getParam('category_id');
        if (!$category_id) return false;
        $newArr = array();
        foreach( $order as $i => $item ) {
            $field_id = substr($item, strrpos($item, '_') + 1);
            if (!empty($field_id))
                array_push($newArr, $field_id);
        }
        Engine_Api::_()->ynmultilisting()->updateCompareCategory($category_id, $newArr);
        return true;
    }
}
