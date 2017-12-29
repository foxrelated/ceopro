<?php
class Advalbum_Model_DbTable_Photocolors extends Engine_Db_Table
{
	protected $_rowClass = 'Advalbum_Model_Photocolor';
	
	public function getPhotoByColor($color)
	{
		$name = $this->info("name");
		$photoTbl = Engine_Api::_()->getDbTable("photos", "advalbum");
		$photoTblName = $photoTbl->info("name");
		$select = $this->select() -> setIntegrityCheck(false)
			-> from ($name)
			-> join ($photoTblName, "$name.photo_id = $photoTblName.photo_id", "$photoTblName.album_id")
			-> where ("$name.color_title = ?", $color)
			-> order (array("$photoTblName.album_id", "$name.pixel_count"))
		;
		return $this->fetchAll($select);
	}
	
	public function getVirtualPhotoByColor($color)
	{
		$name = $this->info("name");
		$photoTbl = Engine_Api::_()->getDbTable("virtualphotos", "advalbum");
		$photoTblName = $photoTbl->info("name");
		$select = $this->select() -> setIntegrityCheck(false)
			-> from ($name)
			-> join ($photoTblName, "$name.photo_id = $photoTblName.photo_id", "$photoTblName.album_id")
			-> where ("$name.color_title = ?", $color)
			-> order (array("$photoTblName.album_id", "$name.pixel_count"))
		;
		return $this->fetchAll($select);
	} 
}