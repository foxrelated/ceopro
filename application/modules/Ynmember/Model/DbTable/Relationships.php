<?php

class Ynmember_Model_DbTable_Relationships extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmember_Model_Relationship';
	protected $_name = 'ynmember_relationships';
	
	public function getAllRelationships(){
		$select = $this -> select();
		return $this -> fetchAll($select);
	}
}
