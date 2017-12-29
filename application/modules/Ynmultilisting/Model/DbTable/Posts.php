<?php
class Ynmultilisting_Model_DbTable_Posts extends Engine_Db_Table
{
  protected $_name = 'ynmultilisting_posts';
  protected $_rowClass = 'Ynmultilisting_Model_Post';
  
  public function getPostsCount($listingTypeId = null)
  {
  	$select = $this -> select();
	if($listingTypeId)
	{
		$tableListing = Engine_Api::_() -> getItemTable('ynmultilisting_listing');
		$listings = $tableListing -> getListingTypeListings($listingTypeId);
		$arrIDs = array();
		foreach($listings as $listing)
		{
			$arrIDs[] = $listing -> getIdentity();
		}
		if(count($arrIDs))
		{
			$select -> where('listing_id IN (?)', $arrIDs);
		}
		else
		{
			$select -> where("1 = 0");
		}
	}
  	return count($this->fetchAll($select));
  }  
}