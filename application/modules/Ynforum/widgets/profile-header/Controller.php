<?php

class Ynforum_Widget_ProfileHeaderController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		// Get subject and check auth
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$forum -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> setNoRender();
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> forum = $forum = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> navigationForums = $forum -> getForumNavigations();
		// Check watching
		$isWatching = null;
		if ($viewer -> getIdentity())
		{
			$forumWatchesTable = Engine_Api::_() -> getDbtable('forumWatches', 'ynforum');
			$isWatching = $forumWatchesTable -> select() -> from($forumWatchesTable -> info('name'), 'watch') -> where('forum_id = ?', $forum -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1) -> query() -> fetchColumn(0);
			if (false === $isWatching)
			{
				$isWatching = null;
			}
			else
			{
				$isWatching = (bool)$isWatching;
			}
		}
		$this -> view -> isWatching = $isWatching;
		$this -> view -> canPost = $forum -> authorization() -> isAllowed(null, 'post.create');
		$this -> view -> canTopic = $forum -> authorization() -> isAllowed(null, 'topic.create');
		$this -> view -> canEdit = false;
		$this -> view -> canDelete = false;
		$this -> view -> canApprove = false;
		$this -> view -> canSticky = false;
		$this -> view -> canClose = false;
		$this -> view -> canMove = false;

		if ($viewer && $viewer -> getIdentity())
		{
			$this->view->canTopic = $forum -> checkPermission($viewer, 'forum', 'topic.create');
			$this->view->canPost = $forum -> checkPermission($viewer, 'forum', 'post.create');
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
		
		$request = Zend_Controller_Front::getInstance() -> getRequest();
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
		$this -> view -> searchInSubForums = $searchInSubForums;
		$this -> view -> title = $title;
		
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
	}

}
