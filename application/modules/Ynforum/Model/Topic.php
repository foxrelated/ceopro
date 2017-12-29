<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */

class Ynforum_Model_Topic extends Core_Model_Item_Abstract
{
	private $_posts;

	protected $_parent_type = 'ynforum_forum';
	protected $_owner_type = 'user';
	protected $_children_types = array('ynforum_post');
	protected $_type = 'forum_topic';

	public function addPost($post)
	{
		if (!$this -> _posts)
		{
			$this -> _posts = array();
		}
		$this -> _posts[] = $post;
	}

	public function getPosts()
	{
		return $this -> _posts;
	}

	public function getDescription()
	{
		if (!isset($this -> store() -> firstPost))
		{
			$postTable = Engine_Api::_() -> getDbtable('posts', 'ynforum');
			$postSelect = $postTable -> select() -> where('topic_id = ?', $this -> getIdentity()) -> order('post_id ASC') -> limit(1);
			$this -> store() -> firstPost = $postTable -> fetchRow($postSelect);
		}
		if (isset($this -> store() -> firstPost))
		{
			//            return strip_tags($this->store()->firstPost->body);
			// @todo decide how we want to handle multibyte string functions
			$tmpBody = strip_tags($this -> store() -> firstPost -> body);
			$tmpBody = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $tmpBody);
			return (Engine_String::strlen($tmpBody) > 350 ? Engine_String::substr($tmpBody, 0, 350) . '...' : $tmpBody);
		}
		return '';
	}

	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'ynforum_topic',
			'reset' => true,
			'topic_id' => $this -> getIdentity(),
			'slug' => $this -> getSlug(),
			'action' => 'view',
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	// hooks

	protected function _insert()
	{
		if (empty($this -> forum_id))
		{
			throw new Forum_Model_Exception('Cannot have a topic without a forum');
		}

		if (empty($this -> user_id))
		{
			throw new Forum_Model_Exception('Cannot have a topic without a user');
		}

		$settings = Engine_Api::_() -> getApi('settings', 'core');

		$forum = $this -> getParent();
		$forum -> topic_count = new Zend_Db_Expr('topic_count + 1');
		$forum -> modified_date = date('Y-m-d H:i:s');

		if (!$forum -> isModerator($this -> getOwner()))
		{
			$settings = Engine_Api::_() -> getApi('settings', 'core');
			$this -> approved = $settings -> getSetting('forum_approve_topic', 0);
		}
		else
		{
			$this -> approved = 1;
		}

		// Increment parent topic count
		if ($this -> approved)
		{
			//            $this->_updateStatisticWhenATopicIsApproved();
			$forum -> approved_topic_count = new Zend_Db_Expr('approved_topic_count + 1');
		}
		$forum -> save();

		parent::_insert();
	}

	protected function _update()
	{
		if (empty($this -> forum_id))
		{
			throw new Forum_Model_Exception('Cannot have a topic without a forum');
		}

		if (empty($this -> user_id))
		{
			throw new Forum_Model_Exception('Cannot have a topic without a user');
		}

		if (!empty($this -> _modifiedFields['forum_id']))
		{
			$originalForumIdentity = $this -> getTable() -> select() -> from($this -> getTable() -> info('name'), 'forum_id') -> where('topic_id = ?', $this -> getIdentity()) -> limit(1) -> query() -> fetchColumn(0);

			if ($originalForumIdentity != $this -> forum_id)
			{
				$postsTable = Engine_Api::_() -> getItemTable('ynforum_post');

				$topicLastPost = $this -> getLastCreatedPost();

				$oldForum = Engine_Api::_() -> getItem('ynforum_forum', $originalForumIdentity);
				$newForum = Engine_Api::_() -> getItem('ynforum_forum', $this -> forum_id);

				$oldForumLastPost = $oldForum -> getLastCreatedPost();
				$newForumLastPost = $newForum -> getLastCreatedPost();

				// Update old forum
				if ($this -> approved)
				{
					$oldForum -> approved_topic_count = new Zend_Db_Expr('approved_topic_count - 1');
					$oldForum -> approved_post_count = new Zend_Db_Expr(sprintf('approved_post_count - %d', $this -> approved_post_count));
				}
				$oldForum -> topic_count = new Zend_Db_Expr('topic_count - 1');
				$oldForum -> post_count = new Zend_Db_Expr(sprintf('post_count - %d', $this -> post_count));

				if (!$oldForumLastPost || $oldForumLastPost -> topic_id == $this -> getIdentity())
				{
					// Update old forum last post
					$oldForumNewLastPost = $postsTable -> select() -> from($postsTable -> info('name'), array(
						'post_id',
						'user_id'
					)) -> where('forum_id = ?', $originalForumIdentity) -> where('topic_id != ?', $this -> getIdentity()) -> where('approved = 1') -> order('post_id DESC') -> limit(1) -> query() -> fetch();
					if ($oldForumNewLastPost)
					{
						$oldForum -> lastpost_id = $oldForumNewLastPost['post_id'];
						$oldForum -> lastposter_id = $oldForumNewLastPost['user_id'];
					}
					else
					{
						$oldForum -> lastpost_id = 0;
						$oldForum -> lastposter_id = 0;
					}
				}
				$oldForum -> save();

				// Update new forum
				if ($this -> approved)
				{
					$newForum -> approved_topic_count = new Zend_Db_Expr('approved_topic_count + 1');
					$newForum -> approved_post_count = new Zend_Db_Expr(sprintf('approved_post_count + %d', $this -> approved_post_count));
				}
				$newForum -> topic_count = new Zend_Db_Expr('topic_count + 1');
				$newForum -> post_count = new Zend_Db_Expr(sprintf('post_count + %d', $this -> post_count));
				if (!$newForumLastPost || strtotime($topicLastPost -> creation_date) > strtotime($newForumLastPost -> creation_date))
				{
					// Update new forum last post
					$newForum -> lastpost_id = $topicLastPost -> post_id;
					$newForum -> lastposter_id = $topicLastPost -> user_id;
				}
				if (strtotime($topicLastPost -> creation_date) > strtotime($newForum -> modified_date))
				{
					$newForum -> modified_date = $topicLastPost -> creation_date;
				}
				$newForum -> save();

				// Update posts
				$postsTable = Engine_Api::_() -> getItemTable('ynforum_post');
				$postsTable -> update(array('forum_id' => $this -> forum_id, ), array('topic_id = ?' => $this -> getIdentity(), ));
			}
		}

		parent::_update();
	}

	protected function _delete()
	{
		$forum = $this -> getParent();

		// Decrement forum topic and post count
		$forum -> topic_count = new Zend_Db_Expr('topic_count - 1');
		$forum -> post_count = new Zend_Db_Expr(sprintf('post_count - %s', $this -> post_count));

		// Decrement forum approved topic and approved post count
		if ($this -> approved)
		{
			$forum -> approved_topic_count = new Zend_Db_Expr('approved_topic_count - 1');
			$forum -> approved_post_count = new Zend_Db_Expr(sprintf('approved_post_count - %s', $this -> approved_post_count));

			// Update forum last post
			$olderForumLastPost = Engine_Api::_() -> getDbtable('posts', 'ynforum') -> select() -> where('forum_id = ?', $this -> forum_id) -> where('topic_id != ?', $this -> topic_id) -> where('approved = 1') -> order('post_id DESC') -> limit(1) -> query() -> fetch();

			if ($olderForumLastPost['post_id'] != $forum -> lastpost_id)
			{
				if ($olderForumLastPost)
				{
					$forum -> lastpost_id = $olderForumLastPost['post_id'];
					$forum -> lastposter_id = $olderForumLastPost['user_id'];
				}
				else
				{
					$forum -> lastpost_id = null;
					$forum -> lastposter_id = null;
				}
			}
		}

		$forum -> save();

		// Delete all posts
		$table = Engine_Api::_() -> getItemTable('ynforum_post');
		$select = $table -> select() -> where('topic_id = ?', $this -> getIdentity());

		foreach ($table->fetchAll($select) as $post)
		{
			$post -> deletingTopic = true;
			$post -> delete();
		}

		// remove topic views
		Engine_Api::_() -> getDbTable('topicviews', 'ynforum') -> delete(array('topic_id = ?' => $this -> topic_id, ));

		// remove topic watches
		Engine_Api::_() -> getDbTable('topicWatches', 'ynforum') -> delete(array(
			'resource_id = ?' => $this -> forum_id,
			'topic_id = ?' => $this -> topic_id,
		));

		parent::_delete();
	}

	public function getLastCreatedPost()
	{
		$post = Engine_Api::_() -> getItem('ynforum_post', $this -> lastpost_id);
		if (!$post)
		{
			// this can happen if the last post was deleted
			$table = Engine_Api::_() -> getDbTable('posts', 'ynforum');
			$post = $table -> fetchRow(array('topic_id = ?' => $this -> getIdentity()), 'creation_date DESC');
			if ($post)
			{
				// update topic table with valid information
				$db = $table -> getAdapter();
				$db -> beginTransaction();
				try
				{
					$row = Engine_Api::_() -> getItem('ynforum_topic', $this -> getIdentity());
					$row -> lastpost_id = $post -> getIdentity();
					$row -> lastposter_id = $post -> getOwner('user') -> getIdentity();
					$row -> save();
					$db -> commit();
				}
				catch (Exception $e)
				{
					$db -> rollback();
					// @todo silence error?
				}
			}
		}
		return $post;
	}

	public function registerView($user, $last_post_id = 0)
	{
		$table = Engine_Api::_() -> getDbTable('topicviews', 'ynforum');
		$table -> delete(array(
			'topic_id = ?' => $this -> getIdentity(),
			'user_id = ?' => $user -> getIdentity()
		));
		$row = $table -> createRow();
		$row -> user_id = $user -> user_id;
		$row -> topic_id = $this -> topic_id;
		$row -> last_post_id = $last_post_id?$last_post_id:0;
		$row -> last_view_date = date('Y-m-d H:i:s');
		$row -> save();
	}

	public function isViewed($user)
	{
		$table = Engine_Api::_() -> getDbTable('topicviews', 'ynforum');
		$row = $table -> fetchRow($table -> select() -> where('user_id = ?', $user -> getIdentity()) -> where('last_view_date > ?', $this -> modified_date) -> where("topic_id = ?", $this -> getIdentity()));
		return $row != null;
	}
	
	public function isViewedLastPost($user)
	{
		$table = Engine_Api::_() -> getDbTable('topicviews', 'ynforum');
		$row = $table -> fetchRow($table -> select() -> where('user_id = ?', $user -> getIdentity()) -> where("topic_id = ?", $this -> getIdentity())->where("last_post_id >= ?", $this->lastpost_id));
		return $row != null;
	}

	public function getLastPage($per_page)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$canApprove = false;
		if ($viewer && $viewer->getIdentity()) 
		{
			$authorizeApi = Engine_Api::_()->authorization();
			$listItemModerator = Engine_Api::_()->getItemTable('ynforum_list_item')
	                    ->getModeratorItem($this->getParent()->getIdentity(), $viewer->getIdentity());
	            
			$canApprove = $authorizeApi->isAllowed('forum', $viewer->level_id, 'yntopic.approve');
	        if (!$canApprove && $listItemModerator != null) 
	        {
	            $canApprove = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($this->getParent(), $listItemModerator, 'yntopic.approve')->checkRequire();
	        }
		}
		$select = $this->getChildrenSelect('ynforum_post', array('order' => 'post_id ASC'));
        if (!$canApprove) {
            $select->where('approved = 1 or user_id = ? ', (int)$viewer->getIdentity());
        }
        
        $paginator = Zend_Paginator::factory($select);
		return ceil($paginator->getTotalItemCount() / $per_page);
	}

	public function getAuthorizationItem()
	{
		return $this -> getParent();
	}

	protected function _postUpdate()
	{
		// statistic the post count for the topic, forum and user when the post is approved
		if (array_key_exists('approved', $this -> _modifiedFields))
		{
			if ($this -> approved)
			{
				$forum = $this -> getParent();
				$forum -> approved_topic_count = new Zend_Db_Expr('approved_topic_count + 1');
				$forum -> modified_date = date('Y-m-d H:i:s');
				$forum -> save();
			}
		}

		parent::_postUpdate();
	}

	public function getParent($recurseType = null)
	{
		if ($recurseType == 'user')
		{
			return Engine_Api::_() -> getItem('user', $this -> user_id);
		}
		else
		{
			return parent::getParent($recurseType);
		}
	}
	
	public function getMediaType()
	{
		return 'topic';
	}
	
	public function isWatching($owner_id, $forum_id)
	{
		$isWatching = false;
		
		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynforum');
		
		$isWatching = $topicWatchesTable
		->select()
		->from($topicWatchesTable->info('name'), 'watch')
		->where('resource_id = ?', $forum_id)
		->where('topic_id = ?', $this->getIdentity())
		->where('user_id = ?', $owner_id)
		->limit(1)
		->query()
		->fetchColumn(0);
		
		if ($isWatching) 		
			$isWatching = (bool) $isWatching;
		
		
		return $isWatching;
	}

}
