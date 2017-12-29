<?php
class Ynblog_IndexController extends Core_Controller_Action_Standard {
	/* ------ Init Contidition & Needed Resource Function ----- */
	public function init() {
		// Only show to user if view permisssion authorized
		if (!$this -> _helper -> requireAuth() -> setAuthParams('blog', null, 'view') -> isValid())
			return;
	}

	/* ------ Blog Home Page Function ----- */
	public function indexAction() {
		// Landing page mode
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	/* ----- General Blog Listing Function ----- */
	public function listingAction() {

		// Search Params

		// Do the show thingy
			// Get an array of friend ids
			// Get stuff
			// unset($values['show']);

		// Get blog paginator

		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	/* ------ A User Blogs List Function ----- */
	public function listAction() {
		// Preload info


		// Search Params


		// Get paginator
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	/* ----- Blog Creation Function ----- */
	public function createAction() {
		// Check authoraiztion permisstion
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams('blog', null, 'create') -> isValid())
			return;

		// Render
		$this -> _helper -> content -> setEnabled();

		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Checking maximum blog allowed
		$this -> view -> maximum_blogs = $maximum_blogs = Engine_Api::_() -> getItemTable('blog') -> checkMaxBlogs();
		$blog_number = Engine_Api::_() -> getItemTable('blog') -> getCountBlog($viewer);
		if ($maximum_blogs == 0 || $blog_number < $maximum_blogs) {
			$this -> view -> maximum_reach = false;
		} else {
			$this -> view -> maximum_reach = true;
		}

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynblog_main');

		// Prepare form
		$this -> view -> form = $form = new Ynblog_Form_Create();

		// Post request checking
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$table = Engine_Api::_() -> getDbTable('blogs', 'ynblog');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			// Create blog
			$values = array_merge($form -> getValues(), array('owner_type' => $viewer -> getType(), 'owner_id' => $viewer -> getIdentity()));

			// Moderation mode
			$blog_moderation = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynblog.moderation', 0);
			if ($blog_moderation) 
			{
				$values['is_approved'] = 0;
			} 
			else 
			{
				$values['is_approved'] = 1;
			}
			$blog = $table -> createRow();
			$blog -> setFromArray($values);
			$blog -> save();

			// Authorization set up
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

			if (empty($values['auth_view'])) {
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role) {
				$auth -> setAllowed($blog, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($blog, $role, 'comment', ($i <= $commentMax));
			}

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$blog -> tags() -> addTagMaps($viewer, $tags);
			
			// Set photo
	      	if( !empty($values['photo']) ) {
		        $blog->setPhoto($form->photo);
	      	}

			// Add activity only if blog is published
			if ($values['draft'] == 0 && $values['is_approved'] == 1) 
			{
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $blog, 'ynblog_new');

				// Make sure action exists before attaching the blog to the
				// activity
				if ($action) {
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $blog);
				}

				// Send notifications for subscribers
				Engine_Api::_() -> getDbtable('subscriptions', 'ynblog') -> sendNotifications($blog);

				$blog -> add_activity = 1;
				$blog -> save();
			}
			// Send notify admin
			if($blog_moderation && $values['draft'] == 0)
			{
				$users_table = Engine_Api::_()->getDbtable('users', 'user');
			  	$users_select = $users_table->select()
		  	    	->where('level_id = ?', 1)
			    	->where('enabled >= ?', 1)
					->limit(1);
			  	$super_admin = $users_table->fetchRow($users_select);
				if(!$super_admin -> isSelf($viewer))
				{
					$mailAdminType = 'notify_admin_blog_moderation';
					$mailAdminParams = array(
						'host' => $_SERVER['HTTP_HOST'],
						'date' => date("F j, Y, g:i a"),
						'recipient_title' => $super_admin->displayname,
						'sender_title' => $viewer->displayname,
						'object_title' => $blog -> getTitle(),
						'object_link' => $blog->getHref(),
					);
					Engine_Api::_()->getApi('mail', 'core')->sendSystem(
				         $super_admin,
				         $mailAdminType,
				         $mailAdminParams
			      	);
				}
			}
			
			// Commit
			$db -> commit();
		} catch ( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'));
	}

	/* ----- User Blogs Manage Page Function ----- */
	public function manageAction() {
		// Check authoraiztion permisstion
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// Render
		$this -> _helper -> content -> setEnabled();

		// Get quick navigation
		$this -> view -> quickNavigation = $quickNavigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynblog_quick');

		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $form = new Ynblog_Form_Search();
		$this -> view -> canCreate = $this -> _helper -> requireAuth() -> setAuthParams('blog', null, 'create') -> checkRequire();
		$form -> removeElement('show');

		// Process form
		if( $form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
		} else {
			$values = array();
		}
		$this -> view -> formValues = array_filter($values);
		$values['user_id'] = $viewer -> getIdentity();
		$mode = $values['mode'];

		if (isset($mode)) {
			if ($mode == '0') {
				$values['draft'] = 1;
			} else if ($mode == '1') {
				$values['draft'] = 0;
				$values['is_approved'] = 0;
			} else if ($mode == '2') {
				$values['draft'] = 0;
				$values['is_approved'] = 1;
			}
		}
		// Get blog paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> ynblog() -> getBlogsPaginator($values);
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynblog.page', 10);
		$paginator -> setItemCountPerPage($items_per_page);
		$this -> view -> paginator = $paginator -> setCurrentPageNumber($values['page']);

		if(Zend_Session::namespaceIsset('ynblog_import_result')){
			$messageSession = new Zend_Session_Namespace('ynblog_import_result');
			$aDuplicateBlogs = $messageSession->aDuplicateBlogs;
			if(is_array($aDuplicateBlogs) && count($aDuplicateBlogs))
				$this->view->bShowMessage = true;
		}
	}

	/* ----- Blog Edit Function ----- */
	public function editAction() {
		// User checking
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Get chosen blog to edit
		$blog = Engine_Api::_() -> getItem('blog', $this -> _getParam('blog_id'));
		if (!Engine_Api::_() -> core() -> hasSubject('blog')) {
			Engine_Api::_() -> core() -> setSubject($blog);
		}
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams($blog, $viewer, 'edit') -> isValid())
			return;

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynblog_main');

		// Prepare form
		$this -> view -> form = $form = new Ynblog_Form_Edit();

		// Populate form
		$form -> populate($blog -> toArray());

		$tagStr = '';
		foreach ($blog->tags ()->getTagMaps () as $tagMap) {
			$tag = $tagMap -> getTag();
			if (!isset($tag -> text))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag -> text;
		}
		$form -> populate(array('tags' => $tagStr));
		$this -> view -> tagNamePrepared = $tagStr;

		$auth = Engine_Api::_() -> authorization() -> context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

		foreach ($roles as $role) {
			if ($form -> auth_view) {
				if ($auth -> isAllowed($blog, $role, 'view')) {
					$form -> auth_view -> setValue($role);
				}
			}

			if ($form -> auth_comment) {
				if ($auth -> isAllowed($blog, $role, 'comment')) {
					$form -> auth_comment -> setValue($role);
				}
			}
		}

		// hide status change if it has been already published
		if ($blog -> draft == "0") {
			$form -> removeElement('draft');
		}

		// Check post/form
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try {
			$values = $form -> getValues();

			$blog -> setFromArray($values);
			$blog -> modified_date = date('Y-m-d H:i:s');
			$blog -> save();

			// Authorization
			if (empty($values['auth_view'])) {
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role) {
				$auth -> setAllowed($blog, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($blog, $role, 'comment', ($i <= $commentMax));
			}

			// Handle tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$blog -> tags() -> setTagMaps($viewer, $tags);
			
			// Set photo
	      	if( !empty($values['photo']) ) {
		        $blog->setPhoto($form->photo);
	      	}

			// Insert new activity if blog is just getting published and
			// approved
			if (!$blog -> add_activity && !$blog -> draft && $blog -> is_approved) {
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $blog, 'ynblog_new');
				// make sure action exists before attaching the blog to the
				// activity
				if ($action) {
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $blog);
				}

				$blog -> add_activity = 1;
				$blog -> save();
			}
			
			// Send notify admin
			if(isset($values['draft']) &&  $values['draft'] == 0 && !$blog -> is_approved)
			{
				$users_table = Engine_Api::_()->getDbtable('users', 'user');
			  	$users_select = $users_table->select()
		  	    	->where('level_id = ?', 1)
			    	->where('enabled >= ?', 1)
					->limit(1);
			  	$super_admin = $users_table->fetchRow($users_select);
				if(!$super_admin -> isSelf($viewer))
				{
					$mailAdminType = 'notify_admin_blog_moderation';
					$mailAdminParams = array(
						'host' => $_SERVER['HTTP_HOST'],
						'date' => date("F j, Y, g:i a"),
						'recipient_title' => $super_admin->displayname,
						'sender_title' => $viewer->displayname,
						'object_title' => $blog -> getTitle(),
						'object_link' => $blog->getHref(),
					);
					Engine_Api::_()->getApi('mail', 'core')->sendSystem(
				         $super_admin,
				         $mailAdminType,
				         $mailAdminParams
			      	);
				}
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject ( $blog ) as $action) {
				$actionTable -> resetActivityBindings($action);
			}

			// Send notifications for subscribers
			Engine_Api::_() -> getDbtable('subscriptions', 'ynblog') -> sendNotifications($blog);

			$db -> commit();
		} catch ( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'));
	}

	/* ----- Blog Delete Action ----- */
	public function deleteAction() {
		// User checking
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$blog = Engine_Api::_() -> getItem('blog', $this -> getRequest() -> getParam('blog_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($blog, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynblog_Form_Delete();

		if (!$blog) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Blog entry doesn't exist or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method.');
			return;
		}

		$db = $blog -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			$blog -> delete();

			$db -> commit();
		} catch ( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your blog entry has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'blog_general', true), 'messages' => Array($this -> view -> message)));
	}

	public function styleAction() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams('blog', null, 'style') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Require user
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$user = Engine_Api::_() -> user() -> getViewer();

		// Make form
		$this -> view -> form = $form = new Ynblog_Form_Style();

		// Get current row
		$table = Engine_Api::_() -> getDbtable('styles', 'core');
		$select = $table -> select() -> where('type = ?', 'user_blog') -> // @todo this is not a real type
		where('id = ?', $user -> getIdentity()) -> limit(1);

		$row = $table -> fetchRow($select);

		// Check post
		if (!$this -> getRequest() -> isPost()) {
			$form -> populate(array('style' => (null === $row ? '' : $row -> style)));
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Cool! Process
		$style = $form -> getValue('style');

		// Save
		if (null == $row) {
			$row = $table -> createRow();
			$row -> type = 'user_blog';
			// @todo this is not a real type
			$row -> id = $user -> getIdentity();
		}

		$row -> style = $style;
		$row -> save();

		$this -> view -> draft = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Your changes have been saved.");
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => false, 'messages' => array($this -> view -> message)));
	}

	public function viewAction() {
		// Check permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$blog = Engine_Api::_() -> getItem('blog', $this -> _getParam('blog_id'));
		if ($blog) {
			Engine_Api::_() -> core() -> setSubject($blog);
		}

		if (!$this -> _helper -> requireSubject() -> isValid()) {
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($blog, $viewer, 'view') -> isValid()) {
			return;
		}
		if (!$blog || !$blog -> getIdentity() || ($blog -> draft && !$blog -> isOwner($viewer)) || (!$blog -> is_approved && !$blog -> isOwner($viewer) && !$viewer -> isAdmin())) {
			return $this -> _helper -> requireSubject -> forward();
		}

		// Prepare data
		$blogTable = Engine_Api::_() -> getItemTable('blog');

		$this -> view -> blog = $blog;
		$this -> view -> owner = $owner = $blog -> getOwner();
		$this -> view -> viewer = $viewer;

		if (!$blog -> isOwner($viewer)) {
			$blogTable -> update(array('view_count' => new Zend_Db_Expr('view_count + 1')), array('blog_id = ?' => $blog -> getIdentity()));
		}

		// Get tags
		$this -> view -> blogTags = $blog -> tags() -> getTagMaps();

		// Get category
		if (!empty($blog -> category_id)) {
			$this -> view -> category = Engine_Api::_() -> getItemTable('blog_category') -> find($blog -> category_id) -> current();
		}

		// Get styles
		$table = Engine_Api::_() -> getDbtable('styles', 'core');
		$style = $table -> select() -> from($table, 'style') -> where('type = ?', 'user_blog') -> where('id = ?', $owner -> getIdentity()) -> limit(1);

		$row = $table -> fetchRow($style);
		if (!empty($row)) {
			$this -> view -> headStyle() -> appendStyle($row -> style);
		}
//		if ($blog -> link_detail) {
//			$view = Zend_Registry::get('Zend_View');
//			$view -> headLink(array('rel' => 'canonical', 'href' => $blog -> link_detail), 'PREPEND');
//		}

		// Render
		$this -> _helper -> content
			-> setEnabled();
	}

	public function becomeAction() {
		// Disable layout
		$this -> _helper -> layout -> disableLayout();
		// Don't use view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		// Check permission
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$blog = Engine_Api::_() -> getItem('blog', $this -> _getParam('blog_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($blog, $viewer, 'view') -> isValid())
			return;
		// Process
		$table = Engine_Api::_() -> getDbtable('becomes', 'ynblog');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			// Create become_member
			$become = $table -> createRow();
			$become -> blog_id = $blog -> blog_id;
			$become -> user_id = $viewer -> getIdentity();
			$become -> save();

			$blog -> become_count = $blog -> become_count + 1;
			$blog -> save();
			// Commit
			$db -> commit();
		} catch ( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function uploadPhotoAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> layout -> disableLayout();

		if (!Engine_Api::_() -> authorization() -> isAllowed('album', $viewer, 'create')) {
			return false;
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams('album', null, 'create') -> isValid())
			return;

		if (!$this -> _helper -> requireUser() -> checkRequire()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}
		if (!isset($_FILES['userfile']) || !is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return;
		}
		$albumPhoto_Table = NULL;
		$album_Table = NULL;
		if(Engine_Api::_() -> hasModuleBootstrap('advalbum'))
		{
			$albumPhoto_Table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
			$album_Table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		}
		else {
			$albumPhoto_Table = Engine_Api::_() -> getDbtable('photos', 'album');
			$album_Table = Engine_Api::_() -> getDbtable('albums', 'album');
		}

		$db = $albumPhoto_Table -> getAdapter();
		$db -> beginTransaction();

		try {
			$viewer = Engine_Api::_() -> user() -> getViewer();

			$photo = $albumPhoto_Table -> createRow();
			$photo -> setFromArray(array('owner_type' => 'user', 'owner_id' => $viewer -> getIdentity()));
			$photo -> save();
			Engine_Api::_() -> ynblog() -> setPhoto($photo, $_FILES['userfile']);

			$this -> view -> status = true;
			$this -> view -> name = $_FILES['userfile']['name'];
			$this -> view -> photo_id = $photo -> photo_id;
			$this -> view -> photo_url = Engine_Api::_() -> ynblog() -> finalizeUrl($photo -> getPhotoUrl());

			$album = Engine_Api::_() -> ynblog() -> getSpecialAlbum($viewer, 'blog');

			$photo -> album_id = $album -> album_id;
			$photo -> save();

			if (!$album -> photo_id) {
				$album -> photo_id = $photo -> getIdentity();
				$album -> save();
			}

			$auth = Engine_Api::_() -> authorization() -> context;
			$auth -> setAllowed($photo, 'everyone', 'view', true);
			$auth -> setAllowed($photo, 'everyone', 'comment', true);
			$auth -> setAllowed($album, 'everyone', 'view', true);
			$auth -> setAllowed($album, 'everyone', 'comment', true);
			$db -> commit();

		} catch( Album_Model_Exception $e ) {
			$db -> rollBack();
			$this -> view -> status = false;
			$this -> view -> error = $this -> view -> translate($e -> getMessage());
			throw $e;
			return;

		} catch( Exception $e ) {
			$db -> rollBack();
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			throw $e;
			return;
		}
	}

	public function rssAction() {
		// Disable layout
		$this -> _helper -> layout -> disableLayout();

		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Must be able to view blogs
		if (!Engine_Api::_() -> authorization() -> isAllowed('blog', $viewer, 'view')) {
			return;
		}
		$cat = $this -> _getParam('category');
		$blog_id = $this -> _getParam('rss_id');
		$owner_id = $this -> _getParam('owner');
		//
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynblog_main');
		if ($cat && $blog_id <= 0) {
			// Get navigation
			$params = array();
			if ($cat > 0) {
				$params['category'] = $cat;
				if ($owner_id) {
					$params['user_id'] = $owner_id;
					//
				}
				$categories = Engine_Api::_() -> getItemTable('blog_category') -> getCategories();
				foreach ($categories as $category) {
					if ($category -> category_id == $cat) {
						$pro_type_name = $category -> category_name;
					}
				}
			} else
				$pro_type_name = "All Blogs";
		} else {
			$pro_type_name = 'Blog';
			$params['blogRss'] = $blog_id;
		}
		$table = Engine_Api::_() -> getItemTable('blog');
		$blogs = $table -> fetchAll(Ynblog_Api_Core::getBlogsSelect($params));
		$this -> view -> blogs = $blogs;
		$this -> view -> pro_type_name = str_replace('&', '-', $pro_type_name);
		$this -> getResponse() -> setHeader('Content-type', 'text/xml');
	}
	public function shareAction() {
		$this->_helper->layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender ( TRUE );
        $blog_id = $this->_getParam ( 'blog_id' );
        $blog = Engine_Api::_ ()->getItem ( 'blog', $blog_id );
        if (!$blog) {
            return $this->_helper->requireSubject()->forward ();
        }
        $blog->share_count ++;
        $blog->save ();
        echo '{"share":"' . $blog->share_count . '"}';
	}

	public function showMessageAction() {
		if(Zend_Session::namespaceIsset('ynblog_import_result')){
			$messageSession = new Zend_Session_Namespace('ynblog_import_result');
			$this->view->aDuplicateBlogs = $messageSession->aDuplicateBlogs;
    		Zend_Session::namespaceUnset('ynblog_import_result');
    	}
	}

}
