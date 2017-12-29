<?php
/**
 * YounetNet Company
 *
 * @category
 * @package    Advanced Forum
 * @copyright
 * @license
 * @version
 * @author     LuanNguyen
 */

/**
 * @category
 * @package    Advanced Forum
 * @copyright
 * @license
 */
class Ynforum_Model_DbTable_Memberlevelpermission extends Engine_Db_Table {
	protected $_name = 'forum_memberlevelpermission';

	public function _getAllowed($type, $level_id, $nameArray, $forum_id) 
	{
		$select = $this -> select() -> where('forum_id = ?', $forum_id) -> where('type = ?', $type) -> where('level_id = ?', $level_id);
		if (is_array($nameArray)) 
		{
			$select -> where('name IN (?)', $nameArray);
			$return = $this -> fetchAll($select);
		} 
		elseif (is_scalar($nameArray)) 
		{
			$select -> where('name = ?', $nameArray);
			$return = $this -> fetchAll($select);
		}
		return $return;

	}

	public function isAllowed($type, $level_id, $nameArray, $forum_id) {
		
		// Get
		$data = $this -> _getAllowed($type, $level_id, $nameArray, $forum_id);
		$rows = $data->toArray();
		if($rows)
		{
			$row = $rows[0];
			return $row['value'];
		}
		else 
		{
			return 1;
		}
	}

	public function getAllowed($type, $level_id, $nameArray, $forum_id) 
	{
		$data = $this -> _getAllowed($type, $level_id, $nameArray, $forum_id);
		$rawData = array();
		if($data)
		{
			if($data)
			{
				foreach ($data->toArray() as $row) 
				{
					$rawData[$row['name']] = $row['value'];
				}
			}
		}
		return $rawData;
	}

	public function setAllowed($type, $level_id, $nameArray, $forum_id, $value = null) {
		// Can set multiple actions
		if (is_array($nameArray)) 
		{
			foreach ($nameArray as $key => $value) 
			{
				$this -> setAllowed($type, $level_id, $key, $forum_id, $value);
			}
			return $this;
		}

		// Set info
		// Check for existing row
		$select = $this -> select()
				-> where('level_id = ?', $level_id)
				-> where('type = ?', $type)
				-> where('name = ?', $nameArray)
				-> where('forum_id = ?', $forum_id)
				-> limit(1);
		$row = $this -> fetchRow($select);
		if (is_null($row)) 
		{
			$row = $this -> createRow();
			$arr = array('level_id' => $level_id, 'type' => $type, 'name' => $nameArray, 'forum_id' => $forum_id);
			$row -> setFromArray($arr);
		}
		if (is_scalar($value)) 
		{
			$row -> value = $value;
		} 
		else if (is_array($value)) 
		{
			$row -> value = Zend_Json::encode($value);
		}

		$row -> save();
		return $this;
	}

}
