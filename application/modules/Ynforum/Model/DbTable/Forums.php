<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Forums.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Model_DbTable_Forums extends Engine_Db_Table {

    protected $_rowClass = 'Ynforum_Model_Forum';
    protected $_name = 'forum_forums';

    public function getChildrenSelectOfYnforumCategory($category) {
        return $this->select()->where('category_id = ?', $category->category_id);
    }

    public function fetchAllAndOrderByParent($where = null, $order = null, $count = null, $offset = null) {
        $rowSetForums = $this->fetchAll($where, $order, $count, $offset);
        $forums = array();
        foreach($rowSetForums as $forum) {        	
            $forums[$forum->getIdentity()] = $forum;
        }

        foreach($forums as $forum) {
            if ($forum->parent_forum_id) {
                $forums[$forum->parent_forum_id]->addSubForum($forum);
            }
        }
        return $forums;
    }

    public function fetchAllAndOrderByHierachy($order = array('level', 'order ASC')) {
        $forumSelect = $this->select()->order($order);
        $forums = array();
        foreach ($this->fetchAllAndOrderByParent($forumSelect) as $forum) {
            if (!$forum->parent_forum_id) {
                $forums[$forum->category_id][] = $forum;
            }
        }
        foreach ($this->fetchAllAndOrderByParent($forumSelect) as $forum) {
            if ($forum->parent_forum_id) {
                $index = 0;
                foreach($forums[$forum->category_id] as $index => $orderForum) {
                    if ($forum->parent_forum_id == $orderForum->getIdentity()) {
                        break;
                    }
                }
                array_splice($forums[$forum->category_id], $index + 1, 0, array($forum));
            }
        }
        return $forums;
    }
}