<?php

class Ynadvsearch_Model_DbTable_Contenttypes extends Engine_Db_Table
{
    protected $_rowClass = 'Ynadvsearch_Model_Contenttype';
	
	public function getContentTypesSelect($params = array())
	{										
		$select = $this -> select();
		$select -> order('order ASC');
		if(!empty($params['limit']))
		{
			$select -> limit($params['limit']);
		}
		return $select;
	}
	
	public function getContentTypesPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getContentTypesSelect($params));
		if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
		return $paginator;
	}
	
	public function getContentType($content_type)
	{
		$select = $this-> select();
		$select -> where('type = ?', $content_type);
		$select -> limit(1);
		return $this -> fetchRow($select);
	}
}
