<?php

class Ynmember_Model_DbTable_Features extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmember_Model_Feature';
	protected $_name = 'ynmember_features';
	
	public function getFeatureRowByUserId($user_id)
	{
		$select = $this-> select() -> where('user_id = ?', $user_id) -> limit(1);
		return $this->fetchRow($select);
	}
	
	public function getFeaturedUserId()
	{
		$select = $this->select()->where("active = 1");
		$features = $this->fetchAll($select);
		$userIds = array();
		foreach ($features as $feature){
			$userIds[] = $feature->user_id;
		}
		return $userIds;
	}
	
	public function isFeatured($user)
	{
		if (is_null($user))
		{
			return false;
		}
		$userId = $user->getIdentity();
		if (!$userId)
		{
			return false;
		}
		$select = $this-> select() -> where('user_id = ?', $userId) -> limit(1);
		$featureRow = $this->fetchRow($select);
		if (is_null($featureRow))
		{
			return false;
		}
		else 
		{
			if ($featureRow->active == '1')
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
	}
}
