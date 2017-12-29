<?php
class Ynmultilisting_Model_DbTable_Features extends Engine_Db_Table {
	public function getFeatureRowByListingId($listing_id)
	{
		$select = $this-> select() -> where('listing_id = ?', $listing_id) -> limit(1);
		return $this->fetchRow($select);
	}
}