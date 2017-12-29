<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_PostController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (0 !== ($post_id = (int)$this -> _getParam('post_id')) && null !== ($post = Engine_Api::_() -> getItem('ynforum_post', $post_id)) && $post instanceof Ynforum_Model_Post)
		{
			Engine_Api::_() -> core() -> setSubject($post);
		}

	}

	public function deleteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum_post') -> isValid())
		{
			return;
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> post = $post = Engine_Api::_() -> core() -> getSubject('forum_post');
		$this -> view -> topic = $topic = $post -> getParent();
		$this -> view -> forum = $forum = $topic -> getParent();

		if (!$this -> _helper -> requireAuth() -> setAuthParams($post, null, 'delete') -> checkRequire() && !$forum -> checkPermission($viewer, 'forum', 'post.delete'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}

		$this -> view -> form = $form = new Ynforum_Form_Post_Delete();
		$returnUrl = $this -> _getParam('return-url');
		if ($returnUrl)
		{
			$form -> setAction($form -> getAction() . '?return-url=' . $returnUrl);
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynforum_post');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		$topic_id = $post -> topic_id;

		try
		{
			$post -> delete();

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		$this -> view -> topic = $topic = Engine_Api::_() -> getItem('ynforum_topic', $topic_id);
		$href = (null === $topic ? $forum -> getHref() : $topic -> getHref());
		
		//check if admin call
		if((int)$this -> _getParam('admin'))
		{			
			return $this->_forward('success', 'utility', 'core', array(
		        'smoothboxClose' => 10,
		        'parentRefresh'=> 10,
		        'messages' => array('Aproved success!')
		    ));				
		}
		return $this -> _forward('success', 'utility', 'core', array(
			'closeSmoothbox' => true,
			'parentRedirect' => ($returnUrl != null) ? $returnUrl : $href,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Post deleted.')),
			'format' => 'smoothbox'
		));
	}

	public function denyAction()
	{
		
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum_post') -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$post = Engine_Api::_() -> core() -> getSubject('forum_post');
		$topic = $post -> getParent();
		$forum = $topic -> getParent();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($post, null, 'delete') -> checkRequire() && $forum -> checkPermission($viewer, 'forum', 'yntopic.delete'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		// Process
		$table = Engine_Api::_() -> getItemTable('ynforum_post');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		$topic_id = $post -> topic_id;

		try
		{			
			$post -> approved = 2;
			$post ->save();

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		
		//check if admin call
		if((int)$this -> _getParam('admin'))
		{			
			return $this->_forward('success', 'utility', 'core', array(
		        'smoothboxClose' => 10,
		        'parentRefresh'=> 10,
		        'messages' => array('Denied success!')
		    ));				
		}		
	}

	public function editAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum_post') -> isValid())
		{
			return;
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> post = $post = Engine_Api::_() -> core() -> getSubject('forum_post');
		$this -> view -> topic = $topic = $post -> getParent();
		$this -> view -> forum = $forum = $topic -> getParent();
		if (!$forum -> checkPermission($viewer, 'forum', 'post.edit')) {
			return $this -> _helper -> requireAuth() -> forward();
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
		$this -> view -> navigationForums = $forum -> getForumNavigations();
		
		if (!$this -> _helper -> requireAuth() -> setAuthParams($post, null, 'edit') -> checkRequire() && $forum -> checkPermission($viewer, 'forum', 'yntopic.edit'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$this -> view -> form = $form = new Ynforum_Form_Post_Edit( array(
			'post' => $post,
			'forum' => $forum
		));		
		$form->populate($post->toArray());

		$allowHtml = (bool)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('forum_html', 0);
		$allowBbcode = (bool)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('forum_bbcode', 0);

		if ($allowHtml || $allowBbcode)
		{
			$body = $post -> body;
		}
		else
		{
			$body = htmlspecialchars_decode($post -> body, ENT_COMPAT);
		}

		$form -> getElement('body') -> setValue($body);

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynforum_post');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			$post -> body = $values['body'];
			if($values['title'] == '')
			{
				$post -> title = 'Untitled title';
			}
			else 
			{
				$post -> title = $values['title'];
			}
			if($values['icon_id'])
				$post -> icon_id = $values['icon_id'];
			$post -> edit_id = $viewer -> getIdentity();
			
			//DELETE photo here.
			if (!empty($values['photo_delete']) && $values['photo_delete'])
			{
				$post -> deletePhoto();
			}
			if (!empty($values['photo']))
			{
				$post -> setPhoto($form -> photo);
			}
			$post -> save();
			
			//DELETE attachment here.
			if (!empty($values['check_delete']) && $values['check_delete'])
			{
				$post -> deleteAttach();
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
			
			$db -> commit();

			$returnUrl = $this -> _getParam('return-url');

			if ($returnUrl != null)
			{
				$this -> getResponse() -> setRedirect($returnUrl);
				return;
			}
			
			$submit = $this -> getRequest() -> getPost();
			if (isset($submit['managePhoto'])) {
				return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-photos', 'post_id' => $post -> getIdentity()), 'ynforum_post', true);
			}
			
			return $this -> _helper -> redirector -> gotoRoute(array(
				'post_id' => $post -> getIdentity(),
				'topic_id' => $post -> getParent() -> getIdentity()
			), 'ynforum_topic', true);
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function thankAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum_post') -> isValid())
		{
			return;
		}
		$post = Engine_Api::_() -> core() -> getSubject('forum_post');
		$topic = $post -> getParent();
		$forum = $topic -> getParent();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		$thankTable = Engine_Api::_() -> getItemTable('ynforum_thank');
		$db = $thankTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			
			$thanked = $post -> thank($viewer -> getIdentity());

			// send notification for the owner post
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($post -> getOwner(), $viewer, $post, 'ynforum_topic_thank', array('message' => $this -> view -> BBCode(substr(strip_tags($post -> body), 0, 128)),
			// // @todo make sure this works
			));
			
			// Activity
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($viewer, $topic, 'ynforum_post_thank');
			if ($action)
			{
				$action -> attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
			}

			$db -> commit();

			if ($thanked)
			{
				$canEdit = $forum -> checkPermission($viewer, 'forum', 'yntopic.edit');
				$canDelete = $forum -> checkPermission($viewer, 'forum', 'yntopic.delete');
				$canApprove = $forum -> checkPermission($viewer, 'forum', 'yntopic.approve');
				$canEdit_Post = $forum -> checkPermission($viewer, 'forum', 'post.edit');
				$canDelete_Post = $forum -> checkPermission($viewer, 'forum', 'post.delete');
				$canPost = $forum -> checkPermission($viewer, 'forum', 'post.create');

				$thankUserIds = $post -> getThankedUserIds();
				$userTable = Engine_Api::_() -> getItemTable('user');

				$thankUsers = array();
				foreach ($userTable->find($thankUserIds) as $thankUser)
				{
					$thankUsers[$thankUser -> getIdentity()] = $thankUser;
				}

				$settings = Engine_Api::_() -> getApi('settings', 'core');
				echo $this -> view -> partial('_post.tpl', array(
					'post' => $post,
					'canApprove' => $canApprove,
					'canEdit' => $canEdit,
					'canEdit_Post' => $canEdit_Post,
					'canDelete' => $canDelete,
					'canPost' => $canPost,
					'canDelete_Post' => $canDelete_Post,
					'topic' => $topic,
					'forum' => $forum,
					'viewer' => $viewer,
					'thankUsers' => $thankUsers,
					'decode_bbcode' => $settings -> getSetting('forum_bbcode', 0),
				));
			}
			else
			{
				echo -1;
			}
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			echo -2;
		}

		return;
	}

	public function addReputationAction()
	{
		$postRequest = $this -> getRequest() -> getPost();
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum_post') -> isValid())
		{
			return;
		}
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$post = Engine_Api::_() -> core() -> getSubject('forum_post');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($post -> isAddedReputationBy($viewer -> getIdentity()))
		{
			return;
		}
		$topic = $post -> getParent();
		$forum = $topic -> getParent();

		$reputationTable = Engine_Api::_() -> getItemTable('ynforum_reputation');
		$db = $reputationTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$reputation = $reputationTable -> createRow();
			
			if(!$postRequest['reputation'])
				$postRequest['reputation'] = -1;
			$reputation -> score = $postRequest['reputation'];
			$reputation -> post_id = $post -> getIdentity();
			$reputation -> user_id = $viewer -> getIdentity();
			$reputation -> creation_date = date('Y-m-d H:i:s');
			$reputation -> save();

			$signatureTable = Engine_Api::_() -> getItemTable('ynforum_signature');
			$signatureSelect = $signatureTable -> select() -> where('user_id = ?', $post -> user_id);
			$signature = $signatureTable -> fetchRow($signatureSelect);
			if ($signature == null)
			{
				$signature = $signatureTable -> createRow(array(
					'user_id' => $post -> user_id,
					'body' => '',
					'creation_date' => $reputation -> creation_date,
					'post_count' => 0,
					'thanked_count' => 0,
					'thanks_count' => 0,
					'reputation' => 0,
				));
			}
			else
			{
				$signature -> reputation = new Zend_Db_Expr('reputation + ' . $reputation -> score);
				if($reputation -> score > 0)
					$signature -> positive = new Zend_Db_Expr('positive + ' . $reputation -> score);
				else
					$signature -> neg_positive = new Zend_Db_Expr('neg_positive + ' . $reputation -> score);
				$signature -> modified_date = $reputation -> creation_date;
			}
			$signature -> save();

			// send notification for the owner post
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($post -> getOwner(), $viewer, $post, 'ynforum_topic_reputation', array('message' => $this -> view -> BBCode($post -> body),
			// @todo make sure this works
			));

			$db -> commit();

			$canEdit = $forum -> checkPermission($viewer, 'forum', 'yntopic.edit');
			$canDelete = $forum -> checkPermission($viewer, 'forum', 'yntopic.delete');
			$canApprove = $forum -> checkPermission($viewer, 'forum', 'yntopic.approve');
			$canEdit_Post = $forum -> checkPermission($viewer, 'forum', 'post.edit');
			$canDelete_Post = $forum -> checkPermission($viewer, 'forum', 'post.delete');
			$thankUserIds = $post -> getThankedUserIds();
			$userTable = Engine_Api::_() -> getItemTable('user');

			$thankUsers = array();
			foreach ($userTable->find($thankUserIds) as $thankUser)
			{
				$thankUsers[$thankUser -> getIdentity()] = $thankUser;
			}
			$settings = Engine_Api::_() -> getApi('settings', 'core');
			$this -> view -> decode_bbcode = $settings -> getSetting('forum_bbcode', 0);
			if (!$topic->closed && Engine_Api::_()->authorization()->isAllowed($forum, null, 'post.create')) 
			{
	            $canPost = true;
	        }
        
        	$this->view->canPost = $canPost;
			
			echo $this -> view -> partial('_post.tpl', array(
				'post' => $post,
				'canApprove' => $canApprove,
				'canEdit' => $canEdit,
				'canDelete' => $canDelete,
				'canEdit_Post' => $canEdit_Post,
				'canDelete_Post' => $canDelete_Post,
				'canPost' => $canPost,
				'topic' => $topic,
				'forum' => $forum,
				'viewer' => $viewer,
				'thankUsers' => $thankUsers,
				'decode_bbcode' => $settings -> getSetting('forum_bbcode', 0)
			));

		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		return;
	}
	public function approveAction()
	{
		
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('forum_post') -> isValid())
		{
			return;
		}
		$post = Engine_Api::_() -> core() -> getSubject('forum_post');
		$topic = $post -> getParent();
		$forum = $topic -> getParent();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$postOwner = $post -> getOwner();
		$topicOwner = $topic -> getOwner();

		if (!Engine_Api::_() -> authorization() -> isAllowed('forum', $viewer -> level_id, 'yntopic.approve'))
		{
			$listItem = Engine_Api::_() -> getItemTable('ynforum_list_item') -> getModeratorItem($forum -> getIdentity(), $viewer -> getIdentity());
			if ($listItem != null)
			{
				if (!$this -> _helper -> requireAuth() -> setAuthParams($forum, $listItem, 'yntopic.approve') -> checkRequire())
				{
					return $this -> _helper -> requireAuth() -> forward();
				}
			}
			else
			{
				return $this -> _helper -> requireAuth() -> forward();
			}
		}

		$post -> approved = 1;
		$post -> save();

		if (!$topic -> approved)
		{
			$topic -> approved = 1;
			$topic -> save();
		}

		// add activity when the post is approved
		$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
		if ($topic -> firstpost_id == $post -> getIdentity())
		{
			$action = $activityApi -> addActivity($topicOwner, $topic, 'ynforum_topic_create');
			if ($action)
			{
				$action -> attach($topic);
			}
		}
		else
		{
			$action = $activityApi -> addActivity($postOwner, $topic, 'ynforum_topic_reply');
			if ($action)
			{
				$action -> attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
		}

		// add notification
		$topicTable = Engine_Api::_() -> getDbtable('topics', 'ynforum');
		$forumWatchesTable = Engine_Api::_() -> getDbtable('forumWatches', 'ynforum');
		$topicWatchesTable = Engine_Api::_() -> getDbtable('topicWatches', 'ynforum');
		$forumWatchesTable = Engine_Api::_() -> getDbtable('forumWatches', 'ynforum');
		$postTable = Engine_Api::_() -> getDbtable('posts', 'ynforum');
		$userTable = Engine_Api::_() -> getItemTable('user');
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$notifyForumUserIds = $forumWatchesTable -> select() -> from($forumWatchesTable -> info('name'), 'user_id') -> where('forum_id = ?', $forum -> getIdentity()) -> where('watch = ?', 1) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
		$notifyTopicUserIds = $topicWatchesTable -> select() -> from($topicWatchesTable -> info('name'), 'user_id') -> where('resource_id = ?', $forum -> getIdentity()) -> where('topic_id = ?', $topic -> getIdentity()) -> where('watch = ?', 1) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
		$notifyUserIds = array_merge($notifyForumUserIds, $notifyTopicUserIds);

		// notify for user watching the topic's forum
		if ($topic -> firstpost_id == $post -> getIdentity())
		{
			foreach ($userTable->find($notifyForumUserIds) as $notifyUser)
			{
				// Don't notify self
				if ($notifyUser -> isSelf($viewer))
				{
					continue;
				}

				if (!$notifyUser -> isSelf($topicOwner))
				{
					$notifyApi -> addNotification($notifyUser, $topicOwner, $topic, 'ynforum_topic_create', array('message' => $this -> view -> BBCode($post -> body),
						// // @todo make sure this works
					));
				}
			}
		}

		// notify to all watching users except the moderators when the post is approved
		if (isset($listItem) && $listItem != null)
		{
			for ($i = 0; $i < count($notifyUserIds); $i++)
			{
				if ($notifyUserIds[$i] == $listItem -> child_id)
				{
					unset($notifyUserIds[$i]);
					break;
				}
			}
		}

		if ($topic -> firstpost_id != $post -> getIdentity())
		{
			foreach ($userTable->find($notifyUserIds) as $notifyUser)
			{
				// Don't notify self
				if ($notifyUser -> isSelf($viewer))
				{
					continue;
				}
				if ($notifyUser -> isSelf($topicOwner))
				{
					$type = 'ynforum_topic_response';
				}
				else
				{
					$type = 'ynforum_topic_reply';
				}

				if (!$notifyUser -> isSelf($postOwner))
				{
					$notifyApi -> addNotification($notifyUser, $postOwner, $post, $type, array('message' => $this -> view -> BBCode($post -> body),
						// // @todo make sure this works
					));
				}
			}
		}
		if (!$postOwner -> isSelf($viewer))
		{
			$notifyApi -> addNotification($postOwner, $viewer, $post, 'ynforum_owner_post_approved', array('message' => $this -> view -> BBCode($post -> body)));
		}

		if((int)$this -> _getParam('admin'))
		{			
			return $this->_forward('success', 'utility', 'core', array(
		        'smoothboxClose' => 10,
		        'parentRefresh'=> 10,
		        'messages' => array('Aproved success!')
		    ));				
		}
		
		$returnUrl = $this -> _getParam('return-url');

		if ($returnUrl != null)
		{
			$this -> getResponse() -> setRedirect($returnUrl);
			return;
		}		
		
		
		return $this -> _helper -> redirector -> gotoRoute(array(
			'post_id' => $post -> getIdentity(),
			'topic_id' => $topic -> getIdentity(),
		), 'ynforum_topic', true);
	}
	//LuanND Start //
	public function managePhotosAction() {

		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> post = $post = Engine_Api::_() -> core() -> getSubject('forum_post');

		if (!$post) {
			return $this -> _helper -> requireAuth -> forward();
		}

		//init
		$this -> view -> topic = $topic = $post -> getParent();
		$this -> view -> forum = $forum = $topic -> getParent();
		
		$this -> view -> post_id = $post -> getIdentity();
		$this -> view -> file_id = $post -> file_id;
		
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
	
		if ($post -> file_id > 0)
			if (!$post -> getPhoto($post -> file_id)) {
				$post -> addPhoto($post -> file_id);
			}
		$this -> view -> album = $album = $post -> getSingletonAlbum();
		$this -> view -> postPhotos = $postPhotos = $album -> getCollectiblesPaginator();
		$postPhotos -> setCurrentPageNumber($this -> _getParam('page'));
		$postPhotos -> setItemCountPerPage(100);
		
		$fileIds = '';
		foreach($postPhotos as $photo)
		{
			$fileIds .= ' '.$photo->file_id;
		}
		
		$this -> view -> form = $form = new Ynforum_Form_Photo_Manage(array('fileIds' => $fileIds));
		
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynforum_postphoto');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$values = $form -> getValues();
			
			$arrUploadFile = array();
			$arrUploadFile = explode(' ', trim($values['html5uploadfileids']));
			if ($arrUploadFile) {
				$values['uploadFile'] = $arrUploadFile;
			}
			
			$arrImportFile = array();
			$arrImportFile = explode(' ', trim($values['html5importfile']));
			if ($arrImportFile) {
				$values['importFile'] = $arrImportFile;
			}	
			
			$arrAttachedFile = array();
			$arrAttachedFile = explode(' ', trim($values['ynforumpostuploadfile']));
			if ($arrAttachedFile) {
				$values['attachedFile'] = $arrAttachedFile;
			}			
			
			//skip and finished
			if (isset($values['managePhoto'])) {
				//delete photo
				foreach ($values['uploadFile'] as $photo_id) {
					$photo = Engine_Api::_() -> getItem("ynforum_postphoto", $photo_id);

					if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
						continue;
					$photo -> delete();
					
					$post -> photo_id = NULL;
					$post -> save();
				}
				return $this -> _helper -> redirector -> gotoRoute(array('post_id' => $post -> getIdentity(), 'topic_id' => $post -> getParent() -> getIdentity()), 'ynforum_topic', true);
			}

			foreach($values['attachedFile'] as $file_id)
			{
				//import file by html5
				if(in_array($file_id, $values['uploadFile']))
				{
					$key = array_search($file_id, $values['uploadFile']);
					unset($values['uploadFile'][$key]);
					
					$photo = Engine_Api::_() -> getItem("ynforum_postphoto", $file_id);

					if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
						continue;
	
					$photo -> collection_id = $album -> postalbum_id;
					$photo -> postalbum_id = $album -> postalbum_id;
					$photo -> save();	
					
				}
				//import file by choose album photo
				elseif (in_array($file_id, $values['importFile'])) {
					$key = array_search($file_id, $values['importFile']);
					unset($values['importFile'][$key]);
					
					$file = Engine_Api::_() -> getItem('storage_file', $file_id);
				
					if (!($file instanceof Core_Model_Item_Abstract) || !$file -> getIdentity())
						continue;
										
					$album = $post -> getSingletonAlbum();
					
																
					// We can set them now since only one album is allowed
					$params = 
					array(	'collection_id' => $album -> getIdentity(), 
							'postalbum_id' => $album -> getIdentity(), 
							'post_id' => $post -> getIdentity(), 
							'user_id' => $viewer -> getIdentity(), 
							'parent_type' => 'album_photo', 
							'file_id' => $file->getIdentity(),
						 );
					
					$photo = Engine_Api::_()->getItemTable('ynforum_postphoto')->createRow();
					
					$photo->setFromArray($params);
					$photo->save();
					
				}
			}

			//delete photo upload by html5
			foreach($values['uploadFile'] as $file_id)
			{
				$photo = Engine_Api::_() -> getItem("ynforum_postphoto", $file_id);

				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				$photo -> delete();
			}
			
			$db -> commit();
		} catch ( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array('post_id' => $post -> getIdentity(), 'topic_id' => $post -> getParent() -> getIdentity()), 'ynforum_topic', true);

	}

	public function renderAlbumPhotosAction() {
		$album_id = (int)$this -> _getParam('album_id');

		if (Engine_Api::_() -> hasModuleBootstrap('advalbum')) {
			$album = Engine_Api::_() -> getItem('advalbum_album', $album_id);
			$photoTable = Engine_Api::_() -> getItemTable('advalbum_photo');
			$paginator = $album -> getCollectiblesPaginator();
		} else {
			$album = Engine_Api::_() -> getItem('album', $album_id);
			$photoTable = Engine_Api::_() -> getItemTable('album_photo');
			$paginator = $photoTable -> getPhotoPaginator(array('album' => $album, ));
		}
		$translate = Zend_Registry::get('Zend_Translate');
		$paginator -> setItemCountPerPage(100);
		foreach ($paginator as $photo) {
			
			$html .= "
			<li id='thumbs-photo-" . $photo -> file_id . "'> 
				<a class='thumbs_photo'> <span style='background-image: url(" . $photo -> getPhotoUrl('thumb.normal') . ")'> </span><input type='checkbox' class='checkbox' name='photo_id_" . $photo -> file_id . "' value='" . $photo -> file_id . "'></a>
				<p class='thumbs_info'>			
            		<span class='thumbs_title'><a href='javascript:void()' title='" . $photo -> getTitle() . "' class='thumbs_photo_link'>" . $photo -> getTitle() . "</a></span>
			    </p>
			</li>";
		}
		$html = "
		<ul class='thumbs thumbs_nocaptions'>" . $html . "</ul>		
        ";
		echo Zend_Json::encode(array('html' => $html));
		exit ;

	}

	public function uploadPhotoAction() {

		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}

		if (!$this -> getRequest() -> isPost()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}

		$post = Engine_Api::_() -> core() -> getSubject('forum_post');

		// @todo check auth

		if (empty($_FILES['files'])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $error)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}

		$db = Engine_Api::_() -> getDbtable('postphotos', 'Ynforum') -> getAdapter();
		$db -> beginTransaction();

		try {
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$album = $post -> getSingletonAlbum();

			$params = array(
			// We can set them now since only one album is allowed
			'post_id' => $post -> post_id, 'user_id' => $viewer -> getIdentity(), );
			$temp_file = array('type' => $_FILES['files']['type'][0], 'tmp_name' => $_FILES['files']['tmp_name'][0], 'name' => $_FILES['files']['name'][0]);
			
			$photo = Engine_Api::_() -> ynforum() -> createPhoto($params, $temp_file);
			$photo_id = $photo -> postphoto_id;
			$name = $photo -> getPhotoUrl('thumb.normal');
			if (!$post -> photo_id) {
				$post -> photo_id = $photo_id;
				$post -> save();
			}

			$db -> commit();

			$status = true;

			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $name, 'photo_id' => $photo_id)))));

		} catch( Exception $e ) {
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}

	}

	public function deletePhotoAction() {
		$photo = Engine_Api::_() -> getItem('ynforum_postphoto', $this -> getRequest() -> getParam('photo_id'));

		if (!$photo) {
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('postphotos', 'ynforum') -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> delete();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	}

	//LuanND End //

}
