<?php
class Ynmultilisting_Model_DbTable_Memberlevelpermission extends Engine_Db_Table {
	protected $_name = 'ynmultilisting_memberlevelpermission';

	public function _getAllowed($type, $level_id, $nameArray, $listingtype_id) {
		$select = $this -> select() -> where('listingtype_id = ?', $listingtype_id) -> where('type = ?', $type) -> where('level_id = ?', $level_id);
		if (is_array($nameArray)) {
			$select -> where('name IN (?)', $nameArray);
			$return = $this -> fetchAll($select);
		} 
		elseif (is_scalar($nameArray)) {
			$select -> where('name = ?', $nameArray);
			$return = $this -> fetchAll($select);
		}
		return $return;

	}

	public function isAllowed($type, $level_id, $nameArray, $listingtype_id) {
		
		// Get
		$data = $this -> _getAllowed($type, $level_id, $nameArray, $listingtype_id);
		$rows = $data->toArray();
		if($rows) {
			$row = $rows[0];
			return $row['value'];
		}
		else {
			return 0;
		}
	}

	public function getAllowed($type, $level_id, $nameArray, $listingtype_id) {
		$data = $this -> _getAllowed($type, $level_id, $nameArray, $listingtype_id);
		$rawData = array();
		if($data) {
			foreach ($data->toArray() as $row) {
				$rawData[$row['name']] = $row['value'];
			}
		}
		return $rawData;
	}

	public function setAllowed($type, $level_id, $nameArray, $listingtype_id, $value = null) {
		// Can set multiple actions
		if (is_array($nameArray)) {
			foreach ($nameArray as $key => $value) {
				$this -> setAllowed($type, $level_id, $key, $listingtype_id, $value);
			}
			return $this;
		}

		// Set info
		// Check for existing row
		$select = $this -> select()
				-> where('level_id = ?', $level_id)
				-> where('type = ?', $type)
				-> where('name = ?', $nameArray)
				-> where('listingtype_id = ?', $listingtype_id)
				-> limit(1);
		$row = $this -> fetchRow($select);
		if (is_null($row)) {
			$row = $this -> createRow();
			$arr = array('level_id' => $level_id, 'type' => $type, 'name' => $nameArray, 'listingtype_id' => $listingtype_id);
			$row -> setFromArray($arr);
		}
		if (is_scalar($value)) {
			$row -> value = $value;
		} 
		else if (is_array($value)) {
			$row -> value = Zend_Json::encode($value);
		}

		$row -> save();
		return $this;
	}

}
