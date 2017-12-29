<?php

class Ynmultilisting_Model_DbTable_Reviewvalues extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmultilisting_Model_Reviewvalue';
	protected $_name = 'ynmultilisting_reviewvalues';
	
	public function getRowReviewThisType($reviewtype_id, $review_id)
	{
		$select = $this -> select()
						-> where('reviewtype_id = ?', $reviewtype_id)
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
