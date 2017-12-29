<?php

class Ynmember_Model_DbTable_Liveplaces extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmember_Model_Liveplace';
	protected $_name = 'ynmember_liveplaces';
	
	public function getLiveCurrentPlacesByUserId($user_id)
	{
		$select = $this-> select() -> where('current = 1') -> where('user_id = ?', $user_id) ;
		return $this->fetchAll($select);
	}
	
	public function getLivePastPlacesByUserId($user_id)
	{
		$select = $this-> select() -> where('current = 0') -> where('user_id = ?', $user_id);
		return $this->fetchAll($select);
	}
	
	public function getLiveAllPlacesByUserId($user_id)
	{
		$select = $this-> select() -> where('user_id = ?', $user_id);
		return $this->fetchAll($select);
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
			foreach ($this->fetchAll($select) as $live)
			{
				if ( $live->isViewable())
				{
					if (!in_array($live->user_id, $userIds))
					{
						$userIds[] = $live->user_id; 
					}	
				}
			}
			return $userIds;
		}
		return array(); 
	}
	
	public function getLocations($base_lat, $base_lng, $target_distance)
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
			$result = array();
			$userIds = array();
			foreach ($this->fetchAll($select) as $live)
			{
				if ( $live->isViewable())
				{
					if (!in_array($live->user_id, $userIds))
					{
						$userIds[] = $live->user_id;
						$result[$live->user_id] = array(
							'latitude' => $live -> latitude,
							'longitude' => $live -> longitude,
							'distance' => $live -> distance,
						); 
					}	
				}
			}
			return $result;
		}
		return array(); 
	}
    public function getAllLocations()
    {
        $tableName = $this->info('name');
        $select = $this ->select()->distinct();
        $select
            -> from("$tableName")
            -> where("current = ?", '1');
        $result = array();
        $userIds = array();
        foreach ($this->fetchAll($select) as $live)
        {
            if ( $live->isViewable())
            {
                if (!in_array($live->user_id, $userIds))
                {
                    $userIds[] = $live->user_id;
                    $result[$live->user_id] = array(
                        'latitude' => $live -> latitude,
                        'longitude' => $live -> longitude,
                    );
                }
            }
        }
        return $result;
    }
}
