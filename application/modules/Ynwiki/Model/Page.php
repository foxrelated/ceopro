<?php
class Ynwiki_Model_Page extends Core_Model_Item_Abstract {
	// Properties
	protected $_parent_type = 'user';
	protected $_owner_type = 'user';

	public $role_group = array('owner', 'ynwiki_list', 'member', 'parent_member', 'registered', 'everyone');
	public $role_wiki = array('owner', 'ynwiki_list', 'member', 'registered', 'everyone');

	protected $_searchTriggers = array('title', 'body', 'search');

	public function getGroup($group_id) {
		$table = Engine_Api::_() -> getDbTable('groups', 'Advgroup');
		$select = $table -> select() -> where('group_id = ?', $group_id);
		return $table -> fetchRow($select) -> title;
	}

	public function membership() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('membership', 'ynwiki'));
	}

	public function getAllPage2() {
		$table = Engine_Api::_() -> getItemTable('ynwiki_page');
		if ($this -> parent_id) 
		{
			$select = $table -> select() -> where('parent_id  =?', $this -> parent_id);
			$results = $table -> fetchAll($select);
			return $results;
		}
		return $this;
	}

	public function getOfficerList() {
		$table = Engine_Api::_() -> getItemTable('ynwiki_list');
		$select = $table -> select() -> where('owner_id = ?', $this -> getIdentity()) -> where('title = ?', 'ynwiki_list') -> limit(1);

		$list = $table -> fetchRow($select);

		if (null === $list) {
			$list = $table -> createRow();
			$list -> setFromArray(array('owner_id' => $this -> getIdentity(), 'title' => 'ynwiki_list', ));
			$list -> save();
		}

		return $list;
	}

	public function getGroupSlug($str = null) {
		//$str = $this->getTitle();
		if (strlen($str) > 32) {
			$str = Engine_String::substr($str, 0, 32) . '...';
		}
		$str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
		$str = strtolower($str);
		$str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
		$str = preg_replace('/-+/', '-', $str);
		$str = trim($str, '-');
		if (!$str) {
			$str = '-';
		}
		return $str;
	}

	public function getHref($params = array()) {
		$slug = $this -> getSlug();

		$params = array_merge(array('route' => 'ynwiki_general', 'action' => 'view', 'reset' => true, 'pageId' => $this -> page_id, 'slug' => $slug, ), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getPrintHref($params = array()) {
		$params = array_merge(array('route' => 'ynwiki_general', 'action' => 'print-view', 'reset' => true, 'pageId' => $this -> page_id, ), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getDescription() {
		$tmpBody = strip_tags($this -> description);
		return (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody);
	}

	public function getKeywords($separator = ' ') {
		$keywords = array();
		foreach ($this->tags()->getTagMaps() as $tagmap) {
			$tag = $tagmap -> getTag();
			$keywords[] = $tag -> getTitle();
		}

		if (null === $separator) {
			return $keywords;
		}

		return join($separator, $keywords);
	}

	public function getSlug($str = null) {
		return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this -> title))), '-');
	}

	public function comments() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	public function likes() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	public function getParentPage() {
		$parent = Engine_Api::_() -> getItem('ynwiki_page', $this -> parent_page_id);
		return $parent;
	}

	/**
	 * get chilren node
	 *
	 */
	public function getChilds() {
		$table = $this -> _table;
		$select = $table -> select() -> where('parent_page_id = ?', $this -> getIdentity());
		return $table -> fetchAll($select);
	}

	public function getAttachments() {
		$attachTable = Engine_Api::_() -> getDbtable('attachments', 'ynwiki');
		$select = $attachTable -> select() -> where('page_id = ?', $this -> page_id) -> order('creation_date DESC');
		$attachments = $attachTable -> fetchAll($select);
		return $attachments;
	}

	public function recycle() {
		//$childs = $this->getChilds();
		$childs = $this -> getChildOfPage($data, $page -> page_id);
		foreach ($childs as $childs) {
			$childs -> delete();
		}
		$this -> delete();
	}

	public function reDelete() {
		//$childs = $this->getChilds();
		$childs = $this -> getChildOfPage($data, $page -> page_id);

		foreach ($childs as $childs) {
			$childs -> reDelete();
		}
		$revisions = $this -> getRevisions();
		foreach ($revisions as $revision)
			$revision -> delete();
		$this -> delete();
	}

	public function moveChilds($increLevel = null) {
		$childs = $this -> getChilds();
		foreach ($childs as $childs) {
			$childs -> moveChilds($increLevel);
		}
		$this -> level = $this -> level + $increLevel;
		$this -> save();
	}

	public function getRevisions() {
		$revisionTable = Engine_Api::_() -> getDbtable('revisions', 'ynwiki');
		$select = $revisionTable -> select() -> where('page_id = ?', $this -> page_id) -> order('creation_date DESC');
		$revisions = Zend_Paginator::factory($select);
		return $revisions;
	}

	public function getBreadCrumNode() {
		$arrParents = Array();
		$node = $this;
		for ($i = 0; $i < $this -> level; $i++) {
			$parent = $node -> getParentPage();
			if ($parent) {
				$node = $parent;
			}
			$arrParents[] = $node;
		}
		return array_reverse($arrParents);
	}

	public function checkParent($page_id) {
		if ($this -> page_id == $page_id)
			return true;
		$node = $this;
		for ($i = 0; $i < $this -> level; $i++) {
			$parent = $node -> getParentPage();
			if ($parent) {
				$node = $parent;
			}
			if ($parent -> page_id == $page_id)
				return true;
		}
		return false;
	}

	public function getRootParent($page_id) {
		if ($this -> parent_page_id != 0) {
			$parents = $this -> getBreadCrumNode();
			return $parents[0];
		}
		return null;
	}

	public function getLastUpdated($compare = 0) {
		$revisionTable = Engine_Api::_() -> getDbtable('revisions', 'ynwiki');
		$select = $revisionTable -> select()
		// -> where('revision_id <> ?', $this->revision_id)
		-> where('page_id = ?', $this -> page_id) -> order("revision_id DESC");
		$rows = $revisionTable -> fetchAll($select);
		if (count($rows) > 1) {
			if ($compare == 1)
				return $rows[1];
			else
				return $rows[0];
		} else {
			return null;
		}
	}

	public function getNewUpdated() {
		$revisionTable = Engine_Api::_() -> getDbtable('revisions', 'ynwiki');
		$select = $revisionTable -> select() -> where('revision_id = ?', $this -> revision_id) -> where('page_id = ?', $this -> page_id);
		$row = $revisionTable -> fetchRow($select);
		return $row;
	}

	public function getAllow($action = 'view', $pageid = 0) {
		$role = array();
		$allow = Engine_Api::_() -> getDbtable('allow', 'authorization');
		$type = 'ynwiki_page';

		if ($pageid == 0)
			$page_id = ($this -> parent_page_id != 0) ? $this -> parent_page_id : $this -> page_id;
		else
			$page_id = $pageid;

		$select = $allow -> select() -> where('resource_id = ?', $page_id) -> where('resource_type = ?', $type) -> where('action = ?', $action);
		$result = $allow -> fetchAll($select);

		$flag = false;
		foreach ($result->toArray() AS $value)
			if ($value['role'] != 'everyone')
				$role[] = $value['role'];
			else
				$flag = true;
		if ($flag == true)
			$role[] = 'everyone';
		$role[] = 'owner';
		print_r($result -> toArray('role'));
		die ;
		return $role;
	}

	public function getAllow2($action = 'view', $pageid = 0) {
		$role = array();
		$allow = Engine_Api::_() -> getDbtable('allow', 'authorization');
		$type = 'ynwiki_page';

		if ($pageid == 0)
			$page_id = ($this -> parent_page_id != 0) ? $this -> parent_page_id : $this -> page_id;
		else
			$page_id = $pageid;

		$select = $allow -> select() -> where('resource_id = ?', $page_id) -> where('resource_type = ?', $type) -> where('action = ?', $action);
		$result = $allow -> fetchAll($select);

		foreach ($result->toArray() as $value) {
			$arr_role[] = $value['role'];
		}

		return $arr_role;

	}

	public function getMaxAllow($action = 'view', $pageid = 0) {

		$arr_role = $this -> getAllow2($action, $pageid);

		$role = null;

		foreach ($this->role_group as $item) {

			if (in_array($item, $arr_role)) {
				$role = $item;
			}
		}

		if ($role == null)
			$role = 'owner';

		return $role;

	}

	public function getMinAllow($action = 'view', $pageid = 0) {
		$role = $this -> getAllow($action, $pageid);
		return $role[0];
	}

	public function getChildOfPage(&$ar, $parent_id = null, $descendantIds = array()) {
		$temp = array();
		if (!$parent_id) {
			$temp = $this -> getDirectDescendant($this -> page_id);
		} else {
			$temp = $this -> getDirectDescendant($parent_id);
		}

		if (count($temp) > 0 && is_array($temp)) {
			$descendantIds = array_merge($descendantIds, $temp);
		}

		// print_r($descendantIds);

		foreach ($temp as $te) {
			$ar[] = $te;
			$this -> getChildOfPage($ar, $te -> page_id, $descendantIds);
		}

		return $ar;
	}

	public function getDirectDescendant($parentPageId) {
		$table = $this -> _table;
		$select = $table -> select();
		$id = array();
		$select -> where('parent_page_id = ?', $parentPageId);
		$results = $table -> fetchAll($select);
		foreach ($results as $result) {
			$id[] = $result;
		}
		return $id;
	}

	public function check($action = 'view', $parent_type = 'group') {

		$allow = array();
		if ($this -> parent_page_id != 0) {

			$parents = $this -> getBreadCrumNode();
			foreach ($parents AS $parent) {
				$allow[] = $this -> getMaxAllow($action, $parent -> page_id);
			}

		} else {
			$allow[] = $this -> getMaxAllow('view', $this -> page_id);
		}

		if ($parent_type == 'group') {
			$roles = array('everyone' => 'Everyone', 'registered' => 'All Registered Members', 'parent_member' => 'Group Members (group wikis only)', 'member' => 'Wiki members', 'ynwiki_list' => 'Owner and Officer');
		} else {
			$roles = array('everyone' => 'Everyone', 'registered' => 'All Registered Members', 'member' => 'Wiki members', 'ynwiki_list' => 'Owner and Officer');
		}

		$allow = array_intersect_key($roles, array_flip($allow));

		$key = array();

		foreach ($allow AS $key => $value)
			$tmp[] = $key;
		return $tmp[count($tmp) - 1];

	}

	public function checkToView($parent_type) {
		return $this -> check('view', $parent_type);
	}

	public function checkToEdit($parent_type) {
		return $this -> check('edit', $parent_type);
	}

	public function checkToDelete($parent_type) {
		return $this -> check('delete', $parent_type);
	}

	public function checkToAdd($parent_type) {
		return $this -> check('add', $parent_type);
	}

	public function checkToRestrict($parent_type) {
		return $this -> check('restrict', $parent_type);
	}

	public function checkFollow() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$followTable = Engine_Api::_() -> getDbtable('follows', 'ynwiki');
		$select = $followTable -> select() -> where('page_id = ?', $this -> page_id) -> where('user_id = ?', $viewer -> getIdentity());
		$row = $followTable -> fetchRow($select);
		if ($row) {
			return false;
		} else {
			return true;
		}
	}

	public function getUserFollows() {
		$followTable = Engine_Api::_() -> getDbtable('follows', 'ynwiki');
		$select = $followTable -> select() -> where('page_id = ?', $this -> page_id);
		$rows = $followTable -> fetchAll($select);
		return $rows;
	}

	public function checkFavourite() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$favouriteTable = Engine_Api::_() -> getDbtable('favourites', 'ynwiki');
		$select = $favouriteTable -> select() -> where('page_id = ?', $this -> page_id) -> where('user_id = ?', $viewer -> getIdentity());
		$row = $favouriteTable -> fetchRow($select);
		if ($row) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function tags() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}

	protected function _delete() {
		parent::_delete();
		$model = new Ynwiki_Model_DbTable_Reports();
		$reports = $model -> getReportById($this -> page_id);
		foreach ($reports as $report) {
			$report -> delete();
		}

	}

	public function saveAttach($name, $title) {
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'ynwiki', 'parent_id' => $this -> getIdentity());

		// Save
		$storage = Engine_Api::_() -> storage();

		// Store
		$aMain = $storage -> create($path . '/' . $name, $params);

		// Remove temp files
		@unlink($path . '/' . $name);

		// save row
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$attach_table = Engine_Api::_() -> getItemTable('ynwiki_attachment');
		$attachment = $attach_table -> createRow();
		$attachment -> page_id = $this -> getIdentity();
		$attachment -> user_id = $viewer -> getIdentity();
		$attachment -> file_id = $aMain -> getIdentity();
		$attachment -> title = $title;
		$attachment -> creation_date = date('Y-m-d H:i:s');
		$attachment -> modified_date = date('Y-m-d H:i:s');
		$attachment -> save();

		return $this;
	}

	public function setPhoto($photo) {
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
		} else if (is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
		} else if (is_string($photo) && file_exists($photo)) {
			$file = $photo;
		} else {
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'ynwiki', 'parent_id' => $this -> getIdentity());

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(100, 100) -> write($path . '/in_' . $name) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;

		$image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/is_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> getIdentity();
		$this -> save();

		return $this;
	}

}
