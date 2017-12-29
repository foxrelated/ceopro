<?php
class Ynmultilisting_Model_Quicklink extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getTotalListings() {
        return count($this->getListings(array()));
    }
    
    public function getListingType() {
        return Engine_Api::_()->getItem('ynmultilisting_listingtype', $this->listingtype_id);
    }
	
	public function getListings($params = array()) {
		$table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
		$select = $this->getListingsSelect($params);
		return $table->fetchAll($select);
	}
	
	public function getListingsSelect($params = array()) {
		$params['listingtype_id'] = $this->listingtype_id;
		if ($this->category_ids)
			$params['category_ids'] = $this->category_ids;
		if (empty($params['category_ids'])) $params['category_ids'] = array(0);
		$params['long'] = $this->longitude;
		$params['lat'] = $this->latitude;
		$params['within'] = $this->radius;
        if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi') == 'km')
            $params['within'] *= Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.mi.to.km', 0.621371);
		if ($this->owner_ids)
			$params['owner_ids'] = $this->owner_ids;
		if ($this->listing_ids)
			$params['listing_ids'] = $this->listing_ids;
		if ($this->price)
			$params['price'] = $this->price;
		if ($this->expire_from)
			$params['expire_from'] = $this->expire_from;
		if ($this->expire_to)
			$params['expire_to'] = $this->expire_to;
		$params['search'] = 1;
		$params['status'] = 'open';
		$params['approved_status'] = 'approved';
		$table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
		$select = $table->getListingsSelect($params);
		return $select;
	}
	
	public function getHref($params = array()) {
		$params = array_merge(array(
			'route' => 'ynmultilisting_general',
			'action' => 'browse',
			'reset' => true,
			'quicklink_id' => $this -> getIdentity(),
			'listingtype_id' => $this -> listingtype_id,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
}