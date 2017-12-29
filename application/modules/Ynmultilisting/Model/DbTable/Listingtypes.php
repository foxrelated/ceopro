<?php
class Ynmultilisting_Model_DbTable_Listingtypes extends Engine_Db_Table
{
	protected $_rowClass = 'Ynmultilisting_Model_Listingtype';
	
	public function getAll()
	{
		return $this -> fetchAll();
	}
	
	public function getTypesSelect($params = array())
	{
		$tableName = $this->info('name');
		$select = $this
		->select()
		->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : $tableName.'.order ASC' );

		if( !empty($params['visible']) && $params['visible'] == 'all')
		{}
		else if( !empty($params['visible']) )
		{
			$select->where($tableName.".show = ?", $params['visible']);
		}
		else 
		{
			$select->where($tableName.".show = 1");
		}
		return $select;
	}

	public function getTypesPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this->getTypesSelect($params));
		if( !empty($params['page']) )
		{
			$paginator->setCurrentPageNumber($params['page']);
		}
		if( !empty($params['limit']) )
		{
			$paginator->setItemCountPerPage($params['limit']);
		}
		else
		{
			$paginator->setItemCountPerPage(10);
		}	
		return $paginator;
	}
    
    //HOANGND get all available listingtypes
    public function getAvailableListingTypes() {
        $select = $this->select()->where('`show` = ?', 1)->order('order ASC');
        return $this->fetchAll($select);   
    }
    
    //HOANGND get default listing type
    public function getDefaultListingType() {
        $select = $this->select()->where('`show` = ?', 1)->order('order ASC');
        $listingtype = $this->fetchRow($select);
        if (!$listingtype) $listingtype = $this->fetchRow($this->select()->where('`show` = ?', 1));
        return $listingtype;
    }
	
	public function getTypeAssoc()
    {
    	$rows = $this -> fetchAll();
    	$result = array(
    		'0' => Zend_Registry::get("Zend_Translate")->_('None')
    	);
    	foreach ($rows as $r){
    		$result[$r -> listingtype_id] = $r -> title;
    	}
    	return $result;
    }
}