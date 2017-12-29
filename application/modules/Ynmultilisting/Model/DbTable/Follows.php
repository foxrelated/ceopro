<?php
class Ynmultilisting_Model_DbTable_Follows extends Engine_Db_Table
{
  	protected $_name = 'ynmultilisting_follows';
	public function getRow($user_id, $owner_id, $isActive = null)
	{
		if(empty($isActive))
		{
			$isActive = false;
		}
		$select = $this-> select();
		$select -> where('user_id = ?', $user_id);
		$select -> where('owner_id = ?', $owner_id);
		if($isActive)
		{
			$select -> where('status = ?', '1');
		}
		$select -> limit(1);
		return $this->fetchRow($select);
	}
}