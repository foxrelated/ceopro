<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ListItems.php 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Model_DbTable_ListItems extends Engine_Db_Table {

    protected $_rowClass = 'Ynforum_Model_ListItem';
    protected $_name = 'forum_listitems';

    public function getModeratorItem($forum_id, $moderator_id) {       
        $listTable = Engine_Api::_()->getItemTable('ynforum_list');        
        $lists = $listTable->fetchAll(array('owner_id = ?' => $forum_id));
        
        $listItemTable = Engine_Api::_()->getItemTable('ynforum_list_item');        
        $listItemSelect = $listItemTable->select();
        $listItemSelect = $listItemSelect->where('child_id = ?',  $moderator_id);
        $listIds = array();
        foreach($lists as $list) {
            $listIds[] = $list->getIdentity();
        }    
        if (count($listIds) > 0) {
            $listItemSelect->where('list_id IN (?)', $listIds);
            return $listItemTable->fetchRow($listItemSelect);
        }
    }
}