<?php
class Ynmultilisting_Model_DbTable_Comparisons extends Engine_Db_Table {
    protected $_name = 'ynmultilisting_comparisons';
    protected $_serializedColumns = array('common_fields', 'custom_fields', 'rating_fields', 'review_fields');
    
    public function getCategoryComparison($category_id) {
        $select = $this->select()->where('category_id = ?', $category_id);
        $comparison = $this->fetchRow($select);
        return $comparison;
    }
	
	public function addDefaultComparison($category_id) {
		if ($this->getCategoryComparison($category_id)) return false;
		$values = array(
			'common_fields' => array("photo","title","price","owner","creation_date","expiration_date","short_description"),
			'rating_fields' => array("overal_rating"),
			'review_fields' => array("overal_review","pros","cons", "latest_review", "number_review"),
			'category_id' => $category_id
		);
		$comparison = $this->createRow();
		$comparison->setFromArray($values);
		$comparison->save();
		return true;
	}
}