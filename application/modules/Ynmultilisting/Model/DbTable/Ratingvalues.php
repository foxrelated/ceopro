<?php

class Ynmultilisting_Model_DbTable_Ratingvalues extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmultilisting_Model_Ratingvalue';
	protected $_name = 'ynmultilisting_ratingvalues';
	
	public function getRatingOfType($ratingtype_id, $listing_id)
	{
		$select = $this -> select() -> where('listing_id = ?', $listing_id) 
									-> where('ratingtype_id = ?', $ratingtype_id);
		$rows = $this -> fetchAll($select);
		$count = 0;
		$total = 0;
		foreach($rows as $row)
		{
			$count++;
			$total += $row -> rating;
		}
		$rate = (count($rows)) ? round(($total/$count), 1) : 0;
		return $rate;
	}
	
	public function getRowRatingThisType($ratingtype_id, $review_id)
	{
		$select = $this -> select()
						-> where('ratingtype_id = ?', $ratingtype_id)
						-> where('review_id = ?', $review_id)
						-> limit(1);
		$row = $this -> fetchRow($select);
		if($row)
		{
			return $row;
		}
		return false;
	}
	
	public function deleteReview($review_id)
	{
		$select = $this -> select()
						-> where('review_id = ?', $review_id);
		$rows = $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$row -> delete();
		}			
		return true;
	}
}	
