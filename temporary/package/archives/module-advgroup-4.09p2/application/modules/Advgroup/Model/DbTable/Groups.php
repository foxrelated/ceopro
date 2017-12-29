<?php
class Advgroup_Model_DbTable_Groups extends Engine_Db_Table {
	protected $_name = 'group_groups';
	protected $_rowClass = 'Advgroup_Model_Group';

	public function getGroupPaginator($params = array()) {
		if(Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
			$params = Engine_Api::_()->ynlocationbased()->mergeWithCookie('advgroup',$params);
		}
		return Zend_Paginator::factory($this -> getGroupSelect($params));
	}

	public function getGroupSelect($params = array()) {
		// Get Groups Table
		$groupTable = Engine_Api::_() -> getItemTable('group');
		$groupName = $groupTable -> info('name');

		// Get Tagmaps Table
		$tags_table = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tags_name = $tags_table -> info('name');

		$select = $groupTable -> select() -> distinct();

		//Get your location
		$target_distance = $base_lat = $base_lng = "";
		if (isset($params['lat']))
			$base_lat = $params['lat'];
		if (isset($params['long']))
			$base_lng = $params['long'];

		//Get target distance in miles
		if (isset($params['within']))
            $target_distance = intval($params['within']);
            if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi') == 'km')
                $target_distance *= Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.mi.to.km', 0.621371);

		$select -> setIntegrityCheck(false);
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {

			$select -> from("$groupName", array("$groupName.*", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance"));
			$select -> where("latitude <> ''");
			$select -> where("longitude <> ''");

		} else {
			$select -> from("$groupName", array("$groupName.*"));
		}

		if (!isset($params['direction'])) {
			$params['direction'] = 'DESC';
		}

		if (isset($params['order']) && $params['order'] == 'displayname') 
		{
			$select -> setIntegrityCheck(false) -> join('engine4_users as u', "u.user_id = $groupName.user_id", '') -> order("u.displayname " . $params['direction']);
		} else if (!empty($params['order']) && $params['order'] === 'most_active') 
		{
			$topicTable = Engine_Api::_() -> getItemTable('advgroup_topic');
			$topicName = $topicTable -> info('name');
			$select -> setIntegrityCheck(false) -> joinLeft($topicName, "$topicName.group_id = $groupName.group_id", "$topicName.topic_id") -> group("$groupName.group_id") -> order("COUNT('topic_id') " . $params['direction']);
		}
		else if(!empty($params['order']) && $params['order'] === 'alpha_az') 
		{
		    $select -> order($groupName.'.title ASC');
		}
        else if(!empty($params['order']) && $params['order'] === 'alpha_za') 
        {
            $select -> order($groupName.'.title DESC');
        }
        else
        {
			// Order
			if (!empty($params['order'])) {
				$select -> order($params['order'] . ' ' . $params['direction']);
			} else {
				$select -> order('group_id DESC');
			}
		}
		// Search
		if (isset($params['search'])) {
			$select -> where("$groupName.search = ?", (bool)$params['search']);
		}

		//Private
		if (isset($params['private']) && !empty($params['private'])) {
			$select -> where("$groupName.parent_id not in (?)", $params['private']);
		}

		//Search Text
		if (!empty($params['text'])) {
			$select -> where("$groupName.title LIKE ? OR $groupName.description LIKE ? ", '%' . $params['text'] . '%');
		}

		if (!empty($params['title'])) {
			$select -> where("$groupName.title LIKE ?", '%' . $params['title'] . '%');
		}
		// User-based
		if (!empty($params['owner']) && $params['owner'] instanceof Core_Model_Item_Abstract) {
			$select -> where("$groupName.user_id = ?", $params['owner'] -> getIdentity());
		} else if (!empty($params['user_id'])) {
			$select -> where("$groupName.user_id = ?", $params['user_id']);
		} else if (!empty($params['users']) && is_array($params['users'])) {
			foreach ($params['users'] as &$id)
				if (!is_numeric($id))
					$id = 0;
			$params['users'] = array_filter($params['users']);
			$select -> where("$groupName.user_id IN(" . join(',', $params['users']) . ')');
		}
		//Group_based
		if (!empty($params['group_ids']) && is_array($params['group_ids'])) {
			$select -> where("$groupName.group_id IN(?)", $params['group_ids']);
		}

		// Category
		if (!empty($params['category_id'])) {
			$cat_array = Engine_Api::_() -> getDbTable('categories', 'advgroup') -> getArraySearch($params['category_id']);
			$select -> where("$groupName.category_id in (?)", $cat_array);
		}

		//User_based
		if (!empty($params['owner'])) {
			$key = stripslashes($params['owner']);
			$select -> setIntegrityCheck(false) -> join('engine4_users as u1', "u1.user_id = $groupName.user_id", '') -> where("u1.displayname LIKE ?", "%{$key}%");
		}

		//Admin Condition Based
		if (isset($params['featured'])) {
			$select -> where("$groupName.featured = ?", $params['featured']);
		}

		if (isset($params['is_subgroup'])) {
			$select -> where("$groupName.is_subgroup = ?", $params['is_subgroup']);
		}

		//Tags
		if (!empty($params['tag'])) {
			$select -> setIntegrityCheck(false) -> joinLeft($tags_name, "$tags_name.resource_id = $groupName.group_id", "") -> where($tags_name . '.resource_type = ?', 'group') -> where($tags_name . '.tag_id = ?', $params['tag']);
		}

		// Order
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
			$select -> having("distance <= $target_distance");
			$select -> order("distance ASC");
		}
		return $select;
	}

}
