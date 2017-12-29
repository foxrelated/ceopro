<?php
class Ynmultilisting_Model_DbTable_Packages extends Engine_Db_Table {
	protected $_rowClass = 'Ynmultilisting_Model_Package';
	protected $_serializedColumns = array('themes');
	
	public function getPackagesPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getPackagesSelect($params));
	}

	public function getPackagesSelect($params = array()) {
		$select = $this -> select();
		
		if(isset($params['title']))
		{
			$select -> where('title LIKE ?', '%'.$params['title'].'%');
		}
		
		if(isset($params['show']))
		{
			$select -> where('`show` = ?', $params['show']);
		}
		
		if (empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
			
	    if (!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order('order ASC');
		}
		
		$select -> where('deleted <> ?', 1);
		
		return $select;
	}
	
	public function getPackageAssoc($params = array())
	{
		$select = $this->getPackagesSelect($params);
		$packages = $this -> fetchAll($select);
		$result = array();
		foreach ($packages as $p){
			$result[$p -> getIdentity()] = $p -> title;
		}
		return $result;
	}
	
}
