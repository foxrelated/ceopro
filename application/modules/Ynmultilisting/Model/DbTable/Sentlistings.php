<?php
class Ynmultilisting_Model_DbTable_Sentlistings extends Engine_Db_Table {
	
	public function getListingIdsByEmail($email)
	{
		return $this -> select() -> from($this, 'listing_id') -> where('email = ?', $email) -> query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);
	}
	
	public function deleteRowsByEmail($email)
	{
		$rows = $this -> fetchAll($this -> select() -> where('email = ?', $email));
		foreach($rows as $delete_row)
		{
			$delete_row -> delete();
		}
	}
}
