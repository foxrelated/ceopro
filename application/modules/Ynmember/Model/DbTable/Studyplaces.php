<?php

class Ynmember_Model_DbTable_Studyplaces extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmember_Model_Studyplace';
	protected $_name = 'ynmember_studyplaces';
	
	public function getStudyPlacesByUserId($user_id)
	{
		$select = $this-> select() -> where('user_id = ?', $user_id) -> order('current DESC');
		return $this->fetchAll($select);
	}
	
	public function getCurrentStudyPlacesByUserId($user_id)
	{
		$select = $this-> select() -> where('user_id = ?', $user_id) -> where('current = 1') -> limit(1);
		return $this->fetchRow($select);
	}
	
	public function getStudyPlaceArrayByUserId($user_id)
	{
		$select = $this-> select() -> where('user_id = ?', $user_id);
		$workPlaces = $this->fetchAll($select);
		$result = array();
		foreach ($workPlaces as $work)
		{
			$result[] = $work->company;
		}
		return $result;
	}
	
	public function getUserIdByLocation($base_lat, $base_lng, $target_distance)
	{
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
			$tableName = $this->info('name');
			$select = $this ->select()->distinct();
			$select 
			-> from("$tableName", array("$tableName.*", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance"))
			-> where("latitude <> ''")
			-> where("longitude <> ''")
			-> where("current = ?", '1')
			-> having("distance <= $target_distance")
			-> order("distance ASC");
			$userIds = array();
			foreach ($this->fetchAll($select) as $study)
			{
				if ( $study->isViewable())
				{
					if (!in_array($study->user_id, $userIds))
					{
						$userIds[] = $study->user_id; 
					}	
				}
			}
			return $userIds;
		}
		return array(); 
	}
}
