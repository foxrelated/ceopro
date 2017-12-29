<?php
class Ynmultilisting_Plugin_Task_CheckExpiredListings extends Core_Plugin_Task_Abstract {
	public function execute() {
		$now = date("Y-m-d H:i:s");
		$listingTable = Engine_Api::_()->getItemTable('ynmultilisting_listing');
		$select = $listingTable -> select() 
		  -> where("status IN (?)", array('open', 'closed'))
		  -> where("expiration_date < '$now'");
		$listings = $listingTable -> fetchAll($select);
		if (count($listings)) {
			foreach ($listings as $listing) {
				
				$listing -> status = 'expired';
				$listing -> save();
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
        		$notifyApi -> addNotification($listing -> getOwner(), $listing, $listing, 'ynmultilisting_listing_status_change', array('status' => 'expired'));
            	
				//send email
				$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
				$params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
				$href =  				 
					'http://'. @$_SERVER['HTTP_HOST'].
					Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $listing -> getIdentity(), 'slug' => $listing -> getSlug()),'ynmultilisting_profile',true);
				$params['listing_link'] = $href;	
				$params['listing_name'] = $listing -> getTitle();
				try{
					Engine_Api::_()->getApi('mail','ynmultilisting')->send($listing -> getOwner(), 'ynmultilisting_listing_expired',$params);
				}
				catch(exception $e)
				{
					//keep silent
				}
				
			}
		}
	}
}