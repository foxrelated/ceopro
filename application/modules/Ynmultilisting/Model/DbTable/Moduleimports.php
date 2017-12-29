<?php
class Ynmultilisting_Model_DbTable_Moduleimports extends Engine_Db_Table {
	
	public function getImportedItemIdsOfModule($module_id, $owner_id = 0) {
		$select = $this -> select() -> from($this, 'item_id') -> where('module_id = ?', $module_id);
		if (!empty($owner_id)) {
			if (is_array($owner_id)) {
				$select->where('owner_id IN (?)', $owner_id);
			}
			else {
				$select->where('owner_id = ?', $owner_id);
			}
		}
		return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
	}
	
	public function getListingIdsOfModule($module_id, $item_id) {
		$select = $this -> select() -> from($this, 'listing_id') -> where('module_id = ?', $module_id) -> where('item_id = ?', $item_id);
		return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
	}
	
	public function importItem($module_id, $item_id, $owner_id, $listing_id) {
		$values = array(
			'module_id' => $module_id,
			'item_id' => $item_id,
			'owner_id' => $owner_id,
			'listing_id' => $listing_id,
			'creation_date' => date('Y-m-d H:i:s')
		);
		
		$item = $this->createRow();
		$item->setFromArray($values);
		$item->save();
	}
	
	public function removeImportedItemsByListingId($listing_id) {
		$this->delete(array(
	      	'listing_id = ?' => $listing_id,
	    ));
	} 
}