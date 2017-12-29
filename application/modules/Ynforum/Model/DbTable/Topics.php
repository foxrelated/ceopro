<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_Model_DbTable_Topics extends Engine_Db_Table {
    protected $_rowClass = 'Ynforum_Model_Topic';
    protected $_name = 'forum_topics';

    public function getChildrenSelectOfForum($forum, $params) {
        $select = $this->select()->where('forum_id = ?', $forum->forum_id);
        return $select;
    }
    
    // get the select query to search topics in some forums containing a specific title
    public function searchTopics($forumIds, $title) {
        $select = $this->select();
        if ($forumIds) {
            $select->where('forum_id in (?)', $forumIds);
        }
        $select->where('title like ?', '%' . $title . '%');
        $select->where('approved = 1');
        
        return $select;
    }
}