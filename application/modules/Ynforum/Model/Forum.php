<?php
class Ynforum_Model_Forum extends Core_Model_Item_Collectible {
    private $_subForums;
    
    protected $_children_types = array('ynforum_topic');
    protected $_parent_type = 'ynforum_category';
    protected $_owner_type = 'ynforum_category';
    protected $_collection_type = 'ynforum_category';
    protected $_collection_column_name = 'category_id';
    protected $_type = 'forum';        
    
    //We use membership system to manage moderators
    public function membership() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'ynforum'));
    }

    public function getCollection() {
        return Engine_Api::_()->getItem($this->_collection_type, $this->category_id);
    }

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynforum_forum',
            'reset' => true,
            'forum_id' => $this->getIdentity(),
            'slug' => $this->getSlug(),
            'action' => 'view',
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
    }

    public function getSlug($str = null) {
        $translate = Zend_Registry::get('Zend_Translate');
        $title = $translate->translate($this->getTitle());
        return parent::getSlug($title);
    }

    public function getLastCreatedPost() {
        return Engine_Api::_()->getItem('ynforum_post', $this->lastpost_id);
    }

    public function getLastUpdatedTopic() {
        $lastPost = Engine_Api::_()->getItem('ynforum_post', $this->lastpost_id);
        if (!$lastPost)
            return false;
        return Engine_Api::_()->getItem('ynforum_topic', $lastPost->topic_id);
        //return $this->getChildren('forum_topic', array('limit'=>1, 'order'=>'modified_date DESC'))->current();
    }

    // Hooks
    protected function _insert() {
        if (empty($this->category_id)) {
            throw new Ynforum_Model_Exception('Cannot have a forum without a category');
        }

        // Increment parent forum count
        $category = $this->getParent();
        $category->forum_count = new Zend_Db_Expr('forum_count + 1');
        $category->save();

        parent::_insert();
    }

    protected function _update() {
        if (empty($this->category_id)) {
            throw new Ynforum_Model_Exception('Cannot have a forum without a category');
        }

        // check the update to avoid the looping situation when choosing an appropriate parent category
        if (array_key_exists('parent_forum_id', $this->_modifiedFields) && $this->parent_forum_id) {
            $forumTable = $this->getTable();
            $forum = $this;
            do {
                $forum = $forumTable->fetchRow($forumTable->select()->where('forum_id = ?', $forum->parent_forum_id));
                if ($forum->getIdentity() == $this->getIdentity()) {
                    throw new Ynforum_Model_Exception('The forum hierachy is looped');
                }
            } while ($forum->parent_forum_id);
        }
        
        parent::_update();
    }

    protected function _delete() {
        // Decrement parent forum count
        $category = $this->getParent();
        $category->forum_count = new Zend_Db_Expr('forum_count - 1');
        $category->save();

        // Delete all child topics
        $table = Engine_Api::_()->getItemTable('ynforum_topic');
        $select = $table->select()->where('forum_id = ?', $this->getIdentity());
        foreach ($table->fetchAll($select) as $topic) {
            $topic->delete();
        }
        
        // Delete all child forums
        $tableSubForum = Engine_Api::_()->getItemTable('ynforum_forum');
        $selectSubForum = $tableSubForum->select()->where('parent_forum_id = ?', $this->getIdentity());
        foreach ($tableSubForum->fetchAll($selectSubForum) as $subForum) {
            $subForum->delete();
        }

        parent::_delete();
    }

    public function isModerator($user) {
        $list = $this->getModeratorList();
        return $list->has($user);
    }
	
	public function isMember($user) {
        $list = $this->getMemberList();
        return $list->has($user);
    }
	
	public function getMemberList() {
        $table = Engine_Api::_()->getItemTable('ynforum_userview');
        $select = $table->select()
                ->where('owner_id = ?', $this->getIdentity())
                ->limit(1);

        $list = $table->fetchRow($select);
        if (null === $list) {
            $list = $table->createRow();
            $list->setFromArray(array(
                'owner_id' => $this->getIdentity(),
            ));
            $list->save();
        }
		
        return $list;
    }

    public function getModeratorList() {
        $table = Engine_Api::_()->getItemTable('ynforum_list');
        $select = $table->select()
                ->where('owner_id = ?', $this->getIdentity())
                ->limit(1);

        $list = $table->fetchRow($select);

        if (null === $list) {
            $list = $table->createRow();
            $list->setFromArray(array(
                'owner_id' => $this->getIdentity(),
            ));
            $list->save();
        }

        return $list;
    }

    public function getPrevForum() {
        $table = $this->getTable();
        if (!in_array('order', $table->info('cols'))) {
            throw new Core_Model_Item_Exception('Unable to use order as order column doesn\'t exist');
        }

        $select = $table->select()
                ->where('`order` < ?', $this->order)
                // Should be confined to a category
                ->where('`category_id` = ?', $this->category_id)
                ->order('order DESC')
                ->limit(1);

        return $table->fetchRow($select);
    }

    public function moveForumWith($forum) {
        $oldOrder = $this->order;
		$newOrder = $forum->order;
		if($oldOrder == $newOrder)
		{
			$newOrder = $newOrder - 1;
		}
        $this->order = $newOrder;
        $forum->order = $oldOrder;
        $forum->save();
        $this->save();
    }
    
    public function moveUp() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $forums = $table->select()->where('category_id = ?', $this->category_id)->order('order ASC')->query()->fetchAll();
            $newOrder = array();
            foreach ($forums as $forum) {
                if ($this->forum_id == $forum['forum_id']) {
                    $prevForum = array_pop($newOrder);
                    array_push($newOrder, $forum['forum_id']);
                    if ($prevForum) {
                        array_push($newOrder, $prevForum);
                        unset($prevForum);
                    }
                } else {
                    array_push($newOrder, $forum['forum_id']);
                }
            }
            foreach ($table->fetchAll($table->select()) as $row) {
                if ($row->category_id == $this->category_id) {
                    $order = array_search($row->forum_id, $newOrder);
                    $row->order = $order + 1;
                    $row->save();
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function setPhoto($photo) {        
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Event_Model_Exception('invalid argument passed to setPhoto');
        }

        if ($this->photo_id) {
            $this->removeOldPhoto();
        }
        
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => $this->getType()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(56, 41)
                ->write($path . '/m_' . $name)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);

        // Remove temp files
        @unlink($path . '/m_' . $name);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $iMain->getIdentity();
        $this->save();

        return $this;
    }
    
    /**
     * Remove the old thumbnail of a book. 
     * This is used in the case of updating book.
     */
    protected function removeOldPhoto() {
        if ($this->photo_id) {
            $item = Engine_Api::_()->storage()->get($this->photo_id);

            $table = Engine_Api::_()->getItemTable('storage_file');
            $select = $table->select()
                    ->where('parent_type = ?', $this->getType())
                    ->where('parent_id = ?', $this->getIdentity());

            foreach ($table->fetchAll($select) as $file) {
                try {
                    $file->delete();
                } catch (Exception $e) {
                    if (!($e instanceof Engine_Exception)) {
                        $log = Zend_Registry::get('Zend_Log');
                        $log->log($e->__toString(), Zend_Log::WARN);
                    }
                }
            }
        }
    }
    
    public function getChildrenForum() {
        $table = $this->getTable();
        $select = $table->select()->where('parent_forum_id = ?', $this->getIdentity())->order('order ASC');
        return $table->fetchAll($select);        
    }
    
    public function addSubForum($forum) {
        if (!$this->_subForums) {
            $this->_subForums = array();
        }
        $this->_subForums[] = $forum;
    }
    
    public function getSubForums() {
        if (!$this->_subForums) {
            $this->_subForums = array();
        }
        return $this->_subForums;
    }

    public function assignAllModsFromItsParentForumAndCategory() {
        $forum = $this;
        
        $listIds = array();
        while ($forum->parent_forum_id) {
            $forum = Engine_Api::_()->getItem('ynforum_forum', $forum->parent_forum_id);
            $list = $forum->getModeratorList();
            $listIds[] = $list->getIdentity();
        }
        $modIds = array();
        $listItemTable = Engine_Api::_()->getItemTable('ynforum_list_item');
        if (count($listIds) > 0) {
            $listItemSelect = $listItemTable->select()->where('list_id in (?)', $listIds);
            $listItems = $listItemTable->fetchAll($listItemSelect);
            foreach($listItems as $listItem) {
                if ($listItem->child_id && !in_array($listItem->child_id, $modIds)) {
                    $modIds[] = $listItem->child_id;
                }
            }
        }
        
        $category = Engine_Api::_()->getItem('ynforum_category', $forum->category_id);
        $catListIds = array();
        do {
            $catList = $category->getModeratorList();
            $catListIds[] = $catList->list_id;
            if ($category->parent_category_id) {
                $category = Engine_Api::_()->getItem('ynforum_category', $category->parent_category_id);
            } else {
                break;
            }
        } while ($category);
        
        if (count($catListIds) > 0) {
            $catListItemTable = Engine_Api::_()->getItemTable('ynforum_category_list_item');        
            $catListItemSelect = $catListItemTable->select()->where('list_id in (?)', $catListIds);
            $catListItems = $catListItemTable->fetchAll($catListItemSelect);
            foreach($catListItems as $catListItem) {
                if ($catListItem->child_id && !in_array($catListItem->child_id, $modIds)) {
                    $modIds[] = $catListItem->child_id;
                }
            }
        }
        
        $list = $this->getModeratorList();
        
        foreach ($modIds as $modId) {
            $listItem = $listItemTable->createRow();
            $listItem->list_id = $list->getIdentity();
            $listItem->child_id = $modId;
            $listItem->save();
            $list->child_count++;
        }
        $list->save();
    }
    
    public function removeMod($user, $removeModFromItsChildren = false) {         
        $list = $this->getModeratorList();
        
        if ($list->has($user)) {
            $list->remove($user);
        }
        
        if ($removeModFromItsChildren) {            
            foreach ($this->getChildrenForum() as $forum) {
                $forum->removeMod($user, $removeModFromItsChildren);
            }
        }
    }
	
	public function removeMember($user) {         
        $list = $this->getMemberList();
        
        if ($list->has($user)) {
            $list->remove($user);
        }
    }
    
    public function addMod($user, $addModToItsChildren = false) {
        $list = $this->getModeratorList();
        if (!$list->has($user)) {
            $list->add($user);
            // add permissions for the new moderator
            $role = $list->get($user);
            $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
            $permissions = array('yntopic.view');
            foreach($permissions as $permission) {
                $allowTable->setAllowed($this, $role, $permission, 1);
            }
        }
        if ($addModToItsChildren) {            
            foreach ($this->getChildrenForum() as $forum) {
                $forum->addMod($user, $addModToItsChildren);
            }
        }
    }
    
	public function addMember($user) {
        $list = $this->getMemberList();
        if (!$list->has($user)) {
            $list->add($user);
        }
    }
	
    public function getHighestOrderOfSubForums() {
        $table = $this->getTable();
        $select = $table->select();
        $select->from($table->info('name'), new Zend_Db_Expr('MAX(`order`) as max_order'))
                ->where('parent_forum_id = ?', $this->getIdentity());

        $data = $select->query()->fetch();
        $next = (int) @$data['max_order'];
        return $next;
    }
    
    public function getForumNavigations() {
        $navigationForums = array($this);
        $tempForum = $this;        
        $forumTable = $this->getTable();
        while ($tempForum->parent_forum_id) {
            $tempForum = $forumTable->fetchRow($forumTable->select()->where('forum_id = ?', $tempForum->parent_forum_id));
            if ($tempForum) {
                array_push($navigationForums, $tempForum);
            } else {
                break;
            }
        }
        return array_reverse($navigationForums);
    }
    
    public function watchForum($userId, $watchAllSubForums = false, $watch = true) {
        $forumWatchesTable = Engine_Api::_()->getDbTable('forumWatches', 'ynforum');
        
        $isWatching = $forumWatchesTable
                    ->select()
                    ->from($forumWatchesTable->info('name'), 'watch')
                    ->where('forum_id = ?', $this->getIdentity())
                    ->where('user_id = ?', $userId)
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0);
        
        if (false === $isWatching) {
            $forumWatchesTable->insert(array(
                'forum_id' => $this->getIdentity(),
                'user_id' => $userId,
                'watch' => (bool) $watch,
            ));
        } else if ($watch != $isWatching) {
            $forumWatchesTable->update(array(
                'watch' => (bool) $watch,
                    ), array(
                'forum_id = ?' => $this->getIdentity(),
                'user_id = ?' => $userId,
            ));
        }
        
        if ($watchAllSubForums) {            
            foreach ($this->getChildrenForum() as $forum) {
                $forum->watchForum($userId, $watchAllSubForums, $watch);
            }
        }
    }
	/** check permission
	 * 
	 * @author MinhNC
	 */
	 public function checkPermission($viewer, $type, $action)
	 {
	 	$permisson = Engine_Api::_()->authorization()->isAllowed($type, $viewer, $action);
		if(!$permisson)
		{
			return false;
		}
		$level = 5;
		if($viewer -> getIdentity())
		{
			$level = $viewer->level_id;
		}
		$permisson = Engine_Api::_()->getDbtable('memberlevelpermission', 'ynforum') -> isAllowed($type, $level, $action, $this -> getIdentity());
	 	if (!$permisson) 
	 	{	
            $listItemModerator = Engine_Api::_()->getItemTable('ynforum_list_item') ->getModeratorItem($this->getIdentity(), $viewer->getIdentity());
            if ($listItemModerator != null) 
            {
            	$allowTable = Engine_Api::_() -> getDbtable('allow', 'authorization');
                if ($allowTable -> isAllowed($this, $listItemModerator, $action)) 
                {
                    return TRUE;
                }
				else 
				{
					return FALSE;
				}
            }
			else 
			{
                return FALSE;
            }
        }
		return $permisson;
	}
	public function markReadAll($viewer)
	{
		$table_topic = Engine_Api::_()->getItemTable('ynforum_topic');
		$select = $table_topic -> select();
		$select -> where('forum_id = ?', $this -> getIdentity());
		$topics = $table_topic -> fetchAll($select);
		foreach ($topics as $topic) 
		{
			$last_post_id = $topic->lastpost_id;
			if(!$topic->lastpost_id)
			{
				$table = Engine_Api::_()->getItemTable('ynforum_post');
				$select = $table -> select();
				$select -> where('topic_id = ?', $topic -> getIdentity());
				$posts = $table -> fetchAll($select);
				$last_post = $posts -> toArray();
				$last_post = end($last_post);
				$last_post_id = $last_post['post_id'];
			}
	        $topic->registerView($viewer, $last_post_id);
		}
	}
	public function checkEventHighlight($item_id, $type)
	{
		$table = Engine_Api::_() -> getDbTable('highlights', 'ynforum');
		$select = $table -> select() -> where("forum_id = ?", $this -> getIdentity()) -> where('item_id = ?', $item_id) -> where("type = ?", $type) -> limit(1);
		$row = $table -> fetchRow($select);
		if($row)
		{
			return $row -> highlight;
		}
		return false;
	}
	
	public function getTotalTopic()
	{
		$total = $this -> approved_topic_count;
		$subForums = $this -> getSubForums();
		foreach ($subForums as $subForum) 
		{
			$total += $subForum -> 	approved_topic_count;
		}
		return $total;
	}
	
	public function getTotalPost()
	{
		$total = $this -> approved_post_count;
		$subForums = $this -> getSubForums();
		foreach ($subForums as $subForum) 
		{
			$total += $subForum -> 	approved_post_count;
		}
		return $total;
	}
}