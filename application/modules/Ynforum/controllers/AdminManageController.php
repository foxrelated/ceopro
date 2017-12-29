<?php
class Ynforum_AdminManageController extends Core_Controller_Action_Admin {
	// @todo add in stricter settings for admin level checking
	public function indexAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynforum_admin_main', array(), 'ynforum_admin_main_manage');
		$cat_id = $this -> getRequest() -> getParam('cat_id');
		$forum_id = $this -> getRequest() -> getParam('forum_id');
		$categoryTable = Engine_Api::_() -> getItemTable('ynforum_category');

		if ($forum_id) {
			$table = Engine_Api::_() -> getItemTable('ynforum_forum');
			$select = $table -> select() -> order('order ASC');
			$select -> where('forum_id = ?', $forum_id);
			$select -> orWhere('parent_forum_id = ?', $forum_id);
			$forums = array();
			foreach ($table->fetchAll($select) as $forum) {
				$forums[$forum -> getIdentity()] = $forum;
			}
			foreach ($forums as $forum) {
				if ($forum -> parent_forum_id == $forum_id) {
					$forums[$forum_id] -> addSubForum($forum);
				}
			}
			if (array_key_exists($forum_id, $forums)) {
				$this -> view -> navigationForums = $forums[$forum_id] -> getForumNavigations();
				$this -> view -> forums = $forums;
				$this -> view -> forum_id = $forum_id;

				$category = $categoryTable -> fetchRow($categoryTable -> select() -> where('category_id = ?', $forum -> category_id));
			}
		} else {
			$select = $categoryTable -> select() -> order('order ASC');
			if ($cat_id) {
				$select -> where('category_id = ?', $cat_id);
			} else {
				// when there is no category is determined, get the categories with the level zero and one
				$select -> where('level < ?', 1);
			}
			$cats = $categoryTable -> fetchAll($select);
			$fullCats = array();
			foreach ($cats as $cat) {
				$fullCats[$cat -> getIdentity()] = $cat;
				foreach ($cat->getChildrenCategory() as $childCat) {
					$fullCats[$childCat -> getIdentity()] = $childCat;
				}
			}

			if ($cat_id) {
				$category = $fullCats[$cat_id];
				$this -> view -> firstCatgoryLevel = $fullCats[$cat_id] -> level;
			} else {
				$category = null;
				$this -> view -> firstCatgoryLevel = 0;
			}
			$this -> view -> categories = $fullCats;
		}

		if ($category) {
			$categories = $categoryTable -> getCategoriesOrderByLevel();
			$orderCats = array();
			foreach ($categories as $cat) {
				$orderCats[$cat -> getIdentity()] = $cat;
			}
			$curCat = $category;
			$linkedCategories = array($curCat);
			while ($curCat -> parent_category_id != null) {
				$curCat = $orderCats[$curCat -> parent_category_id];
				$linkedCategories[] = $curCat;
			}
			$this -> view -> linkedCategories = $linkedCategories;
		}
	}

	public function moveForumAction() {
		if ($this -> getRequest() -> isPost()) {
			$postRequest = $this -> getRequest() -> getPost();
			$forum_id = $postRequest['id'];
			$other_forum_id = $postRequest['pre_forum_id'];
			$forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
			$other_forum = Engine_Api::_() -> getItem('ynforum_forum', $other_forum_id);

			$forum -> moveForumWith($other_forum);
		}
	}

	public function moveCategoryAction() {
		if ($this -> getRequest() -> isPost()) {
			$postRequest = $this -> getRequest() -> getPost();
			$category_id = $postRequest['id'];
			$other_category_id = $postRequest['pre_category_id'];
			$category = Engine_Api::_() -> getItem('ynforum_category', $category_id);
			$other_category = Engine_Api::_() -> getItem('ynforum_category', $other_category_id);

			$category -> moveCategoryWith($other_category);
		}
	}

	public function editForumAction() {
		$forum_id = $this -> getRequest() -> getParam('forum_id');
		$forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);

		if ($forum) {
			$form = $this -> view -> form = new Ynforum_Form_Admin_Forum_Edit( array('forum' => $forum));
		} else {
			return;
		}

		// Populate
		$form -> populate($forum -> toArray());
		$form -> populate(array('title' => htmlspecialchars_decode($forum -> title), 'description' => htmlspecialchars_decode($forum -> description), ));

		$auth = Engine_Api::_() -> authorization() -> context;
		$allowed = array();
		// populate permission view in forum
		if ($auth -> isAllowed($forum, 'everyone', 'view')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if (Engine_Api::_() -> authorization() -> context -> isAllowed($forum, $level, 'view')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		if (!empty($allowed)) {
			$form -> populate(array('levels' => $allowed, ));
		}

		// populate permission create, edit, delelte forum's event
		if ($auth -> isAllowed($forum, 'everyone', 'forum.events')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if (Engine_Api::_() -> authorization() -> context -> isAllowed($forum, $level, 'forum.events')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		if (!empty($allowed)) {
			$form -> populate(array('forum_events' => $allowed, ));
		}

		// populate permission create, edit, delelte forum's group
		if ($auth -> isAllowed($forum, 'everyone', 'forum.groups')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if (Engine_Api::_() -> authorization() -> context -> isAllowed($forum, $level, 'forum.groups')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		if (!empty($allowed)) {
			$form -> populate(array('forum_groups' => $allowed, ));
		}

		// populate permission create, edit, delelte forum's poll
		if ($auth -> isAllowed($forum, 'everyone', 'forum.polls')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if (Engine_Api::_() -> authorization() -> context -> isAllowed($forum, $level, 'forum.polls')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		if (!empty($allowed)) {
			$form -> populate(array('forum_polls' => $allowed, ));
		}

		// Check request/method
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$values = $this -> _prepareParamsForForum($form -> getInputedValues(), 1);

		$table = Engine_Api::_() -> getItemTable('ynforum_forum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			if ($forum -> category_id != $values['category_id']) {
				$previousCat = Engine_Api::_() -> getItem('ynforum_category', $forum -> category_id);
				if ($previousCat) {
					$previousCat -> forum_count = $previousCat -> forum_count - 1;
					$previousCat -> save();
				}
				$newCat = Engine_Api::_() -> getItem('ynforum_category', $values['category_id']);
				if ($newCat) {
					$newCat -> forum_count = $newCat -> forum_count + 1;
					$newCat -> save();
				}
			}

			$forum -> setFromArray($values);
			$forum -> save();
			if (!empty($values['icon'])) {
				$forum -> setPhoto($this -> view -> form -> icon);
			}

			// Handle permissions
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();

			// Clear permissions
			$auth -> setAllowed($forum, 'everyone', 'view', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'view', false);
			}

			// Add
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'view', true);
			} else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'view', true);
				}
			}

			// Clear permissions create, edit, delete forum's event
			$auth -> setAllowed($forum, 'everyone', 'forum.events', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'forum.events', false);
			}

			// Add permissions create, edit, delete forum's event
			if (count($values['forum_events']) == 0 || count($values['forum_events']) == count($form -> getElement('forum_events') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'forum.events', true);
			} else {
				foreach ($values['forum_events'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'forum.events', true);
				}
			}

			// Clear permissions create, edit, delete forum's group
			$auth -> setAllowed($forum, 'everyone', 'forum.groups', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'forum.groups', false);
			}

			// Add permissions view forum
			if (count($values['forum_groups']) == 0 || count($values['forum_groups']) == count($form -> getElement('forum_groups') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'forum.groups', true);
			} else {
				foreach ($values['forum_groups'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'forum.groups', true);
				}
			}

			// Clear permissions create, edit, delete forum's poll
			$auth -> setAllowed($forum, 'everyone', 'forum.polls', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'forum.polls', false);
			}

			// Add permissions view forum
			if (count($values['forum_polls']) == 0 || count($values['forum_polls']) == count($form -> getElement('forum_polls') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'forum.polls', true);
			} else {
				foreach ($values['forum_polls'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'forum.polls', true);
				}
			}

			// Extra auth stuff
			$auth -> setAllowed($forum, 'registered', 'topic.create', true);
			$auth -> setAllowed($forum, 'registered', 'post.create', true);
			$auth -> setAllowed($forum, 'registered', 'comment', true);

			// Make mod list now
			$list = $forum -> getModeratorList();

			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Forum saved.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function editCategoryAction() {
		$category_id = $this -> _getParam('category_id');
		$category = Engine_Api::_() -> getItem('ynforum_category', $category_id);
		$form = $this -> view -> form = new Ynforum_Form_Admin_Category_Edit( array('category' => $category));

		$auth = Engine_Api::_() -> authorization() -> context;
		$allowed = array();
		// populate permission view forum category
		if ($auth -> isAllowed($category, 'everyone', 'forumcat.view')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if ($auth -> isAllowed($category, $level, 'forumcat.view')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		if (!empty($allowed)) {
			$form -> populate(array('levels' => $allowed, ));
		}

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$values = $form -> getInputedValues();

		// Handle permissions
		$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();

		// Clear permissions view category by level
		$auth -> setAllowed($category, 'everyone', 'forumcat.view', false);
		foreach ($levels as $level) {
			$auth -> setAllowed($category, $level, 'forumcat.view', false);
		}

		// Add permissions view forum
		if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
			$auth -> setAllowed($category, 'everyone', 'forumcat.view', true);
		} else {
			foreach ($values['levels'] as $levelIdentity) {
				$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
				$auth -> setAllowed($category, $level, 'forumcat.view', true);
			}
		}

		if ($values['parent_category_id']) {
			$category -> setFromArray($values);
			$parentCat = Engine_Api::_() -> getItem('ynforum_category', $values['parent_category_id']);
			$category -> order = $parentCat -> order + 1;
			$category -> level = $parentCat -> level + 1;
		} else {
			if ($category -> parent_category_id != null) {
				$category -> setFromArray($values);
				$category -> order = Engine_Api::_() -> ynforum() -> getMaxCategoryOrder() + 1;
				$category -> level = 0;
			}
			$category -> setFromArray($values);
		}

		$category -> save();
		if (!empty($values['icon'])) {
			$category -> setPhoto($this -> view -> form -> icon);
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Category saved.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function addCategoryAction() {
		$this -> view -> form = $form = new Ynforum_Form_Admin_Category_Create();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$this -> view -> form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$table = Engine_Api::_() -> getItemTable('ynforum_category');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$category = $table -> createRow();
			$values = $this -> view -> form -> getInputedValues();
			$category -> setFromArray($values);
			$category -> order = Engine_Api::_() -> ynforum() -> getMaxCategoryOrder() + 1;
			if ($category -> parent_category_id) {
				$parent = Engine_Api::_() -> getItem('ynforum_category', $category -> parent_category_id);
				if ($parent) {
					$category -> level = $parent -> level + 1;
				}
			}
			$category -> save();

			if (!empty($values['icon'])) {
				$category -> setPhoto($this -> view -> form -> icon);
			}

			$db -> commit();

			// Handle permissions
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			// Clear permissions view category by level
			$auth = Engine_Api::_() -> authorization() -> context;
			$auth -> setAllowed($category, 'everyone', 'forumcat.view', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($category, $level, 'forumcat.view', false);
			}

			// Add permissions view forum
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($category, 'everyone', 'forumcat.view', true);
			} else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($category, $level, 'forumcat.view', true);
				}
			}

		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Category added.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	private function _prepareParamsForForum($values, $edit) {
		$parent_category_forum = $values['parent_category_forum'];
		if (strpos($parent_category_forum, 'category_id') !== false) {
			$category_id = (int)substr($parent_category_forum, strlen('category_id='));
			if ($category_id) {
				$values['category_id'] = $category_id;
				$collection = Engine_Api::_() -> getItem('ynforum_category', $category_id);
				if (!$edit)
					$values['order'] = $collection -> getHighestOrder() + 1;
			}
		} else if (strpos($parent_category_forum, 'forum_id') !== false) {
			$forum_id = (int)substr($parent_category_forum, strlen('forum_id='));
			if ($forum_id) {
				$values['parent_forum_id'] = $forum_id;
				$parentForum = Engine_Api::_() -> getItem('ynforum_forum', $values['parent_forum_id']);
				if (!$edit)
					$values['order'] = $parentForum -> getHighestOrderOfSubForums() + 1;
				$values['category_id'] = $parentForum -> category_id;
				$values['level'] = $parentForum -> level + 1;
			}
		}

		return $values;
	}

	public function addForumAction() {
		$form = $this -> view -> form = new Ynforum_Form_Admin_Forum_Create();

		$cat_id = $this -> _getParam('cat_id');
		if ($cat_id) {
			$form -> getElement('parent_category_forum') -> setValue('category_id=' . $cat_id);
			$this -> view -> cat_id = $cat_id;
		}
		$forum_id = $this -> _getParam('forum_id');
		if ($forum_id) {
			$form -> getElement('parent_category_forum') -> setValue('forum_id=' . $forum_id);
			$this -> view -> forum_id = $forum_id;
		}

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$table = Engine_Api::_() -> getItemTable('ynforum_forum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$forum = $table -> createRow();
			$values = $this -> _prepareParamsForForum($form -> getInputedValues());
			$forum -> setFromArray($values);
			$forum -> save();

			if (!empty($values['icon'])) {
				$forum -> setPhoto($this -> view -> form -> icon);
			}

			// assign its parents forum or category moderators as its moderators
			$forum -> assignAllModsFromItsParentForumAndCategory();

			// Handle permissions
			$auth = Engine_Api::_() -> authorization() -> context;
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();

			// Clear permissions view forum
			$auth -> setAllowed($forum, 'everyone', 'view', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'view', false);
			}

			// Add permissions view forum
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'view', true);
			} else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'view', true);
				}
			}

			// Clear permissions create, edit, delete forum's event
			$auth -> setAllowed($forum, 'everyone', 'forum.events', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'forum.events', false);
			}

			// Add permissions view forum
			if (count($values['forum_events']) == 0 || count($values['forum_events']) == count($form -> getElement('forum_events') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'forum.events', true);
			} else {
				foreach ($values['forum_events'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'forum.events', true);
				}
			}

			// Clear permissions create, edit, delete forum's group
			$auth -> setAllowed($forum, 'everyone', 'forum.groups', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'forum.groups', false);
			}

			// Add permissions view forum
			if (count($values['forum_groups']) == 0 || count($values['forum_groups']) == count($form -> getElement('forum_groups') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'forum.groups', true);
			} else {
				foreach ($values['forum_groups'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'forum.groups', true);
				}
			}

			// Clear permissions create, edit, delete forum's poll
			$auth -> setAllowed($forum, 'everyone', 'forum.polls', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($forum, $level, 'forum.polls', false);
			}

			// Add permissions view forum
			if (count($values['forum_polls']) == 0 || count($values['forum_polls']) == count($form -> getElement('forum_polls') -> options)) {
				$auth -> setAllowed($forum, 'everyone', 'forum.polls', true);
			} else {
				foreach ($values['forum_polls'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($forum, $level, 'forum.polls', true);
				}
			}

			// Extra auth stuff
			$auth -> setAllowed($forum, 'registered', 'topic.create', true);
			$auth -> setAllowed($forum, 'registered', 'post.create', true);
			$auth -> setAllowed($forum, 'registered', 'comment', true);

			// Make mod list now
			$list = $forum -> getModeratorList();
			foreach ($list->getAll() as $listItem) {
				$auth -> setAllowed($forum, $listItem, 'yntopic.edit', true);
				$auth -> setAllowed($forum, $listItem, 'yntopic.delete', true);
				$auth -> setAllowed($forum, $listItem, 'yntopic.approve', true);
				$auth -> setAllowed($forum, $listItem, 'yntopic.sticky', true);
				$auth -> setAllowed($forum, $listItem, 'yntopic.close', true);
				$auth -> setAllowed($forum, $listItem, 'yntopic.move', true);
			}

			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Forum added.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function addModeratorAction() {
		$forum_id = $this -> getRequest() -> getParam('forum_id');
		$category_id = $this -> getRequest() -> getParam('category_id');

		if (!empty($forum_id)) {
			$this -> view -> object = $object = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
			$this -> view -> objectParamName = 'forum_id';
			$form = $this -> view -> form = new Ynforum_Form_Admin_Moderator_Create();
		} else if (!empty($category_id)) {
			$this -> view -> object = $object = Engine_Api::_() -> getItem('ynforum_category', $category_id);
			$this -> view -> objectParamName = 'category_id';
			$form = $this -> view -> form = new Ynforum_Form_Admin_Moderator_Create( array('form_type' => 'category'));
		}

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$values = $form -> getValues();
		$user_id = $values['user_id'];

		$moderator = Engine_Api::_() -> getItem('user', $user_id);

		$table = Engine_Api::_() -> getItemTable('ynforum_forum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$object -> addMod($moderator, $form -> getElement('add_moderator') -> getValue());
			$db -> commit();

			$viewer = Engine_Api::_() -> user() -> getViewer();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($moderator, $viewer, $object, 'ynforum_promote');
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Moderator Added.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function addMemberAction() {
		$forum_id = $this -> getRequest() -> getParam('forum_id');

		if (!empty($forum_id)) {
			$this -> view -> object = $object = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
			$this -> view -> objectParamName = 'forum_id';
			$form = $this -> view -> form = new Ynforum_Form_Admin_Member_Create();
		}

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$values = $form -> getValues();
		$user_id = $values['user_id'];

		$member = Engine_Api::_() -> getItem('user', $user_id);

		$table = Engine_Api::_() -> getItemTable('ynforum_forum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$object -> addMember($member);
			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Added.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function userSearchAction() {
		$page = $this -> getRequest() -> getParam('page', 1);
		$username = $this -> getRequest() -> getParam('username');
		$table = Engine_Api::_() -> getDbtable('users', 'user');
		$select = $table -> select();
		if (!empty($username)) {
			$select = $select -> where('username LIKE ? || displayname LIKE ?', '%' . $username . '%');
		}
		$forum_id = $this -> getRequest() -> getParam('forum_id');
		$category_id = $this -> getRequest() -> getParam('category_id');

		if (!empty($forum_id)) {
			$this -> view -> object = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
		} else if (!empty($category_id)) {
			$this -> view -> object = Engine_Api::_() -> getItem('ynforum_category', $category_id);
		}

		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> paginator = $paginator -> setCurrentPageNumber($page);
		$this -> view -> paginator -> setItemCountPerPage(20);
	}

	public function memberSearchAction() {
		$page = $this -> getRequest() -> getParam('page', 1);
		$username = $this -> getRequest() -> getParam('username');
		$table = Engine_Api::_() -> getDbtable('users', 'user');
		$select = $table -> select();
		if (!empty($username)) {
			$select = $select -> where('username LIKE ? || displayname LIKE ?', '%' . $username . '%');
		}
		$forum_id = $this -> getRequest() -> getParam('forum_id');

		if (!empty($forum_id)) {
			$this -> view -> object = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
		}
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> paginator = $paginator -> setCurrentPageNumber($page);
		$this -> view -> paginator -> setItemCountPerPage(20);
	}

	public function removeModeratorAction() {
		$form = $this -> view -> form = new Ynforum_Form_Admin_Moderator_Delete();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$user_id = $this -> getRequest() -> getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);

		$forum_id = $this -> getRequest() -> getParam('forum_id');
		$category_id = $this -> getRequest() -> getParam('category_id');
		if (!empty($forum_id)) {
			$object = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
		} else if (!empty($category_id)) {
			$object = Engine_Api::_() -> getItem('ynforum_category', $category_id);
		}

		$table = Engine_Api::_() -> getItemTable('ynforum_category');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$object -> removeMod($user, $form -> getElement('remove_moderator') -> getValue());
			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		//        $list = $object->getModeratorList();
		//        $list->remove($user);
		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Moderator Removed.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function removeMemberAction() {
		$form = $this -> view -> form = new Ynforum_Form_Admin_Member_Delete();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$user_id = $this -> getRequest() -> getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);

		$forum_id = $this -> getRequest() -> getParam('forum_id');
		if (!empty($forum_id)) {
			$object = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
		}

		$table = Engine_Api::_() -> getItemTable('ynforum_userview');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$object -> removeMember($user);
			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Removed.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function deleteCategoryAction() {
		$form = $this -> view -> form = new Ynforum_Form_Admin_Category_Delete();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$table = Engine_Api::_() -> getItemTable('ynforum_category');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		$category_id = $this -> getRequest() -> getParam('category_id');
		try {
			$category = Engine_Api::_() -> getItem('ynforum_category', $category_id);
			$category -> delete();
			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Category deleted.')), 'layout' => 'default-simple', 'parentRefresh' => true));
	}

	public function deleteForumAction() {
		$form = $this -> view -> form = new Ynforum_Form_Admin_Forum_Delete();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$table = Engine_Api::_() -> getItemTable('ynforum_forum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		$forum_id = $this -> getRequest() -> getParam('forum_id');
		try {
			$forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
			$forum -> delete();
			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Forum deleted.')), 'layout' => 'default-simple', 'parentRefresh' => true));
	}

	//LUAND START Member Level Setting in forum//
	public function memberPermissionAction() {
		$forum_id = $this -> _getParam('forum_id');
		if ($forum_id) {
			$forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
			if ($forum) {
				// Get level id
				if (null !== ($id = $this -> _getParam('id'))) {
					$level = Engine_Api::_() -> getItem('authorization_level', $id);
				} else {
					$level = Engine_Api::_() -> getItemTable('authorization_level') -> getDefaultLevel();
				}

				if (!$level instanceof Authorization_Model_Level) {
					throw new Engine_Exception('missing level');
				}
				$level_id = $id = $level -> level_id;
				// Make form
				$this -> view -> form = $form = new Ynforum_Form_Admin_Settings_Level( array('public' => ( in_array($level -> type, array('public'))), 'moderator' => ( in_array($level -> type, array('admin', 'moderator'))), ));
				$form -> removeElement('commentHtml');
				$form -> level_id -> setValue($level_id);

				//check value is written or Not
				$Memberlevel = Engine_Api::_() -> getDbtable('memberlevelpermission', 'ynforum');
				$flag = $Memberlevel -> select() -> where('forum_id = ?', $forum_id) -> limit(1) -> query() -> fetchColumn();

				if (!$flag) 
				{
					$permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
					// Prepare modified permission keys
					$permissionKeys = array_keys($form -> getValues());
					$fixedPermissionKeys = array();
					foreach ($permissionKeys as $index => $key) {
						if (strpos($key, '_') !== false) {
							list($type, $subtype) = explode('_', $key);
							$fixedPermissionKeys[$type][] = $subtype;
						} else {
							$fixedPermissionKeys['forum'][] = $key;
						}
					}
					$fixedPermissionValues = array();
					foreach ($fixedPermissionKeys as $type => $typeArray) {
						if ($type == 'forum') {
							$typeKey = 'forum';
						} else {
							$typeKey = 'forum_' . $type;
						}
						$values = $permissionsTable -> getAllowed($typeKey, $level_id, $typeArray);

						foreach ($values as $valueKey => $value) {
							if ($type == 'forum') {
								$formKey = $valueKey;
							} else {
								$formKey = $type . '_' . $valueKey;
							}
							$fixedPermissionValues[$formKey] = $value;
						}
					}
				} 
				else 
				{
					$permissionsTable = Engine_Api::_() -> getDbtable('memberlevelpermission', 'ynforum');
					// Prepare modified permission keys
					$permissionKeys = array_keys($form -> getValues());
					$fixedPermissionKeys = array();
					foreach ($permissionKeys as $index => $key) {
						if (strpos($key, '_') !== false) {
							list($type, $subtype) = explode('_', $key);
							$fixedPermissionKeys[$type][] = $subtype;
						} else {
							$fixedPermissionKeys['forum'][] = $key;
						}
					}

					$fixedPermissionValues = array();
					foreach ($fixedPermissionKeys as $type => $typeArray) {
						if ($type == 'forum') {
							$typeKey = 'forum';
						} else {
							$typeKey = 'forum_' . $type;
						}

						$values = $permissionsTable -> getAllowed($typeKey, $level_id, $typeArray, $forum_id);

						foreach ($values as $valueKey => $value) {
							if ($type == 'forum') {
								$formKey = $valueKey;
							} else {
								$formKey = $type . '_' . $valueKey;
							}
							$fixedPermissionValues[$formKey] = $value;
						}
					}
				}
				$form -> populate($fixedPermissionValues);

				// Check method
				if (!$this -> getRequest() -> isPost()) {
					return;
				}
				// Check validitiy
				if (!$form -> isValid($this -> getRequest() -> getPost())) {
					return;
				}

				// Process
				$values = $form -> getValues();
				$fixedPermissionValues = array();
				foreach ($values as $key => $value) {
					if (strpos($key, '_') === false) {
						$fixedPermissionValues['forum'][$key] = $value;
					} else {
						list($type, $subtype) = explode('_', $key);
						$fixedPermissionValues['forum'][$type . '.' . $subtype] = $value;
						$fixedPermissionValues['forum_' . $type][$subtype] = $value;
					}
				}
				$db = $Memberlevel -> getAdapter();
				$db -> beginTransaction();

				try 
				{
					foreach ($fixedPermissionValues as $type => $fixedValues) 
					{
						$Memberlevel -> setAllowed($type, $level_id, $fixedValues, $forum_id);
					}

					// Commit
					$db -> commit();
				} catch (Exception $e) {
					$db -> rollBack();
					throw $e;
				}
				$form -> addNotice('Your changes have been saved');
				return;
			}
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('No forum is selected.')), 'layout' => 'default-simple', 'parentRefresh' => true));
	}

	//LUAND END Member Level Setting in forum//

	public function sharePermissionAction() {
		$forum_id = $this -> _getParam('forum_id');
		if ($forum_id) {
			$forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
			if ($forum) {
				$moderators = $forum -> getModeratorList() -> getAllChildren();
				$form = $this -> view -> form = new Ynforum_Form_Admin_Settings_SharePermission( array('moderators' => $moderators));

				$moderatorDropdownList = $form -> getElement('moderator');
				$moderatorId = $this -> _getParam('mod_id');
				if ($moderatorId) {
					$moderatorDropdownList -> setValue($moderatorId);
				}

				$modId = $moderatorDropdownList -> getValue();
				$allowTable = Engine_Api::_() -> getDbtable('allow', 'authorization');
				$listItemTable = Engine_Api::_() -> getDbtable('listItems', 'ynforum');
				$role = $listItemTable -> getModeratorItem($forum_id, $modId);

				if (!$this -> getRequest() -> isPost()) {
					if ($modId) {
						$values = $form -> getValues();
						$permissionKeys = array_keys($values);
						foreach ($permissionKeys as $index => $key) {
							list($type, $subtype) = explode('_', $key);
							$allow = $allowTable -> getAllowed($forum, $role, $type . '.' . $subtype);
							$form -> getElement($key) -> setValue($allow);
						}
					}
					return;
				}
				if (!$form -> isValid($this -> getRequest() -> getPost())) {
					return;
				}

				$values = $form -> getValues();
				$db = $allowTable -> getAdapter();
				$db -> beginTransaction();
				try {
					foreach ($values as $key => $value) {
						list($type, $subtype) = explode('_', $key);
						$allowTable -> setAllowed($forum, $role, $type . '.' . $subtype, $value);
					}
					$db -> commit();
				} catch(Exception $e) {
					$db -> rollBack();
					throw $e;
				}

				$form -> addNotice('Your changes have been saved');
				return;
			}
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('No forum is selected.')), 'layout' => 'default-simple', 'parentRefresh' => true));
	}

	public function removeForumAction() {
		$form = $this -> view -> form = new Ynforum_Form_Admin_Forum_Remove();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$forum_id = $this -> getRequest() -> getParam('forum_id');
		if ($forum_id) {
			$forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id);
			if ($forum) {
				$forum -> parent_forum_id = null;
				$forum -> save();
			}
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Forum removed.')), 'layout' => 'default-simple', 'parentRefresh' => true));
	}

	/**
	 * MinhNC Start
	 *
	 * Manage icons
	 *
	 * @author: MinhNC
	 */
	public function iconsAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynforum_admin_main', array(), 'ynforum_admin_main_manage_icons');
		$this -> view -> form = $form = new Ynforum_Form_Admin_Icon_Search();
		$values = array();
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		if ($this -> getRequest() -> isPost()) {
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value) {
				if ($key == 'delete_' . $value) {
					$icon = Engine_Api::_() -> getItem('ynforum_icon', $value);
					$icon -> delete();
				}
			}
		}
		if (!isset($values['order'])) {
			$values['order'] = "creation_date";
		}

		if (!isset($values['direction'])) {
			$values['direction'] = "DESC";
		}
		$page = $this -> _getParam('page', 1);
		$table = Engine_Api::_() -> getDbTable("icons", "ynforum");
		$this -> view -> paginator = $table -> getPaginator($values);
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> formValues = $values;
	}

	public function addIconAction() {
		$this -> view -> form = $form = new Ynforum_Form_Admin_Icon_Create();
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$table = Engine_Api::_() -> getDbTable('icons', 'ynforum');
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$values = $form -> getValues();
				$icon = $table -> createRow();
				$icon -> setFromArray($values);
				$icon -> save();
				if (!empty($values['icon']))
					$icon -> setPhoto($form -> icon);
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Add icon successfully.')));
			} catch (Exception $e) {
				$db -> rollBack(); 
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	public function editIconAction() {
		$icon = Engine_Api::_() -> getItem('ynforum_icon', $this -> _getParam('id'));
		if (!$icon) {
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> form = $form = new Ynforum_Form_Admin_Icon_Edit();
		$form -> populate($icon -> toArray());
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$table = Engine_Api::_() -> getDbTable('icons', 'ynforum');
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$values = $form -> getValues();
				$icon -> title = $values['title'];
				$icon -> save();
				if (!empty($values['icon']))
					$icon -> setPhoto($form -> icon);
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Edit icon successfully.')));
			} catch (Exception $e) {
				$db -> rollBack();
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	public function deleteIconAction() {
		$icon = Engine_Api::_() -> getItem('ynforum_icon', $this -> _getParam('id'));
		if (!$icon) {
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> form = $form = new Ynforum_Form_Admin_Icon_Delete();
		$form -> populate($icon -> toArray());
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$table = Engine_Api::_() -> getDbTable('icons', 'ynforum');
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$icon -> delete();
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Delete icon successfully.')));
			} catch (Exception $e) {
				$db -> rollBack();
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	/** Manage reports
	 *
	 * @author: MinhNC
	 */
	public function reportsAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynforum_admin_main', array(), 'ynforum_admin_main_manage_reports');
		$this -> view -> form = $form = new Ynforum_Form_Admin_Report_Filter();
		$values = array();
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		if ($this -> getRequest() -> isPost()) {
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value) {
				if ($key == 'delete_' . $value) {
					$report = Engine_Api::_() -> getItem('core_report', $value);
					if (isset($values['delete']) && $report) {
						$post = Engine_Api::_() -> getItem('ynforum_post', $report -> subject_id);
						$post -> delete();
					}
					$report -> delete();
				}
			}
		}
		if (!isset($values['order']) || $values['order'] == null) {
			$values['order'] = "creation_date";
		}
		
		
		if (!isset($values['direction']) || $values['direction'] == null) {
			$values['direction'] = "DESC";
		}
		$page = $this -> _getParam('page', 1);
		$table = Engine_Api::_() -> getItemTable('core_report');
		$select = $table -> select();
		$select -> where("subject_type = 'forum_post'");
		//Search Report
		// Desciption
		if (!empty($values['description'])) {
			$select -> where("description LIKE ?", '%' . $values['description'] . '%');
		}
		// Category
		if (!empty($values['category'])) {
			$select -> where("category = ?", $values['category']);
		}
		// From date
		if (!empty($values['start_date']) && empty($values['end_date'])) {
			$fromdate = Engine_Api::_() -> ynforum() -> getFromDaySearch($values['start_date']);
			if (!$fromdate) {
				$select -> where("false");
				return $select;
			}
			$select -> where("(creation_date >= ?)", $fromdate);
		}

		// To date
		if (!empty($values['end_date']) && empty($values['start_date'])) {
			$todate = Engine_Api::_() -> ynforum() -> getToDaySearch($values['end_date']);
			if (!$todate) {
				$select -> where("false");
				return $select;
			}
			$select -> where("(creation_date <= ?)", $todate);
		}

		if (!empty($values['start_date']) && !empty($values['end_date'])) {
			$fromdate = Engine_Api::_() -> ynforum() -> getFromDaySearch($values['start_date']);
			$todate = Engine_Api::_() -> ynforum() -> getToDaySearch($values['end_date']);
			$select -> where("creation_date between '$fromdate' and '$todate'");
		}
		
		$select -> order($values['order'] . ' ' . $values['direction']);
		
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> formValues = $values;
	}

	public function dismissReportAction() {
		$report = Engine_Api::_() -> getItem('core_report', $this -> _getParam('id'));
		if (!$report) {
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> form = $form = new Ynforum_Form_Admin_Report_Delete();
		$form -> populate($report -> toArray());
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$table = Engine_Api::_() -> getDbTable('reports', 'core');
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$report -> delete();
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Delete report successfully.')));
			} catch (Exception $e) {
				$db -> rollBack();
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	public function deletePostAction() {
		$report = Engine_Api::_() -> getItem('core_report', $this -> _getParam('id'));
		if (!$report) {
			return $this -> _helper -> requireSubject -> forward();
		}
		$post = Engine_Api::_() -> getItem('ynforum_post', $report -> subject_id);
		if (!$post) {
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> form = $form = new Ynforum_Form_Admin_Report_DeletePost();
		$form -> populate($post -> toArray());
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$table = Engine_Api::_() -> getDbTable('posts', 'ynforum');
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$post -> delete();
				$report -> delete();
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Delete post successfully.')));
			} catch (Exception $e) {
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	/**
	 * MinhNC - END
	 *
	 */

	/*
	 LuanND
	 */
	public function postsAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynforum_admin_main', array(), 'ynforum_admin_main_manage_posts');
		$this -> view -> form = $form = new Ynforum_Form_Admin_Post_Manage();

		$values = array();
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		if ($this -> getRequest() -> isPost()) {
			$values = $this -> getRequest() -> getPost();			
			foreach ($values as $key => $value) {
				if ($key == 'delete_' . $value) {
					$post = Engine_Api::_() -> getItem('ynforum_post', $value);
					$post -> delete();
				}
			}
		}

		$page = $this -> _getParam('page', 1);
		if (!isset($values['approved']))
			$values['approved'] = 1;

		
		if (!isset($values['order'])) {
			$values['order'] = "creation_date";
		}

		if (!isset($values['direction'])) {
			$values['direction'] = "asc";
		}
		$this -> view -> formValues = $values;
		$this -> view -> paginator = Engine_Api::_() -> getItemTable('ynforum_post') -> getPostPaginator($values);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);

	}

	/*
	 LuanND - END
	 */

}
