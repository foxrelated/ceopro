<?php

class Ynwiki_Model_DbTable_Pages extends Engine_Db_Table
{

    protected $_rowClass = 'Ynwiki_Model_Page';
	
	public function getAllPage(){
		$select = $this->select();
		$results = $this->fetchAll($select);
		$arr = array();
		foreach($results as $result){
			$arr[] = $result->page_id;
		}
		
		return $arr;
	}
	
	
	
}
