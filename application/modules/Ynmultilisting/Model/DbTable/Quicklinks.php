<?php
class Ynmultilisting_Model_DbTable_Quicklinks extends Engine_Db_Table {
    protected $_rowClass = 'Ynmultilisting_Model_Quicklink';
    protected $_serializedColumns = array('category_ids', 'price');
    
    public function getListingTypeQuicklinks($listingtype_id, $params = array()) {
        $select = $this->select();
		if (empty($params['all']))
        	$select->where('listingtype_id = ?', $listingtype_id);
		if (!empty($params['show'])) {
			$select->where('`show` = ?', $params['show']);
		}
		if (isset($params['ids'])) {
			if (empty($params['ids'])) $params['ids'] = array(0);
			$select->where('quicklink_id IN (?)', $params['ids']);
		}
        $quicklinks = $this->fetchAll($select);
        return $quicklinks;
    }
	
}