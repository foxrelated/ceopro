<?php
class Ynmember_Model_Studyplace extends Core_Model_Item_Abstract {
	function isViewable() {
        return $this->authorization()->isAllowed(null, 'view'); 
    }
}