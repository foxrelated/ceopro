<?php
class Ynmultilisting_Model_Module extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
    
    public function getTitle() {
        $view = Zend_Registry::get('Zend_View');
        return $view->translate(parent::getTitle());
    }
}