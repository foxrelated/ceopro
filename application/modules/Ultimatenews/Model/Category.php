<?php
class Ultimatenews_Model_Category extends Core_Model_Item_Abstract 
{
	protected $_searchTriggers = false;
	public function getTopContents($params = array()) 
	{
		$contentTbl = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');
		$contentTblName = $contentTbl -> info('name');
		$categoryTbl = Engine_Api::_() -> getDbTable('categories', 'ultimatenews');
		$categoryTblName = $categoryTbl -> info('name');

		$select = $contentTbl -> select() -> from($contentTblName) -> setIntegrityCheck(false);
		$select -> joinLeft("$categoryTblName", "$categoryTblName.category_id = $contentTblName.category_id", array('logo' => "$categoryTblName.category_logo", 'logo_icon' => "$categoryTblName.logo", 'display_logo' => "$categoryTblName.display_logo", 'mini_logo' => "$categoryTblName.mini_logo", 'feed_name' => "$categoryTblName.category_name", 'feed_url' => "$categoryTblName.url_resource"));

		if (isset($params['checkcomment'])) {
			$select -> where("$contentTblName.content_id IN (SELECT resource_id FROM engine4_core_comments WHERE engine4_core_comments.resource_type='ultimatenews_content' AND engine4_core_comments.resource_id = engine4_ultimatenews_contents.content_id)");
		}

		if (isset($params['getcommment'])) {
			$select -> joinLeft("engine4_core_comments", "engine4_core_comments.resource_id= $contentTblName.content_id AND engine4_core_comments.resource_type = 'ultimatenews_content'", array('total_comment' => "count($contentTblName.content_id)", 'resource_id' => 'engine4_core_comments.resource_id'));
			$select -> group("$contentTblName.content_id");
		}

		$timezone_server_H = date('H');
		$timezone_server_i = date('i');
		$timezone_server_s = date('s');
		$timezone_server = $timezone_server_H * 3600 + $timezone_server_i * 60 + $timezone_server_s;

		$oldTz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$timezone_viewer = $timezone_server;
		if ($viewer -> getIdentity() > 0) {
			date_default_timezone_set($viewer -> timezone);
			$timezone_viewer_H = date('H');
			$timezone_viewer_i = date('i');
			$timezone_viewer_s = date('s');
			$timezone_viewer = $timezone_viewer_H * 3600 + $timezone_viewer_i * 60 + $timezone_viewer_s;
		}
		$subtimezone = ($timezone_server - $timezone_viewer);
		date_default_timezone_set($oldTz);
		$subtimezone = 0;
		if (isset($params['start_date']) && $params['start_date'] != '') {
			$start_date_ = $params['start_date'];
			$start_date = strtotime($start_date_) + $subtimezone;
			if ($start_date != $subtimezone)
				$select -> where("$contentTblName.pubDate >= $start_date ");
		}

		if (isset($params['end_date']) && $params['end_date'] != '') {
			$end_date_ = $params['end_date'];
			$end_date = strtotime($end_date_) + $subtimezone;

			if ($end_date != $subtimezone)
				$select -> where("$contentTblName.pubDate <= $end_date ");
		}

		// Category parent for listing news
		if ((!isset($params['category_id']) || $params['category_id'] == 0) && isset($params['category_parent']) && $params['category_parent'] != -1) {
			$select -> where("$categoryTblName.category_parent_id = ?", $params['category_parent']);
		}

		// Category Parent
		if (isset($params['category_parent']) && $params['category_parent'] != "" && $params['category_parent'] != '-1') {
			$select -> where("$categoryTblName.category_parent_id = ?", $params['category_parent']);
		}

		// Category
		$select -> where("$contentTblName.category_id = ?", $this -> category_id);

		// title
		if (!empty($params['title'])) {
			$title = $params['title'];
			$select -> where("$contentTblName.title LIKE ?", "%$title%");
		}
		//search
		if (!empty($params['search'])) {
			$select -> where("$contentTblName.title LIKE ? OR description LIKE ? OR content LIKE ?", $params['search'], $params['search'], $params['search']);
		}
		
		if (!empty($params['approved'])) {
			$select -> where("$contentTblName.approved = 1");
		}

		// Order
		if (!empty($params['group'])) {
			$select -> order("$contentTblName.category_id DESC");
		}

		if (isset($params['order']) && $params['order'] != "") {
			if (isset($params['direction']) && $params['direction'] != "")
				$select -> order($params['order'] . " " . $params['direction']);
			else {
				$select -> order($params['order']);
			}
		} else
			$select -> order('content_id DESC');

		if (isset($params['limit']) && $params['limit'] > 0) {
			$select -> limit($params['limit']);
		}
		$arrs = $contentTbl -> fetchAll($select);
		return $arrs;
	}

	function removeNews() {
		$contentTbl = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');
		$contentTbl -> delete("category_id = {$this->category_id}");
	}

	public function isSubscribe() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
        $users = array();
        if ($this -> subscribe) {
            $users = Zend_Json::decode($this -> subscribe);
        }

		if (!$users || !in_array($viewer -> getIdentity(), $users)) {
			return 'subscribe';
		} else {
			return 'unsubscribe';
		}
	}

	public function getTitle() {
		return $this -> category_name;
	}

	public function getHref($params = array()) {
		$slug = $this -> getSlug();
		$params = array_merge(array('route' => 'ultimatenews_feed', 'action' => 'feed', 'category' => $this -> category_id, 'slug' => $slug), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);

	}

	public function getSlug($str = NULL, $maxstrlen = 64) 
	{
		$str = $this -> getTitle();
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

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function tags() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
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

}
?>