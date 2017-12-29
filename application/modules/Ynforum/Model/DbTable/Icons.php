<?php
class Ynforum_Model_DbTable_Icons extends Engine_Db_Table
{
	protected $_rowClass = 'Ynforum_Model_Icon';
  	protected $_name = 'ynforum_icons';
	public function getPaginator($params = array())
	{
		return Zend_Paginator::factory($this -> getSelect($params));
	}
	public function getSelect($params = array())
	{
		$table = Engine_Api::_()->getDbTable("icons", "ynforum");
		$select = $table -> select();
		// order by direction
		if (!empty($params['order']) && !empty($params['direction']))
		{
			$select -> order($params['order'] . ' ' . $params['direction']);
		}
		// title
		if (!empty($params['icon_name']))
		{
			$select -> where("title LIKE ?", '%' . $params['icon_name'] . '%');
		}
			// From date
		if (!empty($params['start_date']) && empty($params['end_date']))
		{
			$fromdate = Engine_Api::_() -> ynforum() -> getFromDaySearch($params['start_date']);
			if (!$fromdate)
			{
				$select -> where("false");
				return $select;
			}
			$select = $this -> _selectFromDate($select, $fromdate);
		}

		// To date
		if (!empty($params['end_date']) && empty($params['start_date']))
		{
			$todate = Engine_Api::_() -> ynforum() -> getToDaySearch($params['end_date']);
			if (!$todate)
			{
				$select -> where("false");
				return $select;
			}
			$select = $this -> _selectToDate($select, $todate);
		}

		if (!empty($params['start_date']) && !empty($params['end_date']))
		{
			$fromdate = Engine_Api::_() -> ynforum() -> getFromDaySearch($params['start_date']);
			$todate = Engine_Api::_() -> ynforum() -> getToDaySearch($params['end_date']);
			$select = $this -> _appendSelectInRange($select, $fromdate, $todate);
		}
		return $select;
	}
	private function _selectFromDate($select, $from)
	{
		$tableName = $this -> info('name');
		$select -> where("($tableName.creation_date >= ?)", $from);
		return $select;
	}

	private function _selectToDate($select, $todate)
	{
		$tableName = $this -> info('name');
		$select -> where("($tableName.creation_date <= ?)", $todate);
		return $select;
	}
	private function _appendSelectInRange($select, $from, $to)
	{
		$tableName = $this -> info('name');
		$select -> where("$tableName.creation_date between '$from' and '$to'");
		return $select;
	}
}