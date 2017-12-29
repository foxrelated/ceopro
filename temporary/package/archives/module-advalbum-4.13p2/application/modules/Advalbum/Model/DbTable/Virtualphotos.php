<?php
class Advalbum_Model_DbTable_Virtualphotos extends Engine_Db_Table
{
	protected $_rowClass = 'Advalbum_Model_Virtualphoto';
	
	public function checkPhoto($albumId, $photoId)
	{
		$select = $this
		->select()
		->where("album_id = ?", $albumId)
		->where("photo_id = ?", $photoId);
		$result = $this->fetchAll($select);
		if (count($result))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}