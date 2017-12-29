<?php

class Ynmultilisting_Model_DbTable_Reviewtypes extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmultilisting_Model_Reviewtype';
	protected $_name = 'ynmultilisting_reviewtypes';
	
	public function getAllReviewTypes($category_id)
	{
		$select = $this -> select()
						-> where('category_id = ?', $category_id);
		return $this -> fetchAll($select);
	}
	
	public function getReviewTypesByCategory($category)
	{
		$categoryId = 0;
		if (is_object($category))
		{
			$categoryId = $category -> getIdentity();
		}
		else if (is_numeric($category))
		{
			$categoryId = $category;
		}
		$select = $this -> select() -> where("category_id = ?", $categoryId);
		return $this -> fetchAll($select);
	}
	
	public function getReviewTypeAssocByCategory($category)
	{
		$rows = $this -> getReviewTypesByCategory($category);
        $translate = Zend_Registry::get("Zend_Translate");
		$result = array(
            'latest_review' => $translate->_('Latest Review'),
            'number_review' => $translate->_('Number of Review')
        );
		foreach ($rows as $r) 
		{
			$result[$r -> reviewtype_id] = $r -> title;
		}
		return $result;
	}
	
	public function addType($category, $data)
	{
		if (is_array($data) && count($data) == 0)
		{
			return false;
		}
		if (is_string($data) && trim($data) == '')
		{
			return false;
		}
		if (is_string($data)){
			$row = $this -> createRow();
			$row -> setFromArray(array(
				'category_id' => $category -> getIdentity(),
				'title' => trim($data)
			));
			$row -> save();
		}
		else if (is_array($data))
		{
			foreach ($data as $d){
				$row = $this -> createRow();
				$row -> setFromArray(array(
					'category_id' => $category -> getIdentity(),
					'title' => trim($d)
				));
				$row -> save();
			}
		}
	}
}
