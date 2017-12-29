<?php
class Ynmultilisting_Model_Wishlist extends Core_Model_Item_Abstract {
    public function getAllListings() {
        $listing_ids = $this->getListingIds();
		if (empty($listing_ids)) return array();
		$table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
		$tableName = $table->info('name');
		if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
			$select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
		} else {
			$select = $table->select()->from("$tableName", array("$tableName.*"));
		}
		$select->where("listing_id IN (?) AND deleted = 0 AND search = 1 AND status = 'open' AND approved_status = 'approved'", $listing_ids);
		return $table->fetchAll($select);
	}
	
	public function getListingIds() {
		$wishlistListings = Engine_Api::_()->getDbTable('wishlistlistings', 'ynmultilisting')->getWishlistListings($this->getIdentity());
    	$listing_ids = array();
		foreach ($wishlistListings as $row) {
			$listing_ids[] = $row->listing_id;
		}
		return $listing_ids;
	}
	
	function isViewable() {
        return $this->authorization()->isAllowed(null, 'view'); 
    }
	
	function hasListings() {
		$listings = $this->getAllListings();
		return (count($listings)) ? true : false;
	}
	
	public function getHref($params = array()) {
		$params = array_merge(array(
			'route' => 'ynmultilisting_wishlist',
			'action' => 'view',
			'reset' => true,
			'id' => $this -> getIdentity()
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
	
	public function addToWishList($wishlist_id) {
		//clone list of listings
		Engine_Api::_()->getDbTable('wishlistlistings', 'ynmultilisting')->addMultiListings($wishlist_id, $this->getListingIds());
	}
	
	public function getPhotoUrl($type = null) {
	    $listing = $this->getFirstListing();
		
		if ($listing) {
			return $listing->getPhotoUrl($type);
		}
		$view = Zend_Registry::get("Zend_View");
		$photoUrl = $view->baseUrl().'/application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png';
		return $photoUrl;
  	}
	
	public function getFirstListing() {
		return Engine_Api::_()->getDbTable('wishlistlistings', 'ynmultilisting')->getFirstListingOfWishlist($this->getIdentity());
	}
	
	public function removeListing($listing_id) {
		Engine_Api::_()->getDbTable('wishlistlistings', 'ynmultilisting')->removeWishlistListing($this->getIdentity(), $listing_id);
	}
}