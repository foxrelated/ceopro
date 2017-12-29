<?php

class Ynmember_Model_DbTable_Ratingtypes extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmember_Model_Ratingtype';
	protected $_name = 'ynmember_ratingtypes';
	
	public function getAllRatingTypes(){
		$select = $this -> select();
		return $this -> fetchAll($select);
	}
	
}
