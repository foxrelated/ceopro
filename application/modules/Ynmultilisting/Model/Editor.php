<?php
class Ynmultilisting_Model_Editor extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
	
	public function getListingType()
	{
		return Engine_Api::_() -> getItem('ynmultilisting_listingtype', $this -> listingtype_id);
	}
}