<?php
class Ynmobileview_Model_DbTable_MenuItems extends Engine_Db_Table
{
  protected $_serializedColumns = array('params');
	public function checkAndAdd($item)
	{
		$select = $this -> select() -> where('name = ?', $item -> name)-> limit(1);
		if(!$this-> fetchRow($select))
		{
			$arr_item = $item -> toArray();
			unset($arr_item['id']);
			$row = $this -> createRow();
			$row -> setFromArray($arr_item);
			$row -> save();
		}	
	}
}