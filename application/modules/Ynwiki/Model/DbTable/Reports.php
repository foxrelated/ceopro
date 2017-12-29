<?php

class Ynwiki_Model_DbTable_Reports extends Engine_Db_Table
{

    protected $_rowClass = 'Ynwiki_Model_Report';
	
	public function getReportById($page_id){
		$select = $this->select()->where('page_id =?', $page_id);
		$results = $this->fetchAll($select);
		
		return $results;
	}
}
