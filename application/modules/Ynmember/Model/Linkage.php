<?php 
class Ynmember_Model_Linkage extends Core_Model_Item_Abstract 
{
	function isViewable($user = null) 
	{
        return $this->authorization()->isAllowed($user, 'view'); 
    }
}