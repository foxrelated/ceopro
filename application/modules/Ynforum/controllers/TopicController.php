<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_TopicController extends Core_Controller_Action_Standard {

    public function init() 
    {
        if (0 !== ($topic_id = (int) $this->_getParam('topic_id')) &&
                null !== ($topic = Engine_Api::_()->getItem('ynforum_topic', $topic_id)) &&
                $topic instanceof Ynforum_Model_Topic) {
            Engine_Api::_()->core()->setSubject($topic);
        }
    }

    public function deleteAction() 
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) 
        {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
		//check permission delete topic
        if(!$forum -> checkPermission($viewer, 'forum', 'yntopic.delete'))
		{
			return;
		}
        $this->view->form = $form = new Ynforum_Form_Topic_Delete();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $table = Engine_Api::_()->getItemTable('ynforum_topic');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic deleted.')),
            'layout' => 'default-simple',
            'parentRedirect' => $forum->getHref(),
        ));
    }

    public function editAction() {
        if (!$this->_helper->requireSubject('ynforum_topic')->isValid()) {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('ynforum_topic');
        $this->view->forum = $forum = $topic->getParent();
        
		if(!$forum -> checkPermission($viewer, 'forum', 'yntopic.edit'))
		{
			return;
		}

        $this->view->form = $form = new Ynforum_Form_Topic_Edit();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $table = Engine_Api::_()->getItemTable('ynforum_topic');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();

            $topic->setFromArray($values);
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function viewAction() 
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) 
        {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
        
		$category = $forum -> getParent();
		if(!Engine_Api::_()->authorization()->isAllowed($category, $viewer, 'forumcat.view'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
        if (!$this->_helper->requireAuth->setAuthParams($forum, null, 'view')->isValid()) 
        {
             return;
        }
		
		if (!$forum -> checkPermission($viewer, 'forum', 'view')) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		
        $this->view->canEdit = false;
        $this->view->canDelete = false;
        $this->view->canApprove = false;
        $this->view->canSticky = false;
        $this->view->canClose = false;
        $this->view->canMove = false;
        $this->view->canEdit_Post = false;
        $this->view->canDelete_Post = false;
        
        if ($viewer && $viewer->getIdentity()) 
        {
            $this->view->canEdit = $forum -> checkPermission($viewer, 'forum', 'yntopic.edit');
            $this->view->canDelete = $forum -> checkPermission($viewer, 'forum', 'yntopic.delete');
            $this->view->canSticky = $forum -> checkPermission($viewer, 'forum', 'yntopic.sticky');
            $this->view->canClose = $forum -> checkPermission($viewer, 'forum', 'yntopic.close');
            $this->view->canMove = $forum -> checkPermission($viewer, 'forum', 'yntopic.move');
            $this->view->canApprove = $forum -> checkPermission($viewer, 'forum', 'yntopic.approve');
			$this->view->canEdit_Post = $forum -> checkPermission($viewer, 'forum', 'post.edit');
			$this->view->canDelete_Post = $forum -> checkPermission($viewer, 'forum', 'post.delete');
        }
        if (!$this->view->canApprove) {
            if (!$topic->approved && $topic->user_id != $viewer->getIdentity()) {
                $this->_helper->requireSubject->forward();
            }
        }
        
        // Settings
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->post_id = $post_id = (int) $this->_getParam('post_id');
        $this->view->allowBbcode = $settings->getSetting('forum_bbcode', 0);
        $this->view->allowHtml = $settings->getSetting('forum_html', 0);
        $this->view->decode_bbcode = $settings->getSetting('forum_bbcode', 0);

        // Views
        if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
            $topic->view_count = new Zend_Db_Expr('view_count + 1');
            $topic->save();
        }

		//acction post 
		if ($this->getRequest()->isPost()) 
		{
			$values = $this->getRequest()->getPost();
			$ids = explode(',', $values['post_ids']);
			
			switch ($values['post_moderate']) 
			{
				case 'delete':
					 if($forum -> checkPermission($viewer, 'forum', 'yntopic.delete'))
					 {
						foreach($ids as $id)
						{
							if($id)
							{
								$post = Engine_Api::_() -> getItem('ynforum_post', $id);
								$post->delete();
							}
						}
					 }
					break;
				case 'approve':
					if($forum -> checkPermission($viewer, 'forum', 'yntopic.approve'))
					 {
						foreach($ids as $id)
						{
							if($id)
							{
								$post = Engine_Api::_() -> getItem('ynforum_post', $id);	
														
								$post->approved = 1;
								$post -> save(); 
							}
						}
					 }
					break;				
			}
		}

        // Check watching
        $isWatching = null;
        // Checking forum watching
        $isForumWatching = null;
        if ($viewer->getIdentity()) {
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynforum');
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $forum->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0);
            if (false === $isWatching) {
                $isWatching = null;
            } else {
                $isWatching = (bool) $isWatching;
            }
        }
        $this->view->isWatching = $isWatching;
        
        // Auth for topic
        $canPost = false;
        
        if (!$topic->closed && Engine_Api::_()->authorization()->isAllowed($forum, null, 'post.create')) {
            $canPost = true;
        }
        $this->view->canPost = $canPost;

        // Make form
        if ($canPost) {
            $this->view->form = $form = new Ynforum_Form_Post_Quick(array('forum' => $forum));
            $form->setAction($topic->getHref(array('action' => 'post-create')));
            $form->populate(array(
                'topic_id' => $topic->getIdentity(),
                'ref' => $topic->getHref(),
                'watch' => ( false === $isWatching ? '0' : '1' ),
            ));
        }

        $select = $topic->getChildrenSelect('ynforum_post', array('order' => 'post_id ASC'));
        if (!$this->view->canApprove) {
            $select->where('approved = 1 or user_id = ? ', (int)$viewer->getIdentity());
        }
        
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($settings->getSetting('forum_topic_pagelength'));

        // set up variables for pages
        $page_param = (int) $this->_getParam('page');
        $post = Engine_Api::_()->getItem('ynforum_post', $post_id);

        // if there is a post_id
        if ($post_id && $post && !$page_param) 
        {
            $icpp = $paginator->getItemCountPerPage();
            $post_page = ceil(($post->getPostIndex() + 1) / $icpp);

            $paginator->setCurrentPageNumber($post_page);
        } 
        else if ($page_param) 
        { // Use specified page
            $paginator->setCurrentPageNumber($page_param);
        }
        
        $posts = $paginator->getCurrentItems();
		
		// Keep track of topic user views to show them which ones have new posts
        if ($viewer->getIdentity()) 
        {
        	$last_post_id = $topic->lastpost_id;
			if($topic->lastpost_id)
			{
				$arr_posts = $posts -> toArray();
				$last_post = end($arr_posts);
				$last_post_id = $last_post['post_id'];
			}
            $topic->registerView($viewer, $last_post_id);
        }
        $userIds = array();
        foreach($posts as $post) {
            $thankedUserIds = $post->getThankedUserIds();
            $userIds = array_unique(array_merge($userIds, $thankedUserIds));
        }
        $userTable = Engine_Api::_()->getItemTable('user');
        $thankUsers = array();
        foreach ($userTable->find($userIds) as $thankUser) {
            $thankUsers[$thankUser->getIdentity()] = $thankUser;
        }
        $this->view->thankUsers = $thankUsers;
        
        $this->view->formRepuration = new Ynforum_Form_Post_AddReputation();
        
        $categoryTable = Engine_Api::_()->getItemTable('ynforum_category');
        $cats = $categoryTable->fetchAll($categoryTable->select()->order('order ASC'));
        $categories = array();
        foreach($cats as $cat) {
            $categories[$cat->getIdentity()] = $cat;
        }
        $curCat = $categories[$forum->category_id];
        $linkedCategories = array();
        do {
            $linkedCategories[] = $curCat;
            if (!$curCat->parent_category_id) {
                break;
            }
            $curCat = $categories[$curCat->parent_category_id];            
        } while (true);
        
        $this->view->linkedCategories = $linkedCategories;
        $this->view->navigationForums = $forum->getForumNavigations();
		
		//check rate
		$this->view->avgrating = Engine_Api::_()->getApi('core', 'ynforum')->getAvgTopicRating($topic->getIdentity());
		$this->view->totalRates = Engine_Api::_()->getApi('core', 'ynforum')->getTotalRatingTopic($topic->getIdentity());
		if($viewer->getIdentity() > 0)
			$this->view->can_rate = Engine_Api::_()->getApi('core', 'ynforum')->checkTopicRating($topic->getIdentity(),$viewer->getIdentity());
		else
			$this->view->can_rate = false;
		
		//check detect link
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->view->detect_link = $settings->getSetting('forum_detect_link',1);
		// Render
		$this -> _helper -> content -> setEnabled();
    }

    public function stickyAction() {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
		if(!$forum -> checkPermission($viewer, 'forum', 'yntopic.sticky'))
		{
			return;
		}      

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic->sticky = ( null === $this->_getParam('sticky') ? !$topic->sticky : (bool) $this->_getParam('sticky') );
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_redirectCustom($topic);
    }

    public function closeAction() {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
        if(!$forum -> checkPermission($viewer, 'forum', 'yntopic.close'))
		{
			return;
		}
        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $topic->closed = ( null === $this->_getParam('closed') ? !$topic->closed : (bool) $this->_getParam('closed') );
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirectCustom($topic);
    }

    public function renameAction() {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
        if(!$forum -> checkPermission($viewer, 'forum', 'yntopic.edit'))
		{
			return;
		}
        $this->view->form = $form = new Ynforum_Form_Topic_Rename();

        if (!$this->getRequest()->isPost()) {
            $form->title->setValue(htmlspecialchars_decode(($topic->title)));
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $title = htmlspecialchars($form->getValue('title'));
            $topic->title = $title;
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic renamed.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

    public function moveAction() {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
        if(!$forum -> checkPermission($viewer, 'forum', 'yntopic.move'))
		{
			return;
		}

        $this->view->form = $form = new Ynforum_Form_Topic_Move();
        /**
         * ->getItemTable('ynforum_category');
         */
        // Populate with options
        $multiOptions = array();
        foreach (Engine_Api::_()->getItemTable('ynforum_forum')->fetchAll(null, 'title') as $forum) {
            $multiOptions[$forum->getIdentity()] = $this->view->translate($forum->getTitle());
        }
        $form->getElement('forum_id')->setMultiOptions($multiOptions);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            // Update topic
            $topic->forum_id = $values['forum_id'];
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic moved.')),
            'layout' => 'default-simple',
            //'parentRefresh' => true,
            'parentRedirect' => $topic->getHref(),
        ));
    }
    
    public function postCreateAction() 
    {              
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
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
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'post.create')->isValid()) {
            return;
        }
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$forum -> checkPermission($viewer, 'forum', 'post.create')) 
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
        if ($topic->closed) {
            return;
        }

        $this->view->form = $form = new Ynforum_Form_Post_Create(array('forum' => $forum));

        // Remove the file element if there is no file being posted
        if ($this->getRequest()->isPost() && empty($_FILES['photo'])) {
            $form->removeElement('photo');
        }

        $settingApi = Engine_Api::_()->getApi('settings', 'core');
        $allowHtml = (bool) $settingApi->getSetting('forum_html', 0);
        $allowBbcode = (bool) $settingApi->getSetting('forum_bbcode', 0);

        $quote_id = $this->getRequest()->getParam('quote_id');
        if (!empty($quote_id)) {
            $quote = Engine_Api::_()->getItem('ynforum_post', $quote_id);
            if ($quote->user_id == 0) {
                $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
            } else {
                $quoteOwner = $quote->getOwner();
                $owner_name = '<a href="' . $quoteOwner->getHref() . '">' . $quoteOwner->getTitle() . '</a>';
            }
            if ($allowHtml || !$allowBbcode) {
                $form->body->setValue("<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />");
            } else {
                $form->body->setValue("[blockquote][b]" . strip_tags($this->view->translate('%1$s said:', $owner_name)) . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n");
            }
        } 

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();
		
        if (!($allowHtml || $allowBbcode)) {
            $values['body'] = htmlspecialchars_decode($values['body'], ENT_COMPAT);
        } 
        
        $values['user_id'] = $viewer->getIdentity();
        $values['topic_id'] = $topic->getIdentity();
        $values['forum_id'] = $forum->getIdentity();
		
		if($values['title'] == '')
		{
			$values['title'] = 'Untitled title';
		}

        $topicTable = Engine_Api::_()->getDbtable('topics', 'ynforum');
        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynforum');
        $forumWatchesTable = Engine_Api::_()->getDbtable('forumWatches', 'ynforum');
        $postTable = Engine_Api::_()->getDbtable('posts', 'ynforum');
        $userTable = Engine_Api::_()->getItemTable('user');
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

        $topicOwner = $topic->getOwner();
        $isOwnTopic = $viewer->isSelf($topicOwner);

        $watch = (bool) $values['watch'];
        $isWatching = $topicWatchesTable
                ->select()
                ->from($topicWatchesTable->info('name'), 'watch')
                ->where('resource_id = ?', $forum->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity())
                ->limit(1)
                ->query()
                ->fetchColumn(0);

        $db = $postTable->getAdapter();
        $db->beginTransaction();

        try {
            $post = $postTable->createRow();
            $post->setFromArray($values);
            $post->save();

            if (!empty($values['photo']))
			{
				$post -> setPhoto($form -> photo);
			}
			if(!empty($values['attach']))
	        {
				//determine filename and extension
				$info = pathinfo($form->attach->getFileName(null,false));
				$filename = $info['filename'];
				$ext = $info['extension']?".".$info['extension']:"";
				//filter for renaming.. prepend with current time
				$form->attach->addFilter(new Zend_Filter_File_Rename(array(
	                        "target"=>time().$filename.$ext,
	                        "overwrite"=>true)));
	            $name = $filename.$ext;
	            $title = $filename.$ext;
	            $post->saveAttach($name,$title);
	        }
			
            // Watch
            if (false === $isWatching) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $forum->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $watch,
                ));
            } else if ($watch != $isWatching) {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                        ), array(
                    'resource_id = ?' => $forum->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }
            
            if ($post->approved) {
                // Activity
                /**
				 * WAWRNING: DO NOT REMOVE TRY/CATCH
				 * fixed issue: conflict with semod module.
				 */
				try{
						// Activity
	                if ($post->getIdentity() == $topic->firstpost_id) {
	                    $action = $activityApi->addActivity($viewer, $topic, 'ynforum_topic_create');
	                    if ($action) {
	                        $action->attach($topic);
	                    }
	                } else {                    
	                    $postOwner = $post->getOwner();
	                    $action = $activityApi->addActivity($postOwner, $topic, 'ynforum_topic_reply');
	                    if ($action) {
	                        $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
	                    }
	                }	
				}catch(Exception $ex){
					// silent
						
				}

                // Notifications
                $notifyTopicUserIds = $topicWatchesTable->select()
                        ->from($topicWatchesTable->info('name'), 'user_id')
                        ->where('resource_id = ?', $forum->getIdentity())
                        ->where('topic_id = ?', $topic->getIdentity())
                        ->where('watch = ?', 1)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
                $notifyForumUserIds = $forumWatchesTable->select()
                        ->from($forumWatchesTable->info('name'), 'user_id')
                        ->where('forum_id = ?', $forum->getIdentity())
                        ->where('watch = ?', 1)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
                $notifyUserIds = array_merge($notifyTopicUserIds, $notifyForumUserIds);
                foreach ($userTable->find($notifyUserIds) as $notifyUser) {
                    // Don't notify self
                    if ($notifyUser->isSelf($viewer)) {
                        continue;
                    }
                    if ($notifyUser->isSelf($topicOwner)) {
                        $type = 'ynforum_topic_response';
                    } else {
                        if (in_array($viewer->getIdentity(), $notifyForumUserIds) || in_array($viewer->getIdentity(), $notifyTopicUserIds)) {
                            $type = 'ynforum_topic_reply_forum_watch';
                        } else {
                            $type = 'ynforum_topic_reply';
                        }
                    }

                    if (!$notifyUser->isSelf($viewer)) {
                        $notifyApi->addNotification($notifyUser, $viewer, $post, $type, array(
                            'message' => $this->view->BBCode(substr(strip_tags($post -> body), 0, 128)), // @todo make sure this works
                        ));
                    }
                }
            } else {
                $modList = $forum->getModeratorList();
                $type = 'ynforum_post_wait_approval';
                foreach($modList->getAllChildren() as $notifyUser) {
                    if ($notifyUser->isSelf($viewer)) {
                        $notifyApi->addNotification($notifyUser, $viewer, $post, $type, array(
                            'message' => $this->view->BBCode(substr(strip_tags($post -> body), 0, 128)),
                        ));
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) 
        {
            $db->rollBack();
			throw $e;	
        }
		$submit = $this -> getRequest() -> getPost();
		if (isset($submit['managePhoto'])) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-photos', 'post_id' => $post -> getIdentity(), 'forum_id' => $forum->getIdentity()), 'ynforum_post', true);
		}
		
        $this->_redirectCustom(array(
            'route' => 'ynforum_topic', 
            'topic_id' => $topic->getIdentity(),
            'post_id' => $post->getIdentity()
        ));
    }

    public function watchAction() {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $this->view->forum = $forum = $topic->getParent();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, $viewer, 'view')->isValid()) {
            return;
        }

        $watch = $this->_getParam('watch', true);

        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynforum');
        $db = $topicWatchesTable->getAdapter();
        $db->beginTransaction();

        try {
            $isWatching = $topicWatchesTable
                    ->select()
                    ->from($topicWatchesTable->info('name'), 'watch')
                    ->where('resource_id = ?', $forum->getIdentity())
                    ->where('topic_id = ?', $topic->getIdentity())
                    ->where('user_id = ?', $viewer->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0);

            if (false === $isWatching) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $forum->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool) $watch,
                ));
            } else if ($watch != $isWatching) {
                $topicWatchesTable->update(array(
                    'watch' => (bool) $watch,
                        ), array(
                    'resource_id = ?' => $forum->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
        if($this->_getParam('refeshed'))
        {
        	return $this->_forward('success', 'utility', 'core', array(
        			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Saved')),
        			'layout' => 'default-simple',
        			'parentRefresh' => true,
        	));
        }

        $this->_redirectCustom($topic);
    }
/**
	 *  rate topic
	 * 
	 */
	 public function topicRateAction()
	{
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );

		if (! $this->_helper->requireUser ()->isValid ())
			return;

		$topic_id = ( int ) $this->_getParam ( 'topic_id' );
		$rates = ( int ) $this->_getParam ( 'rates' );
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$topic = Engine_Api::_ ()->getItem ( 'forum_topic', $topic_id );
		
		$can_rate = Engine_Api::_()->getApi('core', 'ynforum')->checkTopicRating($topic->getIdentity(),$viewer->getIdentity());

		if ($rates == 0 || $topic_id == 0 || !$can_rate) {
			return;
		}

		$rateTable = Engine_Api::_ ()->getDbtable ( 'topicRatings', 'ynforum' );
		$db = $rateTable->getAdapter ();
		$db->beginTransaction ();
		try {
			$rate = $rateTable->createRow ();
			$rate->poster_id = $viewer->getIdentity ();
			$rate->topic_id = $topic_id;
			$rate->rate_number = $rates;
			$rate->save ();
			// Commit
			$db->commit ();
		}

		catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		echo Engine_Api::_()->getApi('core', 'ynforum')->getAvgTopicRating($topic->getIdentity());
	}
}
