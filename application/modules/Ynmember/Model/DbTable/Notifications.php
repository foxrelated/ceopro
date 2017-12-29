<?php

class Ynmember_Model_DbTable_Notifications extends Engine_Db_Table
{
	protected $_rowClass = 'Ynmember_Model_Notification';
	protected $_name = 'ynmember_notifications';

	public function getNotificationRow($params)
	{
		$select = $this->select();
		if (!empty($params['resource_id']))
		{
			$select -> where("resource_id = ? ", $params['resource_id']);
		}
		if (!empty($params['user_id']))
		{
			$select -> where("user_id = ? ", $params['user_id']);
		}
		$select -> limit(1);
		return $this -> fetchRow($select);
	}

	public function getNotificationCount($user)
	{
		$select = $this->select()->where("resource_id = ?", $user->getIdentity());
		$select->where('active = ?', '1');
		$notifications = $this->fetchAll($select);
		return count($notifications);
	}

	public function getAllUsers($resource)
	{
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->info('name'), array('active', 'user_id'));
		$select->where('resource_id = ?', $resource->getIdentity());
		$select->where('active = ?', '1');

		$users = array();
		foreach( $select->query()->fetchAll() as $data )
		{
   			$users[] = $data['user_id'];
		}
		$users = array_values(array_unique($users));

		return Engine_Api::_()->getItemMulti('user', $users);
	}
	
}
