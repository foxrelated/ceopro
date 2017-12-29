<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_ForumController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (0 !== ($forum_id = (int)$this -> _getParam('forum_id')) && null !== ($forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id)))
		{
			if (!Engine_Api::_() -> core() -> hasSubject($forum -> getType()))
			{
				Engine_Api::_() -> core() -> setSubject($forum);
			}
		}
		else
		if (0 !== ($category_id = (int)$this -> _getParam('category_id')) && null !== ($category = Engine_Api::_() -> getItem('ynforum_category', $category_id)))
		{
			Engine_Api::_() -> core() -> setSubject($category);
		}
	}

	public function viewNotApprovedPostsAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}

		$forum = Engine_Api::_() -> core() -> getSubject();

		if (!$this -> _helper -> requireAuth -> setAuthParams('forum', null, 'view') -> isValid())
		{
			return;
		}

		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		// check the approve permission of the logging user
		if (!Engine_Api::_() -> authorization() -> isAllowed('forum', $viewer -> level_id, 'yntopic.approve'))
		{
			$listItemModerator = Engine_Api::_() -> getItemTable('ynforum_list_item') -> getModeratorItem($forum -> getIdentity(), $viewer -> getIdentity());
			if (!$this -> _helper -> requireAuth() -> setAuthParams($forum, $listItemModerator, 'yntopic.approve') -> isValid())
			{
				return;
			}
		}

		$postTable = Engine_Api::_() -> getItemTable('ynforum_post');
		$select = $postTable -> select() -> where('forum_id = ?', $forum -> getIdentity()) -> where('approved = ?', 0) -> order('modified_date DESC');
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$paginator -> setItemCountPerPage($settings -> getSetting('forum_topic_pagelength'));

		$topics = array();
		foreach ($paginator->getCurrentItems() as $post)
		{
			if (!array_key_exists($post -> topic_id, $topics))
			{
				$topics[$post -> topic_id] = $post -> getParent();
			}
			$topics[$post -> topic_id] -> addPost($post);
		}

		$this -> view -> forum = $forum = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> topics = $topics;
	}

	public function viewAction()
	{
		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}
		// check forum level permission
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams('forum', null, 'view') -> isValid())
		{
			return;
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$category = $forum -> getParent();
		
		// check categoty permission
		if(!Engine_Api::_()->authorization()->isAllowed($category, $viewer, 'forumcat.view'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$forum -> checkPermission($viewer, 'forum', 'view')) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> forum = $forum = Engine_Api::_() -> core() -> getSubject();
		if($this -> _getParam('mark_read_all'))
		{
			$forum -> markReadAll($viewer);
			$this->_redirectCustom(array(
	            'route' => 'ynforum_forum',
	            'reset' => true,
	            'forum_id' => $forum->getIdentity(),
	            'slug' => $forum->getSlug(),
	            'action' => 'view'
	        ));
		}
		if ($this->getRequest()->isPost()) 
		{
			$values = $this->getRequest()->getPost();
			$ids = explode(',', $values['topic_ids']);
			switch ($values['topic_moderate']) 
			{
				case 'delete':
					 if($forum -> checkPermission($viewer, 'forum', 'yntopic.delete'))
					 {
						foreach($ids as $id)
						{
							if($id)
							{
								$topic = Engine_Api::_() -> getItem('ynforum_topic', $id);
								$topic->delete();
							}
						}
					 }
					break;
				case 'stick':
					if($forum -> checkPermission($viewer, 'forum', 'yntopic.sticky'))
					 {
						foreach($ids as $id)
						{
							if($id)
							{
								$topic = Engine_Api::_() -> getItem('ynforum_topic', $id);
								$topic->sticky = 1;
								$topic -> save(); 
							}
						}
					 }
					break;
				case 'unstick':
					if($forum -> checkPermission($viewer, 'forum', 'yntopic.sticky'))
					 {
						foreach($ids as $id)
						{
							if($id)
							{
								$topic = Engine_Api::_() -> getItem('ynforum_topic', $id);
								$topic->sticky = 0;
								$topic -> save(); 
							}
						}
					 }
					break;
				case 'close':
					if($forum -> checkPermission($viewer, 'forum', 'yntopic.close'))
					 {
						foreach($ids as $id)
						{
							if($id)
							{
								$topic = Engine_Api::_() -> getItem('ynforum_topic', $id);
								$topic->closed = 1;
								$topic -> save(); 
							}
						}
					 }
					break;
				case 'open':
					if($forum -> checkPermission($viewer, 'forum', 'yntopic.close'))
					 {
						foreach($ids as $id)
						{
							if($id)
							{
								$topic = Engine_Api::_() -> getItem('ynforum_topic', $id);
								$topic->closed = 0;
								$topic -> save(); 
							}
						}
					 }
					break;
			}
		}
		// Increment view count
		$forum -> view_count = new Zend_Db_Expr('view_count + 1');
		$forum -> save();


		$this -> view -> canPost = $forum -> authorization() -> isAllowed(null, 'topic.create');
		$this -> view -> canEdit = false;
		$this -> view -> canDelete = false;
		$this -> view -> canApprove = false;
		$this -> view -> canSticky = false;
		$this -> view -> canClose = false;
		$this -> view -> canMove = false;

		if ($viewer && $viewer -> getIdentity())
		{
			$this->view->canEdit = $forum -> checkPermission($viewer, 'forum', 'yntopic.edit');
            $this->view->canDelete = $forum -> checkPermission($viewer, 'forum', 'yntopic.delete');
            $this->view->canSticky = $forum -> checkPermission($viewer, 'forum', 'yntopic.sticky');
            $this->view->canClose = $forum -> checkPermission($viewer, 'forum', 'yntopic.close');
            $this->view->canMove = $forum -> checkPermission($viewer, 'forum', 'yntopic.move');
            $this->view->canApprove = $forum -> checkPermission($viewer, 'forum', 'yntopic.approve');
		}
		if (!Engine_Api::_() -> authorization() -> isAllowed('forum', null, 'yntopic.approve'))
		{
			$this->view->canApprove = $forum -> checkPermission($viewer, 'forum', 'yntopic.approve');
		}
		$this -> view -> allowHtml = (bool)$settings -> getSetting('forum_html', 0);
		$this -> view -> allowBbcode = (bool)$settings -> getSetting('forum_bbcode', 0);

		$this -> view -> subForums = $subForums = $forum -> getChildrenForum();
		$lastPostIds = array();
		$postTable = Engine_Api::_() -> getItemTable('ynforum_post');
		$topicTable = Engine_Api::_() -> getItemTable('ynforum_topic');
		$categoryTable = Engine_Api::_()->getItemTable('ynforum_category');
		$forumTable = Engine_Api::_()->getItemTable('ynforum_forum');
		$this->view->categories = $categoryTable->getCategoriesOrderByLevel();
		$forums = $forumTable->fetchAllAndOrderByHierachy();
		$this->view->forums = $forums;
		$this->view->check_permission = $check_permission = $settings->getSetting('forum_permission_see_forum',0);
		foreach ($subForums as $subForum)
		{
			$lastPostIds[] = $subForum -> lastpost_id;
		}
		$lastPosts = array();
		$lastTopicIds = array();
		foreach ($postTable->find($lastPostIds) as $post)
		{
			$lastPosts[$post -> getIdentity()] = $post;
			array_push($lastTopicIds, $post -> topic_id);
		}
		$lastTopics = array();
		foreach ($topicTable->find($lastTopicIds) as $lastTopic)
		{
			$lastTopics[$lastTopic -> getIdentity()] = $lastTopic;
		}
		
		$request = $this -> getRequest();
		$topicFrom = $request -> getQuery('topic_from');
		$sortTopicBy = $request -> getQuery('sort_topic_by');
		$orderDirection = $request -> getQuery('order_direction');
		
		$Name = $topicTable -> info('name');
		if($sortTopicBy == 'displayname')
		{
			// Make paginator			
			$select = $topicTable -> select()->from($Name,"$Name.*,(SELECT displayname FROM engine4_users WHERE engine4_users.user_id = $Name.user_id ) AS displayname")-> setIntegrityCheck(false);
		}
		else {
			// Make paginator
			$select = $topicTable -> select();
		}		
		
		$select -> where('forum_id = ?', $forum -> getIdentity());
		$select -> where('sticky = ?', 0);
		if ($topicFrom)
		{
			$select -> where('modified_date > ?', new Zend_Db_Expr(sprintf('DATE_SUB(NOW(), INTERVAL %d DAY)', $topicFrom)));
		}

		if ($sortTopicBy)
		{
			if (!$orderDirection)
			{
				$orderDirection = 'asc';
			}
			$select -> order($sortTopicBy . ' ' . $orderDirection);
		}
		else
		{
			$select -> order('modified_date DESC');
		}
		if (!$this -> view -> canApprove)
		{
			$select -> where('approved = 1 or user_id = ?', $viewer -> getIdentity());
		}

		$this -> view -> topicFrom = $topicFrom;
		$this -> view -> sortTopicBy = $sortTopicBy;
		$this -> view -> orderDirection = $orderDirection;

		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage($settings -> getSetting('forum_forum_pagelength'));
		$this -> view -> forum_topic_pagelength = $settings -> getSetting('forum_topic_pagelength');

		$list = $forum -> getModeratorList();
		$moderators = $list -> getAllChildren();
		
		//check moderator deleted
		$arr_temp = array();
		foreach($moderators as $moderator)
		{
			if($moderator -> getIdentity())
				$arr_temp[] = $moderator;
		}
		$this -> view -> moderators = $arr_temp;

		$categoryTable = Engine_Api::_() -> getItemTable('ynforum_category');
		$cats = $categoryTable -> fetchAll($categoryTable -> select() -> order('order ASC'));
		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[$cat -> getIdentity()] = $cat;
		}
		$curCat = $categories[$forum -> category_id];
		$linkedCategories = array();
		do
		{
			$linkedCategories[] = $curCat;
			if (!$curCat -> parent_category_id)
			{
				break;
			}
			$curCat = $categories[$curCat -> parent_category_id];
		}
		while (true);

		$this -> view -> linkedCategories = $linkedCategories;
		$this -> view -> lastTopics = $lastTopics;
		$this -> view -> lastPosts = $lastPosts;
		$this -> view -> stickyTopics = $topicTable -> fetchAll($topicTable -> select() -> where('forum_id = ?', $forum -> getIdentity()) -> where('sticky = ?', 1));
		$this -> view -> numberOfPostOfHotTopic = $settings -> getSetting('forum_minimum_post_of_hot_topic', 25);
		// Render
		$this -> _helper -> content -> setEnabled();
	}

	public function topicCreateAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> forum = $forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($forum, null, 'topic.create') -> isValid())
		{
			return;
		}
		if (!$forum -> checkPermission($viewer, 'forum', 'topic.create')) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		$this -> view -> form = $form = new Ynforum_Form_Topic_Create( array('forum' => $forum));
		
		$categoryTable = Engine_Api::_() -> getItemTable('ynforum_category');
		$cats = $categoryTable -> fetchAll($categoryTable -> select() -> order('order ASC'));
		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[$cat -> getIdentity()] = $cat;
		}
		$curCat = $categories[$forum -> category_id];
		$linkedCategories = array();
		do
		{
			$linkedCategories[] = $curCat;
			if (!$curCat -> parent_category_id)
			{
				break;
			}
			$curCat = $categories[$curCat -> parent_category_id];
		}
		while (true);

		$this -> view -> linkedCategories = $linkedCategories;
		$this -> view -> navigationForums = $forum -> getForumNavigations();
		$this->_helper->content->setEnabled();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$values = $form -> getValues();
		$values['user_id'] = $viewer -> getIdentity();
		$values['forum_id'] = $forum -> getIdentity();

		$topicTable = Engine_Api::_() -> getDbtable('topics', 'ynforum');
		$topicWatchesTable = Engine_Api::_() -> getDbtable('topicWatches', 'ynforum');
		$forumWatchesTable = Engine_Api::_() -> getDbtable('forumWatches', 'ynforum');
		$postTable = Engine_Api::_() -> getDbtable('posts', 'ynforum');
		$userTable = Engine_Api::_() -> getItemTable('user');

		$db = $topicTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create topic
			$topic = $topicTable -> createRow();
			$topic -> setFromArray($values);
			$topic -> title = htmlspecialchars($values['title']);
			$topic -> description = $values['body'];
			$topic -> save();

			// Create post
			$values['topic_id'] = $topic -> getIdentity();

			$post = $postTable -> createRow();
			$post -> setFromArray($values);
			$post -> save();

			$topic -> firstpost_id = $post -> getIdentity();
			$topic -> save();

			if (!empty($values['photo']))
			{
				$post -> setPhoto($form -> photo);
			}
			
			//determine filename and extension
			$info = pathinfo($form -> attach -> getFileName(null, false));
			$filename = $info['filename'];
			$ext = $info['extension'] ? "." . $info['extension'] : "";
			//filter for renaming.. prepend with current time
			$form -> attach -> addFilter(new Zend_Filter_File_Rename( array(
				"target" => time() . $filename . $ext,
				"overwrite" => true
			)));
			$form -> getValue('attach');
			$values = $form -> getValues();
			if (!empty($values['attach']))
			{
				$name = $filename . $ext;
				$title = $filename . $ext;
				$post -> saveAttach($name, $title);
			}
			
			$auth = Engine_Api::_() -> authorization() -> context;
			$auth -> setAllowed($topic, 'registered', 'create', true);

			// Add activity
			if ($post -> approved)
			{
				// Create topic watch
				$topicWatchesTable -> insert(array(
					'resource_id' => $forum -> getIdentity(),
					'topic_id' => $topic -> getIdentity(),
					'user_id' => $viewer -> getIdentity(),
					'watch' => (bool)$values['watch'],
				));

				$notifyUserIds = $forumWatchesTable -> select() -> from($forumWatchesTable -> info('name'), 'user_id') -> where('forum_id = ?', $forum -> getIdentity()) -> where('watch = ?', 1) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				foreach ($userTable->find($notifyUserIds) as $notifyUser)
				{
					// Don't notify self
					if ($notifyUser -> isSelf($viewer))
					{
						continue;
					}

					$notifyApi -> addNotification($notifyUser, $viewer, $topic, 'ynforum_topic_create', array('message' => $this -> view -> BBCode($post -> body),
						// // @todo make sure this works
					));
				}
				/**
				 * WAWRNING: DO NOT REMOVE TRY/CATCH
				 * fixed issue: conflict with semod module.
				 */
				try
				{
					$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
					$action = $activityApi -> addActivity($viewer, $topic, 'ynforum_topic_create');
					if ($action)
					{
						$action -> attach($topic);
					}
				}
				catch(Exception $ex)
				{
					// silent

				}
			}

			$db -> commit();

			$submit = $this -> getRequest() -> getPost();
			if (isset($submit['managePhoto'])) {
				return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-photos', 'post_id' => $post -> getIdentity(), 'forum_id' => $forum->getIdentity()), 'ynforum_post', true);
			}
		} catch (Exception $e) {
			$db -> rollBack();
			if (APPLICATION_ENV == 'developement')
			{
				throw $e;
			}
		}
		return $this -> _redirectCustom($post);
	}

	public function searchAction()
	{
		$forum_id = $this -> _getParam('forum_id');
		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}

		$request = $this -> getRequest();
		$title = $request -> getQuery('title');
		$searchInSubForums = $request -> getQuery('search_in_subforums');
		$forumIds = array($forum -> getIdentity());
		if ($searchInSubForums)
		{
			$subForums = Engine_Api::_() -> getItemTable('ynforum_forum') -> fetchAll(array('parent_forum_id = ?' => $forum_id));
			foreach ($subForums as $subForum)
			{
				$forumIds[] = $subForum -> getIdentity();
			}
		}
		$this -> view -> canPost = $forum -> authorization() -> isAllowed(null, 'topic.create');
		$topicSelect = Engine_Api::_() -> getItemTable('ynforum_topic') -> searchTopics($forumIds, $title);

		$settings = Engine_Api::_() -> getApi('settings', 'core');

		$this -> view -> paginator = $paginator = Zend_Paginator::factory($topicSelect);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage($settings -> getSetting('forum_forum_pagelength'));
		$this -> view -> forum = $forum = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> forum_topic_pagelength = $settings -> getSetting('forum_topic_pagelength');
		$this -> view -> searchInSubForums = $searchInSubForums;
		$this -> view -> title = $title;

		$categoryTable = Engine_Api::_() -> getItemTable('ynforum_category');
		$cats = $categoryTable -> fetchAll($categoryTable -> select() -> order('order ASC'));
		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[$cat -> getIdentity()] = $cat;
		}
		$curCat = $categories[$forum -> category_id];
		$linkedCategories = array();
		do
		{
			$linkedCategories[] = $curCat;
			if (!$curCat -> parent_category_id)
			{
				break;
			}
			$curCat = $categories[$curCat -> parent_category_id];
		}
		while (true);
		$this -> view -> linkedCategories = $linkedCategories;
		$this -> view -> navigationForums = $forum -> getForumNavigations();
		$this -> view -> numberOfPostOfHotTopic = $settings -> getSetting('forum_minimum_post_of_hot_topic', 25);
	}

	public function watchAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}

		$watch = $this -> _getParam('watch', true);
		$form = $this -> view -> form = new Ynforum_Form_Forum_Watch( array('watch' => $watch));

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$values = $form -> getValues();
		$watchAllSubForums = $values['watch_sub_forum'];

		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try
		{
			$forum -> watchForum($viewer -> getIdentity(), $watchAllSubForums, $watch);
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		$message = Zend_Registry::get('Zend_Translate') -> _('The forum is watched successfully.');
		if(!$watch)
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('You are no longer watching this forum.');
		}
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array($message),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}

	public function newestPostsAction()
	{
		if (!$this -> _helper -> requireSubject('forum') -> isValid())
		{
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid())
		{
			return;
		}

		$postTable = Engine_Api::_() -> getItemTable('ynforum_post');
		$forum_ids = array();
		$forum_ids[] = $forum -> getIdentity();
		$subForums = $forum -> getChildrenForum();
		foreach($subForums as $subForum)
		{
			$forum_ids[] = $subForum -> getIdentity();
		}
		$select = $postTable -> select() -> where('forum_id IN (?)', $forum_ids) -> where('approved = ?', 1) -> order('creation_date DESC');
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$paginator -> setItemCountPerPage($settings -> getSetting('forum_topic_pagelength'));

		$topics = array();
		foreach ($paginator->getCurrentItems() as $post)
		{
			if (!array_key_exists($post -> topic_id, $topics))
			{
				$topic = Engine_Api::_() -> getItem('ynforum_topic', $post -> topic_id);
				if(!$topic)
				{
					continue;
				}
				$topics[$post -> topic_id] = $topic;
			}
			$topics[$post -> topic_id] -> addPost($post);
		}

		$this -> view -> forum = $forum = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> topics = $topics;
	}

}
