<?php
class Ynmultilisting_Model_DbTable_Wishlistlistings extends Engine_Db_Table {
    public function getWishlistListings($wishlist_id) {
        $select = $this->select()->where('wishlist_id = ?', $wishlist_id);
        $listings = $this->fetchAll($select);
        return $listings;
    }
	
	public function hasWishlistlisting($listing_id, $wishlist_id) {
		$select = $this->select()->where('listing_id = ?', $listing_id)->where('wishlist_id = ?', $wishlist_id);
		$result = $this->fetchRow($select);
		return ($result) ? true : false;
	}
	
	public function addListingToWishlist($listing_id, $wishlist_id) {
		$row = $this->createRow();
		$row->listing_id = $listing_id;
		$row->wishlist_id = $wishlist_id;
		$row->save();
	}
	
	public function addMultiListings($wishlist_id, $listing_ids) {
		if (!$wishlist_id || empty($listing_ids)) return;
		foreach ($listing_ids as $id) {
			$this->addListingToWishlist($id, $wishlist_id);
		}
	}
	
	public function getFirstListingOfWishlist($wishlist_id) {
		$listings = $this->getWishlistListings($wishlist_id);
		foreach ($listings as $wishlistListing) {
			$listing = Engine_Api::_()->getItem('ynmultilisting_listing', $wishlistListing->listing_id);
			if ($listing) {
				return $listing;
			}
		}
		
		return null;
	}
	
	public function removeWishlistListing($wishlist_id, $listing_id) {
		$where = array(
            $this->getAdapter()->quoteInto('wishlist_id = ?', $wishlist_id),
            $this->getAdapter()->quoteInto('listing_id = ?', $listing_id)
        );
		
		$this->delete($where);
	}
}