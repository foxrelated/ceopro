<?php
class Ynmultilisting_Plugin_Task_CheckFeaturedListings extends Core_Plugin_Task_Abstract {
	public function execute() {
		$now = date("Y-m-d H:i:s");
		$featureTbl = Engine_Api::_()->getDbTable('features', 'ynmultilisting');
		$select = $featureTbl -> select() 
		-> where("active = ? ", '1')
		-> where("expiration_date < '$now'")
		;
		$features = $featureTbl -> fetchAll($select);
		if (count($features))
		{
			foreach ($features as $feature)
			{
				$listing = Engine_Api::_()->getItem('ynmultilisting_listing', $feature->listing_id);
				if (!is_null($listing))
				{
					$listing -> featured = 0;
					$listing -> feature_expiration_date = null;
					$listing -> save();
				}
				$feature -> active = 0;
                $feature -> expiration_date = null;
				$feature -> save();
				
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($listing -> getOwner(), $listing, $listing, 'ynmultilisting_listing_status_change', array('status' => 'un-featured'));
			}
		}
	}
}