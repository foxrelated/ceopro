<?php
//Get parser setting
$parser = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ultimate.parser', 1);
$serviceLink = ($parser) ? "http://newsservice.younetco.com/v1.1" : Engine_Api::_() -> ultimatenews() -> getCurrentHost() . "application/modules/Ultimatenews/Api/newsservice";

//Definde Constants
defined('YOUNET_NEWS_FEED_PARSER') or define('YOUNET_NEWS_FEED_PARSER', "{$serviceLink}/getfeed.php");
defined('YOUNET_NEWS_HOST') or define('YOUNET_NEWS_HOST', "{$serviceLink}/parser.php");
class Ultimatenews_IndexController extends Core_Controller_Action_Standard {
	public function indexAction() {

		$_SESSION['start_date'] = '';
		$_SESSION['end_date'] = '';
		$this -> _forward("list", "index", "ultimatenews");
	}

	public function detailAction() {
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function listAction() {
		$ultimatenews_search_query = "";
		if (isset($_POST['categoryparent']) && $_POST['categoryparent'] > -1) {
			$category_parent = $_POST['categoryparent'];
		} else {
			$category_parent = -1;
		}
		if (isset($_REQUEST['category']) && !empty($_REQUEST['category'])) {
			$category_id = $_REQUEST['category'];
		} else {
			$category_id = 0;
		}

		if (!isset($_POST['search']) && empty($_POST['search'])) {
			$searchText = "";
		} else {
			$searchText = $_POST['search'];
			$ultimatenews_search_arr = explode(" ", $searchText);
			$ultimatenews_searchs = array();
			foreach ($ultimatenews_search_arr as $item) {
				if ($item != "") {
					$ultimatenews_searchs[] = $item;
				}
			}
			$ultimatenews_search_query = implode("%", $ultimatenews_searchs);
			$ultimatenews_search_query = "%" . $ultimatenews_search_query . "%";
		}
		if (isset($_POST['nextpage']) && !(empty($_POST['nextpage']))) {
			$page = $_POST['nextpage'];
		} else {
			$page = 1;
		}
		$_SESSION['keysearch'] = array('nextpage' => $page, 'category' => $category_id, 'category_parent' => $category_parent, 'searchText' => $ultimatenews_search_query, 'keyword' => $searchText);

		$apiultimatenews = new Ultimatenews_Api_Core();
		if ($apiultimatenews -> checkVersionSE())//version 4.1.x
		{
			$this -> _helper -> content -> setNoRender() -> setEnabled();
		} else//version 4.0.x
		{
			$this -> _helper -> content -> render();
		}
	}

	public function tagAction() 
	{
		$tag_id = $this -> _getParam('tag_id');
		$this -> view -> tag_name = ucfirst($this -> _getParam('tag_name')); 
		$page = "1";
		if (isset($_POST['nextpage']) && !(empty($_POST['nextpage']))) {
			$page = $_POST['nextpage'];
		}
		$this -> view -> paginator = $paginator = Engine_Api::_() -> ultimatenews() -> getContentsPaginator(array('tag_id' => $tag_id, 'order' => 'pubDate DESC', 'is_active' => 1, 'getcommment' => true, ));
		$paginator -> setCurrentPageNumber($page);
		$paginator -> setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.newsPerPage', 10));
	}

	public function createNewsAction() 
	{
		$this->_helper->content
			//->setNoRender()
			->setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_news')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> form = $form = new Ultimatenews_Form_Create();
		$form -> removeElement('submit');
		$form -> addElement('Button', 'submit', array('label' => 'Save', 'type' => 'submit'));

		//Populate data to select
		$this -> view -> categories = $categories = Engine_Api::_() -> ultimatenews() -> getAllCategories(array('category_active' => 1, 'category_parent' => $_SESSION['keysearch']['category_parent']));
		foreach ($categories as $category) {
			$form -> category -> addMultiOption($category['category_id'], $category['category_name']);
		}

		$this -> view -> categoryparents = $categoryparents = Engine_Api::_() -> ultimatenews() -> getAllCategoryparents(array('category_active' => 1));
		foreach ($categoryparents as $categoryparent) {
			$form -> categoryparent -> addMultiOption($categoryparent['category_id'], $categoryparent['category_name']);
		}

		$other_id = 0;
		$other_name = 'Other';
		$form -> categoryparent -> addMultiOption($other_id, $other_name);

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		//Be Post values
		$values = $form -> getValues();
		if ($values['categoryparent'] > 0) {
			$categoryparent = Engine_Api::_() -> ultimatenews() -> getAllCategoryparents(array('category_id' => $values['categoryparent'], ));
			if ($categoryparent[0]['is_active'] <= 0) {
				$values['is_active'] = '0';
			} else {
				$values['is_active'] = $categoryparent[0]['is_active'];
			}
		}
		
		$des = $values['description'];
		$des = preg_replace('#<script[^>]*>.*?</script>#is', '', $des);
		$des = preg_replace('#<style[^>]*>.*?</style>#is','',$des);
		$des = preg_replace('#{[^}]*}#is','',$des);
		
		$body = $values['content'];
		$body = preg_replace('#<script[^>]*>.*?</script>#is', '', $body);
		$body = preg_replace('#<style[^>]*>.*?</style>#is','',$body);
		$body = preg_replace('#{[^}]*}#is','',$body);
		
		//Be prepare for adding news
		$edata = array('category_id' => $values['category'], 
		'owner_type' => "user", 
		'owner_id' => $viewer -> getIdentity(), 
		'title' => $values['title'], 
		'content' => $body, 
		'description' => $des, 
		'pubDate' => time(), 
		'pubDate_parse' => date('Y-m-d H:i:s'), 'posted_date' => date('Y-m-d H:i:s'), 'is_active' => $values['is_active']);
		
		// Check auto approved
		if(Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'approve_news'))
		{
			$edata['approved'] = 1;
		}
		else
		{
			$edata['approved'] = 0;
		}
		
		$db = Engine_Api::_() -> getDbtable('contents', 'ultimatenews') -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create event
			$table = $this -> _helper -> api() -> getDbtable('contents', 'ultimatenews');
			$content = $table -> createRow();
			$content -> setFromArray($edata);
			$content -> save();
			
			// handle tags
			$viewer = Engine_Api::_()->user()->getViewer();
		    $tags = preg_split('/[,]+/', $values['tags']);
		    $content->tags()->setTagMaps($viewer, $tags);
			
			// Set photo
			if (!empty($values['photo'])) {
				$content -> setPhoto($form -> photo, 0);
			}

			foreach ($form->getElements() as $name => $element) {
				$element -> setValue("");
			}
			$form -> addNotice("Add news successfully.");
			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function manageAction() {
		$this->_helper->content
			//->setNoRender()
			->setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'manage_news')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$page = $this -> _getParam('page', 1);
		$result = $this -> _getParam('result', null);
		$_SESSION['result'] = $result;
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Search( array('enableDate' => true));
		$values = array();
		if ($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
			if (empty($values['order'])) {
				$values['order'] = 'is_featured';
			}
			if (empty($values['direction'])) {
				$values['direction'] = 'DESC';
			}
			$this -> view -> filterValues = $values;
			$this -> view -> order = $values['order'];
			$this -> view -> direction = $values['direction'];
			$this -> view -> paginator = Engine_Api::_() -> ultimatenews() -> getContentsPaginator($values);
		}
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);

		if ($this -> getRequest() -> isPost()) {
			$values = $this -> getRequest() -> getPost();
			try {
				foreach ($values as $key => $value) {
					if ($key == 'delete_' . $value) {
						$content = Engine_Api::_() -> getItem('ultimatenews_content', $value);
						if ($content)
							$content -> delete();
					}
				}
			} catch (Exception $ex) {
				$_SESSION['result'] = 0;
			}
			$_SESSION['result'] = 1;
		}
	}

	public function myNewsAction()
	{
		$this->_helper->content
			//->setNoRender()
			->setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_news')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$page = $this -> _getParam('page', 1);
		$result = $this -> _getParam('result', null);
		$_SESSION['result'] = $result;
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Search( array('enableDate' => true));
		$values = array();
		if ($form -> isValid($this -> _getAllParams()))
		{
			$values = $form -> getValues();
			if (empty($values['order'])) {
				$values['order'] = 'is_featured';
			}
			if (empty($values['direction'])) {
				$values['direction'] = 'DESC';
			}
			$values['owner_id'] = $viewer -> getIdentity();
			$this -> view -> filterValues = $values;
			$this -> view -> order = $values['order'];
			$this -> view -> direction = $values['direction'];
			$this -> view -> paginator = Engine_Api::_() -> ultimatenews() -> getContentsPaginator($values);
		}
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);

		if ($this -> getRequest() -> isPost()) {
			$values = $this -> getRequest() -> getPost();
			try {
				foreach ($values as $key => $value) {
					if ($key == 'delete_' . $value) {
						$content = Engine_Api::_() -> getItem('ultimatenews_content', $value);
						if ($content)
							$content -> delete();
					}
				}
			} catch (Exception $ex) {
				$_SESSION['result'] = 0;
			}
			$_SESSION['result'] = 1;
		}
	}

	public function editAction() 
	{
		$this -> view -> form = $form = new Ultimatenews_Form_Edit();

		$content_id = Zend_Controller_Front::getInstance() -> getRequest() -> getParam('content_id');
		$Ultimatenews_info = Engine_Api::_() -> getItem('ultimatenews_content', $content_id);
		$Ultimatenews_info -> content = ($Ultimatenews_info -> content) ? $Ultimatenews_info -> content : $Ultimatenews_info -> description;
		$category_item = Engine_Api::_() -> getItem('ultimatenews_category', $Ultimatenews_info -> category_id);

		$categories = $categoryparents = array();
		if ($Ultimatenews_info -> category_id > 0) {
			$categories = Engine_Api::_() -> ultimatenews() -> getAllCategories(array('category_active' => 1, 'category_parent' => $category_item -> category_parent_id));
		}
		$this -> view -> categories = $categories;
		$this -> view -> categoryparents = $categoryparents = Engine_Api::_() -> ultimatenews() -> getAllCategoryparents(array('category_active' => 1));

		// Populate form
		foreach ($categoryparents as $categoryparent) {
			$form -> categoryparent -> addMultiOption($categoryparent['category_id'], $categoryparent['category_name']);
		}
		$other_id = 0;
		$other_name = 'Other';
		$form -> categoryparent -> addMultiOption($other_id, $other_name);

		if (!empty($categories)) {
			foreach ($categories as $category) {
				$form -> category -> addMultiOption($category['category_id'], $category['category_name']);
			}
			$form -> getElement('category') -> setValue($Ultimatenews_info -> category_id);
		} else {
			$form -> category -> clearMultiOptions();
			$noFeed_id = '-10';
			$noFeed_name = 'No Feeds';
			$form -> category -> addMultiOption($noFeed_id, $noFeed_name);
		}

		$form -> populate($Ultimatenews_info -> toArray());
		$form -> getElement('categoryparent') -> setValue($category_item -> category_parent_id);

		$tagStr = '';
		foreach ($Ultimatenews_info->tags()->getTagMaps() as $tagMap) {
			$tag = $tagMap -> getTag();
			if (!isset($tag -> text))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag -> text;
		}
		$form -> populate(array('tags' => $tagStr, ));
		$this -> view -> tagNamePrepared = $tagStr;

		if ($this -> getRequest() -> isPost()) {
			//Reload Feed list before populating
			$postVars = $this -> getRequest() -> getPost();

			if ($postVars['category'] < 0) {
				$form -> addError(Zend_Registry::get("Zend_Translate") -> _("Please choose the feed"));
				return;
			}
			$form -> category -> clearMultiOptions();
			$categories = Engine_Api::_() -> ultimatenews() -> getAllCategories(array('category_active' => 1, 'category_parent' => $postVars['categoryparent']));
			foreach ($categories as $category) {
				$form -> category -> addMultiOption($category['category_id'], $category['category_name']);
			}
			$form -> category -> addMultiOption('-10', Zend_Registry::get("Zend_Translate") -> _("No Feeds"));
			$form -> category -> addMultiOption('0', Zend_Registry::get("Zend_Translate") -> _("All Feeds"));

			//Checking Valid and Populate
			if ($this -> view -> form -> isValid($postVars)) {
				$db = Engine_Api::_() -> getDbTable('contents', 'ultimatenews') -> getAdapter();
				$db -> beginTransaction();
				try {
					$values = $form -> getValues();
					$des = $values['description'];
					$des = preg_replace('#<script[^>]*>.*?</script>#is', '', $des);
					$des = preg_replace('#<style[^>]*>.*?</style>#is','',$des);
					$des = preg_replace('#{[^}]*}#is','',$des);
					
					$body = $values['content'];
					$body = preg_replace('#<script[^>]*>.*?</script>#is', '', $body);
					$body = preg_replace('#<style[^>]*>.*?</style>#is','',$body);
					$body = preg_replace('#{[^}]*}#is','',$body);
					
					$values['description'] = $des;
					$values['content'] = $body;
					$Ultimatenews_info -> setFromArray($values);
					$Ultimatenews_info -> category_id = $values['category'];
					$Ultimatenews_info -> save();

					// handle tags
					$viewer = Engine_Api::_() -> user() -> getViewer();
					$tags = preg_split('/[,]+/', $values['tags']);
					$Ultimatenews_info -> tags() -> setTagMaps($viewer, $tags);

					// Set photo
					if (!empty($values['photo'])) {
						$Ultimatenews_info -> setPhoto($form -> photo, 0);
					}

					$db -> commit();
					$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Your changes have been saved.')));
				} catch (Exception $e) {
					$db -> rollback();
					$this -> view -> success = false;
				}
			}

		}
	}

	public function loaddataAction() {
		$page = "1";
		$category = "0";
		$limit = 5;
		if (isset($_POST['nextpage']) && !(empty($_POST['nextpage']))) {
			$page = $_POST['nextpage'];
		}

		if (isset($_POST['category']) && !(empty($_POST['category']))) {
			$category = $_POST['category'];
		}

		if (isset($_POST['limit']) && !(empty($_POST['limit']))) {
			$limit = $_POST['limit'];
		}

		$this -> view -> paginator = Engine_Api::_() -> ultimatenews() -> getContentsPaginator(array('category_id' => $category, 'checkcomment' => 'yes', 'limit' => $limit, 'order' => 'content_id DESC', ));
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> categoryId = $category;
		$this -> view -> limit = $limit;
	}

	public function loadfeedAction() {
		$categoryparent_id = $this -> _getParam('categoryparent');
		$categories = Engine_Api::_() -> ultimatenews() -> getAllCategories(array('category_active' => 1, 'category_parent' => $categoryparent_id));
		$html = '';
		foreach ($categories as $category) {
			$html .= '<option value="' . $category['category_id'] . '" label="' . $category['category_name'] . '" >' . $category['category_name'] . '</option>';
		}
		$this -> view -> html = $html;
		return;
	}

	public function deleteallAction() {
		$table = Engine_Api::_() -> getDbtable('contents', 'ultimatenews');
		$select = $table -> select();

		$contents = $table -> fetchAll($select);
		if (count($contents) > 0)
			foreach ($contents as $content) {
				$content -> delete();
			}
	}

	public function contentsAction() {
		$categoryparent_id = $this -> _getParam('categoryparent');
		$this -> view -> category_id = $categoryparent_id;
		if ($categoryparent_id > 0)
			$this -> view -> categoryparent = Engine_Api::_() -> getItem('ultimatenews_categoryparent', $categoryparent_id);
		$page = "1";
		if (isset($_POST['nextpage']) && !(empty($_POST['nextpage']))) {
			$page = $_POST['nextpage'];
		}
		$this -> view -> paginator = $paginator = Engine_Api::_() -> ultimatenews() -> getContentsPaginator(array('categoryparent' => $categoryparent_id, 'order' => 'pubDate DESC', 'is_active' => 1, 'getcommment' => true, ));
		$paginator -> setCurrentPageNumber($page);
		$paginator -> setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.newsPerPage', 10));
		$select = Engine_Api::_() -> ultimatenews() -> getCategoryparentsSelect(array('category_active' => 1));
		$table = Engine_Api::_() -> getItemTable('ultimatenews_categoryparent');
		$this -> view -> categories = $categories = $table -> fetchAll($select);
	}

	public function feedAction() {
		$this->_helper->content
			//->setNoRender()
			->setEnabled();
		$category_id = $this -> _getParam('category');
		if ($category_id > 0)
		{
			$this -> view -> category = $category = Engine_Api::_() -> getItem('ultimatenews_category', $category_id);
			if(!Engine_Api::_() -> core() -> hasSubject())
			{
                Engine_Api::_() ->core() ->setSubject($category);
            }
		}
		$page = "1";
		if (isset($_POST['nextpage']) && !(empty($_POST['nextpage']))) {
			$page = $_POST['nextpage'];
		}
		$this -> view -> paginator = $paginator = Engine_Api::_() -> ultimatenews() -> getContentsPaginator(array('category_id' => $category_id, 'order' => 'pubDate DESC', 'is_active' => 1, 'getcommment' => true, ));
		$paginator -> setCurrentPageNumber($page);
		$paginator -> setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.newsPerPage', 10));
	}

	public function listsAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		// Perms
		$this -> view -> canComment = $canComment = ($subject -> authorization() -> isAllowed($viewer, 'comment') && Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'comment'));
		$this -> view -> canDelete = false;

		// Likes
		$this -> view -> viewAllLikes = $this -> _getParam('viewAllLikes', false);
		$this -> view -> likes = $likes = $subject -> likes() -> getLikePaginator();

		// Comments

		// If has a page, display oldest to newest
		if (null !== ($page = $this -> _getParam('page'))) {
			$commentSelect = $subject -> comments() -> getCommentSelect();
			$commentSelect -> order('comment_id ASC');
			$comments = Zend_Paginator::factory($commentSelect);
			$comments -> setCurrentPageNumber($page);
			$comments -> setItemCountPerPage(10);
			$this -> view -> comments = $comments;
			$this -> view -> page = $page;
		}

		// If not has a page, show the
		else {
			$commentSelect = $subject -> comments() -> getCommentSelect();
			$commentSelect -> order('comment_id DESC');
			$comments = Zend_Paginator::factory($commentSelect);
			$comments -> setCurrentPageNumber(1);
			$comments -> setItemCountPerPage(4);
			$this -> view -> comments = $comments;
			$this -> view -> page = $page;
		}

		if ($viewer -> getIdentity() && $canComment) {
			$this -> view -> form = $form = new Core_Form_Comment_Create();
			$form -> populate(array('identity' => $subject -> getIdentity(), 'type' => $subject -> getType(), ));
		}
	}

	public function uploadPhotoAction() {
		// Disable layout
		$this->_helper->layout->disableLayout ();

		$user_id = Engine_Api::_ ()->user ()->getViewer ()->getIdentity ();
		$destination = "public/ultimatenews/";
		if (! is_dir ( $destination )) {
			mkdir ( $destination );
		}
		$destination = "public/ultimatenews/" . $user_id . "/";
		if (! is_dir ( $destination )) {
			mkdir ( $destination );
		}
		$upload = new Zend_File_Transfer_Adapter_Http ();
		$upload->setDestination ( $destination );
		$file_info = pathinfo($upload -> getFileName('userfile', false));

        $fullFilePath = $destination . time() . '.' . $file_info['extension'];

		$image = Engine_Image::factory ();
		$image->open ( $_FILES ['userfile'] ['tmp_name'] )->resize ( 720, 720 )->write ( $fullFilePath );

		$this->view->status = true;
		$this->view->name = $_FILES ['userfile'] ['name'];
		$this->view->photo_url = Zend_Registry::get ( 'StaticBaseUrl' ) . $fullFilePath;
		$this->view->photo_width = $image->getWidth ();
		$this->view->photo_height = $image->getHeight ();
	}

	public function featuredAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'manage_news')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if ($this -> getRequest() -> isPost()) {
			$value = $this -> getRequest() -> getPost();
			$content_ids = explode(',', $value['ultimatenews_featured']);
			$content = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');
			try {
				foreach ($content_ids as $content_id) {
					if (is_numeric($content_id)) {
						$where_content = $content -> getAdapter() -> quoteInto('content_id = ?', $content_id);
						$content -> update(array('is_featured' => $value['is_set_featured']), $where_content);
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 0;
				$_SESSION['result'] = 0;
			}
			$this -> view -> result = 2;
		}

		if ($this -> getRequest() -> page > 1 && !empty($this -> getRequest() -> page))
		{
			return $this->_helper->redirector->gotoRoute(array('controller'=>'index', 'action'=>'manage', 'page' => $this -> getRequest() -> page), 'ultimatenews_general', true);
		}
		else 
		{
			return $this->_helper->redirector->gotoRoute(array('controller'=>'index', 'action'=>'manage'), 'ultimatenews_general', true);
		}
	}
	
	public function approveAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'manage_news')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if ($this -> getRequest() -> isPost()) {
			$value = $this -> getRequest() -> getPost();
			$content_ids = explode(',', $value['ultimatenews_approve']);
			$content = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');
			if($value['is_set_approve'] == 0)
			{
				$value['is_set_approve'] = -1;
			}
			try {
				foreach ($content_ids as $content_id) {
					if (is_numeric($content_id)) 
					{
						$where_content = $content -> getAdapter() -> quoteInto('content_id = ? AND approved = 0', $content_id);
						$content -> update(array('approved' => $value['is_set_approve']), $where_content);
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 0;
				$_SESSION['result'] = 0;
			}
			$this -> view -> result = 2;
		}

		if ($this -> getRequest() -> page > 1 && !empty($this -> getRequest() -> page))
		{
			return $this->_helper->redirector->gotoRoute(array('controller'=>'index', 'action'=>'manage', 'page' => $this -> getRequest() -> page), 'ultimatenews_general', true);
		}
		else 
		{
			return $this->_helper->redirector->gotoRoute(array('controller'=>'index', 'action'=>'manage'), 'ultimatenews_general', true);
		}
	}

	//add subscribe
	public function subscribeAction() {
		$feed = $this -> _getParam('feed', 0);
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (empty($feed)) {
			return;
		}
		$category = Engine_Api::_() -> getItem('ultimatenews_category', $feed);
		$users = Zend_Json::decode($category -> subscribe);
		$users[$viewer -> getIdentity()] = $viewer -> getIdentity();
		$category -> subscribe = Zend_Json::encode($users);
		$category -> save();

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have just subscribed this RSS feed successfully.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function unsubscribeAction() {
		$feed = $this -> _getParam('feed', 0);
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (empty($feed)) {
			return;
		}
		$category = Engine_Api::_() -> getItem('ultimatenews_category', $feed);
		$users = Zend_Json::decode($category -> subscribe);
		unset($users[$viewer -> getIdentity()]);
		$category -> subscribe = Zend_Json::encode($users);
		$category -> save();

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have just unsubscribed this RSS feed successfully.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function yourSubscribeAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        $this->_helper->content
            //->setNoRender()
            ->setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$tb = Engine_Api::_() -> getItemTable('ultimatenews_category');
		$select = $tb -> select() -> where("subscribe LIKE '%:?%'", $viewer -> getIdentity());
		$paginator = Zend_Paginator::factory($tb -> fetchAll($select));

		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		$paginator -> setItemCountPerPage(10);
		$this -> view -> paginator = $paginator;
	}
	
	public function manageFeedAction()
	{
		$this->_helper->content
			//->setNoRender()
			->setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'manage_feed')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$page = $this -> _getParam('page', 1);
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Search();
		$form -> title -> setLabel("Feed Name");
		$values = array();
		if ($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
			if (empty($values['order'])) {
				$values['order'] = 'category_id';
			}
			if (empty($values['direction'])) {
				$values['direction'] = 'DESC';
			}
			$this -> view -> filterValues = $values;
			$this -> view -> order = $values['order'];
			$this -> view -> direction = $values['direction'];
			$this -> view -> paginator = Engine_Api::_() -> ultimatenews() -> getCategoriesPaginator($values);
		}
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);

		if ($this -> getRequest() -> isPost()) {

			$values = $this -> getRequest() -> getPost();
			try {
				foreach ($values as $key => $value) {
					if ($key == 'delete_' . $value) {
						$category = Engine_Api::_() -> getItem('ultimatenews_category', $value);
						$category -> delete();
						$this -> deleteItemData($value);
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 1;
				throw $e;
				$_SESSION['result'] = 0;
			}
			if (!isset($_SESSION['result']))
				$_SESSION['result'] = 1;
			if (isset($_SESSION['result']))
				$this -> view -> result = $_SESSION['result'];
		} else {
			if (isset($_SESSION['result']))
				unset($_SESSION['result']);
		}
	}

	public function myFeedAction()
	{
		$this->_helper->content
			//->setNoRender()
			->setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_feed')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$page = $this -> _getParam('page', 1);
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Search();
		$form -> title -> setLabel("Feed Name");
		$values = array();
		if ($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
			if (empty($values['order'])) {
				$values['order'] = 'category_id';
			}
			if (empty($values['direction'])) {
				$values['direction'] = 'DESC';
			}
			$values['owner_id'] = $viewer -> getIdentity();
			$this -> view -> filterValues = $values;
			$this -> view -> order = $values['order'];
			$this -> view -> direction = $values['direction'];
			$this -> view -> paginator = Engine_Api::_() -> ultimatenews() -> getCategoriesPaginator($values);
		}
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);

		if ($this -> getRequest() -> isPost()) {

			$values = $this -> getRequest() -> getPost();
			try {
				foreach ($values as $key => $value) {
					if ($key == 'delete_' . $value) {
						$category = Engine_Api::_() -> getItem('ultimatenews_category', $value);
						$category -> delete();
						$this -> deleteItemData($value);
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 1;
				throw $e;
				$_SESSION['result'] = 0;
			}
			if (!isset($_SESSION['result']))
				$_SESSION['result'] = 1;
			if (isset($_SESSION['result']))
				$this -> view -> result = $_SESSION['result'];
		} else {
			if (isset($_SESSION['result']))
				unset($_SESSION['result']);
		}
	}

	function deleteItemData($category_id) {
		$table = Engine_Api::_() -> getDbtable('Contents', 'ultimatenews');
		$selectTop = $table -> select() -> where('category_id = ? ', $category_id);

		$contents = $table -> fetchAll($selectTop);
		foreach ($contents as $content) {
			$content -> delete();
		}
	}
	public function createFeedAction() {
		$this->_helper->content
			//->setNoRender()
			->setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_feed')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		set_time_limit(0);
		$this -> view -> form = $form = new Ultimatenews_Form_CreateFeed();
		$form -> setTitle('Add RSS Feed');
		//Checking POST status
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		//Getting form value
		$values = $form -> getValues();
		$postVar = $this -> getRequest() -> getPost();

		//Add validator for number of characters
		if ($postVar['full_content'] == '1') 
		{
			$characterElement = $form -> getElement('characters');
			$characterElement -> setValidators(array( array('Int', true), new Engine_Validate_AtLeast(0), ));
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$values = $form -> getValues();
		$isValid = $this -> isValidData($values);
		if ($isValid == false) {
			$form -> addError("The Feed name or the Feed URL already exists.");
			return;
		}
		// add rss feed
		$feedOption = array('uri' => urlencode($values['url_resource']));
		$val = array();
		$content = file_get_contents(YOUNET_NEWS_FEED_PARSER . '?' . http_build_query($feedOption));

		if (null !== $content) {
			$feedInfo = json_decode($content, 1);
			$val['item_count'] = $feedInfo['item_count'];
			$val['logo'] = $feedInfo['logo'];
			$val['favicon'] = $feedInfo['favicon'];
		} else {

			$form -> addError("Invalid input URL or no item content");
			return;
		}
		if (!empty($values['logo'])) {
			$values['category_logo'] = $this -> uploadPhoto($form -> logo);

			if ($values['category_logo'] == NULL) {
				$form -> addError("Invalid file type for Feed provider logo");
				return;
			}
		} elseif ($values['category_logo'] == "") {
			if (isset($feedInfo['logo'])) {
				$values['category_logo'] = $feedInfo['logo'];
			}
		}
		if (isset($feedInfo['favicon']) && $this -> checkLogo($feedInfo['favicon'])) {
			$values['logo'] = $feedInfo['favicon'];
		}
		$values['posted_date'] = date('Y-m-d H:i:s');
		if ($values['category_parent_id'] > 0) 
		{
			$categoryparent = Engine_Api::_() -> ultimatenews() -> getAllCategoryparents(array('category_id' => $values['category_parent_id'], ));
			if ($categoryparent[0]['is_active'] <= 0) {
				$values['is_active'] = '0';
			} else {
				$values['is_active'] = $categoryparent[0]['is_active'];
			}
		}
		$values['owner_id'] = $viewer -> getIdentity();
		
		// Check auto approved
		if(Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'approve_rss'))
		{
			$values['approved'] = 1;
		}
		else
		{
			$values['approved'] = 0;
		}
		
		$db = Engine_Api::_() -> getDbtable('categories', 'ultimatenews') -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create event
			$table = $this -> _helper -> api() -> getDbtable('categories', 'ultimatenews');
			$feed = $table -> createRow();
			$feed -> setFromArray($values);
			$feed -> save();

			// Add tags
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$tags = preg_split('/[,]+/', $values['tags']);
			$feed -> tags() -> addTagMaps($viewer, $tags);

			foreach ($form->getElements() as $name => $element) 
			{
				if(!in_array($name, array('is_active', 'mini_logo', 'display_logo', 'full_content')))
					$element -> setValue("");
				else {
					$element -> setValue(1);
				}
			}
			$db -> commit();
			$form -> addNotice("Add new RSS Feed successfully.");
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}
	function isValidData($data = array()) {
		$categories = Engine_Api::_() -> ultimatenews() -> getAllCategories();

		foreach ($categories as $category) {
			if (($category['category_name'] == trim($data['category_name'])) || ($category['url_resource'] == trim($data['url_resource']))) {
				return false;
			}
		}
		return true;
	}
	private function uploadPhoto($photo) {
		$imglist = array('gif', 'png');
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
		} else if (is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
		} else if (is_string($photo) && file_exists($photo)) {
			$file = $photo;
		} else {
			echo 'Invalid argument passed to setPhoto' . print_r($photo, true);
			return false;
		}
		$info = pathinfo($file);
		if (!in_array(strtolower($info['extension']), $imglist))
			return NULL;
		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'category_logo', 'parent_id' => Engine_Api::_() -> user() -> getViewer() -> getIdentity());

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name, $info['extension']) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name, $info['extension']) -> destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(100, 100) -> write($path . '/in_' . $name, $info['extension']) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;

		$image -> resample($x, $y, $size, $size, 65, 65) -> write($path . '/is_' . $name, $info['extension']) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');

		// Update row
		return $iMain -> storage_path;
	}
	public function checkLogo($url_logo) {
		if ($url_logo != "") {
			$data = file_get_contents($url_logo);
			if ($data)
				return true;
			else {
				return false;
			}
		}
		return false;
	}
	
	public function favoriteAjaxAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$contentId = $this -> _getParam('content_id', 0);
		if(!$content = Engine_Api::_() -> getItem('ultimatenews_content', $contentId))
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbTable('favorites', 'ultimatenews');
		$row = $table -> createRow();
		$row -> user_id = $viewer -> getIdentity();
		$row -> content_id = $content -> getIdentity();
		$row -> save();
		exit();
	}
	
	public function unFavoriteAjaxAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$contentId = $this -> _getParam('content_id', 0);
		if(!$content = Engine_Api::_() -> getItem('ultimatenews_content', $contentId))
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbTable('favorites', 'ultimatenews');
		$select = $table -> select() -> where('content_id = ?', $contentId) -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1);
		$row = $table -> fetchRow($select);
		if($row)
		{
			$row -> delete();
		}
		exit();
	}
	
	public function favoriteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
        $this->_helper->content
            //->setNoRender()
            ->setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = "1";
		if (isset($_POST['nextpage']) && !(empty($_POST['nextpage']))) 
		{
			$page = $_POST['nextpage'];
		}
		$this -> view -> paginator = $paginator = Engine_Api::_() -> ultimatenews() -> getContentsPaginator(array('favorite_owner_id' => $viewer -> getIdentity(), 'order' => 'pubDate DESC', 'is_active' => 1, 'getcommment' => true, ));
		$paginator -> setCurrentPageNumber($page);
		$paginator -> setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.newsPerPage', 10));
	}
	
	public function unFavoriteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$contentId = $this -> _getParam('content_id', 0);
		if(!$content = Engine_Api::_() -> getItem('ultimatenews_content', $contentId))
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbTable('favorites', 'ultimatenews');
		$select = $table -> select() -> where('content_id = ?', $contentId) -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1);
		$row = $table -> fetchRow($select);
		if($row)
		{
			$row -> delete();
		}
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Unfavorite successfully.'))
		));
	}
}
?>