<?php
class Ynmultilisting_Plugin_Menus {
    public function canCreateListing() {
        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer || !$viewer->getIdentity() ) {
            return false;
        }
		
		$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		if (!$listingtype_id) {
			return false;
		}
		
		$listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if( !$listingtype->checkPermission($viewer, 'ynmultilisting_listing', 'create')) {
            return false;
        }

        return true;
    }
	
	public function hasListingType() {
		
		$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		if (!$listingtype_id) {
			return false;
		}

        return true;
    }
	
	public function canManageWishlist() {
		$viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer || !$viewer->getIdentity() ) {
            return false;
        }
		
		return true;
	}
}