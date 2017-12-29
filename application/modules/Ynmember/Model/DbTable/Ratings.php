<?php

class Ynmember_Model_DbTable_Ratings extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmember_Model_Rating';
	protected $_name = 'ynmember_ratings';
	
	public function getRowRatingThisType($rating_type, $resource_id, $user_id , $review_id)
	{
		$select = $this -> select() -> where('resource_id = ?', $resource_id) 
									-> where('user_id = ?', $user_id) 
									-> where('rating_type = ?', $rating_type)
									-> where('review_id = ?', $review_id)
									-> limit(1);
		$row = $this -> fetchRow($select);
		if($row)
		{
			return $row;
		}
		return false;
	}
	
	public function getRateResouce($resource_id)
	{
		$select = $this-> select() -> where('resource_id = ?', $resource_id) -> where('rating_type = 0');
		$rows = $this -> fetchAll($select);
		$count = 0;
		$total = 0;
		foreach($rows as $row)
		{
			$count++;
			$total += $row -> rating;
		}
		$rate = round(($total/$count), 1);
		return $rate;
	}
	
	public function getGeneralRatingOfReview($review)
	{
		$select = $this -> select() -> where('rating_type = 0') -> where('review_id = ?', $review -> getIdentity()) -> limit(1);
		$row = $this -> fetchRow($select);
		return $row -> rating;
	}
	
	public function getRatingOfType($type_id, $resource_id)
	{
		$select = $this -> select() -> where('resource_id = ?', $resource_id) 
									-> where('rating_type = ?', $type_id);
		$rows = $this -> fetchAll($select);
		$count = 0;
		$total = 0;
		foreach($rows as $row)
		{
			$count++;
			$total += $row -> rating;
		}
		$rate = round(($total/$count), 1);
		return $rate;
	}
}
