<?php

class Ynmultilisting_Model_Package extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
    
    public function getTitle() {
        return $this->title;
    }
    
	public function getPrice(){
		$view = Zend_Registry::get('Zend_View');
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'); 
		return ($view -> locale()->toCurrency($this->price, $currency));
	}
	
    public function isViewable() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer -> getIdentity() == $this -> getOwner() -> getIdentity())
        {
            return true;
        }
        $table = Engine_Api::_() -> getDbtable('levels', 'authorization');
        $level = $table -> fetchRow($table -> select() -> where('level_id = ?', $viewer -> level_id));
        $auth = Engine_Api::_() -> authorization() -> context;
        return $auth -> isAllowed($this, $level, 'view');
    }
    
	public function getAvailableFeatures() {
        $title = array();
        $view = Zend_Registry::get('Zend_View');
        if ($this->allow_photo_tab) {
            array_push($title, $view->translate('Allow adding photos to Photos tab'));
        }
        if ($this->allow_video_tab) {
            array_push($title, $view->translate('Allow adding videos to Videos tab'));
        }
        if ($this->allow_discussion_tab) {
            array_push($title, $view->translate('Allow adding discussions to Discussions tab'));
        }
        return $title;
    }
	
	public function getAllListings(){
		$tableListing = Engine_Api::_() -> getItemTable('ynmultilisting_listing');
		$select = $tableListing -> select() 
								-> where('status IN (?)', array('open','closed'))
								-> where('approved_status = ?', 'approved')
								-> where('package_id = ?', $this -> getIdentity());
		return $tableListing -> fetchAll($select);
	}
}