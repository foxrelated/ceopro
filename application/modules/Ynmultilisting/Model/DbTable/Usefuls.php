<?php

class Ynmultilisting_Model_DbTable_Usefuls extends Engine_Db_Table
{
	protected $_rowClass = 'Ynmultilisting_Model_Useful';
	protected $_name = 'ynmultilisting_usefuls';
	
	public function getUseFul($userId, $reviewId)
	{
		$select =  $this->select()->where("user_id = ?", $userId)->where("review_id = ?", $reviewId)->limit(1);
		return $this->fetchRow($select);
	}
}
