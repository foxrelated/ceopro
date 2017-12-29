<?php
class Ynwiki_Model_DbTable_Lists extends Engine_Db_Table
{
  protected $_name = 'ynwiki_lists';
  protected $_rowClass = 'Ynwiki_Model_List';
  public function getValueById($list_id){
  	 
  	$select = $this->select()->where('list_id =?', $list_id);
  	$results = $this->fetchAll($select);
  	foreach($results as $result){
  		return $result;
  	}
  	
  }
  public function getValueByName($pageId, $action){
  	$select = $this->select()->where('owner_id =?', $pageId)->where('title=?',$action);
  	$results = $this->fetchAll($select);
  	foreach($results as $result){
  		return $result;
  	}
  }
}