<?php
class Ynwiki_Model_DbTable_ListItems extends Engine_Db_Table
{
  protected $_name = 'ynwiki_listitems';
  protected $_rowClass = 'Ynwiki_Model_ListItem';
  public function getValueById($list_id, $child_id){
  	
  	$select = $this->select()->where('list_id =?', $list_id)->where('child_id=?', $child_id);  			
	$results = $this->fetchAll($select);
	return $results;
  }
}