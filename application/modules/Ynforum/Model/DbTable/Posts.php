<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Posts.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Model_DbTable_Posts extends Engine_Db_Table {
	protected $_rowClass = 'Ynforum_Model_Post';
	protected $_name = 'forum_posts';

	public function getChildrenSelectOfForumTopic($topic) {
		$select = $this -> select() -> where('topic_id = ?', $topic -> topic_id);
		return $select;
	}

	public function getPostPaginator($params = array()) {
		$select = $this -> select();
		$Name = $this->info('name');
		$select = $this -> select()->from($Name,new Zend_Db_Expr("$Name.*,(SELECT view_count FROM engine4_forum_topics WHERE engine4_forum_topics.topic_id = $Name.topic_id ) AS view_count"))-> setIntegrityCheck(false);
						
		
		if (isset($params['title']) && $params['title'] != "") {
			$select -> where('title LIKE (?) ', '%'.$params['title'].'%');
		}	
		
		if (isset($params['approved']) && $params['approved'] != "") {
			$select -> where('approved =? ', $params['approved']);
		}		
		// From date
		if (!empty($params['start_date']) && empty($params['end_date'])) {
			$fromdate = Engine_Api::_() -> ynforum() -> getFromDaySearch($params['start_date']);
			if (!$fromdate) {
				$select -> where("false");
				return $select;
			}
			$select = $this -> _selectFromDate($select, $fromdate);
		}

		// To date
		if (isset($params['start_date']) && !empty($params['start_date'])) {
            $select -> where("creation_date >= ?", date("Y-m-d 00:00:01", strtotime($params['start_date'])));
        }
        if (isset($params['end_date']) && !empty($params['end_date'])) {
            $select -> where("creation_date < ?", date("Y-m-d 23:59:59", strtotime($params['end_date'])));
        }
		if (!empty($params['order']) && !empty($params['direction'] ) && $params['order'] != 'displayname')
		{
			$select -> order($params['order'] . ' ' . $params['direction']);
		}
		
		$select -> where("user_id IN (SELECT user_id FROM engine4_users `u`WHERE  `u`.displayname LIKE  '%".$params['creator']."%')");
		
		
		
		$data = $this -> fetchAll($select);
		return Zend_Paginator::factory($data);
	}

	private function _selectFromDate($select, $from) {
		$tableName = $this -> info('name');
		$select -> where("($tableName.creation_date >= ?)", $from);
		return $select;
	}

	private function _selectToDate($select, $todate) {
		$tableName = $this -> info('name');
		$select -> where("($tableName.creation_date <= ?)", $todate);
		return $select;
	}

	private function _appendSelectInRange($select, $from, $to) {
		$tableName = $this -> info('name');
		$select -> where("$tableName.creation_date between '$from' and '$to'");
		return $select;
	}

}
