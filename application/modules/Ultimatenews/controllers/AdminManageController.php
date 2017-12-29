<?php
//Get parser setting
$parser = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ultimate.parser', 1);
$serviceLink = ($parser) ? "http://newsservice.younetco.com/v1.1" : Engine_Api::_() -> ultimatenews() -> getCurrentHost() . "application/modules/Ultimatenews/Api/newsservice";

//Definde Constants
defined('YOUNET_NEWS_FEED_PARSER') or define('YOUNET_NEWS_FEED_PARSER', "{$serviceLink}/getfeed.php");
defined('YOUNET_NEWS_HOST') or define('YOUNET_NEWS_HOST', "{$serviceLink}/parser.php");

class Ultimatenews_AdminManageController extends Core_Controller_Action_Admin {
	public function indexAction() {
		$_SESSION['result'] = null;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ultimatenews_admin_main', array(), 'ultimatenews_admin_main_manage');

		$page = $this -> _getParam('page', 1);
		$result = $this -> _getParam('result', null);
		$_SESSION['result'] = $result;
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Search( array('enableDate' => true));
		$values = array();
		if ($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
			if (empty($values['order'])) {
				$values['order'] = 'content_id';
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

		if ($this -> getRequest() -> isPost() && !isset($_POST['order'])) {
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

	public function feedAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ultimatenews_admin_main', array(), 'ultimatenews_admin_main_category');

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
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> formValues = $values;
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

	public function categoriesAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ultimatenews_admin_main', array(), 'ultimatenews_admin_main_categories');
		$page = $this -> _getParam('page', 1);
		$this -> view -> formsearch = $formsearch = new Ultimatenews_Form_Admin_Search();
		$formsearch -> title -> setLabel("Name");
		$values_search = array();
		if ($formsearch -> isValid($this -> _getAllParams())) {
			$values_search = $formsearch -> getValues();
			if (empty($values_search['order'])) {
				$values_search['order'] = 'category_id';
			}
			if (empty($values_search['direction'])) {
				$values_search['direction'] = 'DESC';
			}
			$this -> view -> filterValues = $values_search;
			$this -> view -> order = $values_search['order'];
			$this -> view -> direction = $values_search['direction'];
			$this -> view -> paginator = Engine_Api::_() -> ultimatenews() -> getCategoryparentsPaginator($values_search);
		}
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		if (isset($_POST['buttondelete'])) {
			$values = $_POST;
			try {
				foreach ($values as $key => $value) {
					if ($key == 'delete_' . $value) {
						$category = Engine_Api::_() -> getItem('ultimatenews_categoryparent', $value);
						if ($category)
							$category -> delete();

						$this -> deleteItemData($value);
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 1;
				$_SESSION['result'] = 0;
			}
			if (!isset($_SESSION['result']))
				$_SESSION['result'] = 1;
			if (isset($_SESSION['result']))
				$this -> view -> result = $_SESSION['result'];
		}
		$this -> view -> paginator = Engine_Api::_() -> ultimatenews() -> getCategoryparentsPaginator($values_search);
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	function deleteItemData($category_id) {
		$table = Engine_Api::_() -> getDbtable('Contents', 'ultimatenews');
		$selectTop = $table -> select() -> where('category_id = ? ', $category_id);

		$contents = $table -> fetchAll($selectTop);
		foreach ($contents as $content) {
			$content -> delete();
		}
	}

	function isValidDataCategory($data = array()) {
		$categories = Engine_Api::_() -> ultimatenews() -> getAllCategoryparents();

		foreach ($categories as $category) {
			if (($category['category_name'] == trim($data['category_name'])) && (!isset($data['category_id']) || $category['category_id'] != $data['category_id'])) {
				return false;
			}
		}

		return true;
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

	public function createAction() {
		set_time_limit(0);
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ultimatenews_admin_main', array(), 'ultimatenews_admin_main_create');
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Manage_Create();

		//Checking POST status
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		//Getting form value
		$values = $form -> getValues();
		$postVar = $this -> getRequest() -> getPost();

		//Add validator for number of characters
		if ($postVar['full_content'] == '1') {
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
		if ($values['category_parent_id'] > 0) {
			$categoryparent = Engine_Api::_() -> ultimatenews() -> getAllCategoryparents(array('category_id' => $values['category_parent_id'], ));
			if ($categoryparent[0]['is_active'] <= 0) {
				$values['is_active'] = '0';
			} else {
				$values['is_active'] = $categoryparent[0]['is_active'];
			}
		}
		$values['owner_id'] = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
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

	private function uploadPhoto($photo) {
		$imglist = array('gif', 'png');
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
		} else if (is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
		} else if (is_string($photo) && file_exists($photo)) {
			$file = $photo;
		} else if ($photo instanceof Engine_Form_Element_Text) {
            return$photo->getValue();

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

	public function saveImg($url, $name) {
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'category_logo', 'parent_id' => Engine_Api::_() -> user() -> getViewer() -> getIdentity());

		$check_allow_url_fopen = ini_get('allow_url_fopen');
		if (($check_allow_url_fopen == 'on') || ($check_allow_url_fopen == 'On') || ($check_allow_url_fopen == '1')) {
			$gis = getimagesize($url);
			$type = $gis[2];
			switch($type) {
				case "1" :
					$imorig = imagecreatefromgif($url);
					break;
				case "2" :
					$imorig = imagecreatefromjpeg($url);
					break;
				case "3" :
					$imorig = imagecreatefrompng($url);
					break;
				default :
					$imorig = imagecreatefromjpeg($url);
			}
		} else {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
			$imorig = imagecreatefromstring($data);
		}

		// Save
		$storage = Engine_Api::_() -> storage();
		$filename = $path . DIRECTORY_SEPARATOR . $name . '.png';

		$im = imagecreatetruecolor(150, 112);
		$x = imagesx($imorig);
		$y = imagesy($imorig);
		if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, 150, 112, $x, $y)) {
			imagejpeg($im, $filename);
		}
		$iMain = $storage -> create($path . '/' . $name . '.png', $params);
		@unlink($filename);
		return $iMain;
	}

	public function editAction() {
		set_time_limit(0);
		$category_id = $this -> _getParam('id', null);
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Manage_Create();

		$category = Engine_Api::_() -> getItem('ultimatenews_category', $category_id);
		if($category -> approved != 1)
		{
			$form -> removeElement('is_active');
		}
		// Posting form
		if ($this -> getRequest() -> isPost()) {
			//Add validator for number of characters
			$postVar = $this -> getRequest() -> getPost();
			if ($postVar['full_content'] == '1') {
				$characterElement = $form -> getElement('characters');
				if (!is_numeric($postVar['characters']) || $postVar['characters'] < 0) {
					$form -> addError(Zend_Registry::get("Zend_Translate") -> _("Number to display limited characters must be a valid integer"));
					$form -> populate($postVar);
					return;
				}
				if ($postVar['characters'] > 2147483648) {
					$form -> addError(Zend_Registry::get("Zend_Translate") -> _("Number to display limited characters is too big. Please set 0 for unlimited"));
					$form -> populate($postVar);
					return;
				}
				$characterElement -> setValidators(array( array('Int', true), new Engine_Validate_AtLeast(0), ));
			}

			if ($form -> isValid($this -> getRequest() -> getPost())) {
				$data_array = $form -> getValues();
				$feedOption = array('uri' => urlencode($data_array['url_resource']));
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
				if (!empty($data_array['logo_img'])) {

					$data_array['category_logo'] = $this -> uploadPhoto($form -> logo_img);
					if ($data_array['category_logo'] == NULL) {
						$form -> addError("Invalid file type for Feed provider logo");
						return;
					}
				} elseif ($data_array['category_logo'] == "") {

					if (isset($feedInfo['logo'])) {
						$data_array['category_logo'] = $feedInfo['logo'];
					}
				}
				
				if (!empty($data_array['favicon_img'])) {

					$data_array['favicon'] = $this -> uploadPhoto($form -> favicon_img);
					if ($data_array['favicon'] == NULL) {
						$form -> addError("Invalid file type for Feed provider favicon");
						return;
					}
				} elseif ($data_array['logo'] == "") {

					if (isset($feedInfo['favicon'])) {
						$data_array['favicon'] = $feedInfo['favicon'];
					}
				}
				if (isset($data_array['favicon']) && $this -> checkLogo($data_array['favicon'])) {
					$data_array['logo'] = $data_array['favicon'];
				}
				if ($data_array['category_parent_id'] != 0) {
					$tb = Engine_Api::_() -> getDbTable('categoryparents', 'ultimatenews');
					$select = $tb -> select() -> where("category_id = ?", $data_array['category_parent_id']) -> where("is_active =?", 0);
					$inactive = $tb -> fetchAll($select);
					if (count($inactive) > 0) {
						$data_array['is_active'] = 0;
					}
				}
				// handle tags
				$viewer = Engine_Api::_() -> user() -> getViewer();
				$tags = preg_split('/[,]+/', $data_array['tags']);
				$category -> tags() -> setTagMaps($viewer, $tags);

				$category -> setFromArray($data_array);
				$category -> save();
			}

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Feed Edited.')));
		}

		// Initialize data
		else {
			$form -> populate($category -> toArray());
			$tagStr = '';
			foreach ($category->tags()->getTagMaps() as $tagMap) {
				$tag = $tagMap -> getTag();
				if (!isset($tag -> text))
					continue;
				if ('' !== $tagStr)
					$tagStr .= ', ';
				$tagStr .= $tag -> text;
			}
			$form -> populate(array('tags' => $tagStr, ));
			$this -> view -> tagNamePrepared = $tagStr;
		}
	}

	public function createCategoryAction() {
        $this -> view -> form = $form = new Ultimatenews_Form_Admin_Manage_Createcategory();
        $form->setTitle('Add New Category');
        if (isset($_POST['category_name']) && isset($_POST['add'])) {
            if (!$form -> isValid($this -> getRequest() -> getPost())) {
                $form -> addError("The Category name is invalid");
                return;
            }

            $values = $form -> getValues();
            if (trim($values['category_name'] == "")) {
                $form -> addError("Please insert category name - it is required.");
                return;
            }
            $isValid = $this -> isValidDataCategory($values);
            if ($isValid == false) {
                $form -> addError("The Category name already exists.");
                return;
            }
            $db = Engine_Api::_() -> getDbtable('categoryparents', 'ultimatenews') -> getAdapter();
            $db -> beginTransaction();

            try {
                // Create category
                $table = $this -> _helper -> api() -> getDbtable('categoryparents', 'ultimatenews');
                $category = $table -> createRow();

                $category -> setFromArray($values);
                $category -> save();
                $db -> commit();
                $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Category created.')));
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }
        }
    }

	public function editCategoryAction() {

		$category_id = $this -> _getParam('id', null);
		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Manage_Createcategory();
        $form->setTitle('Edit Category');

		$category = Engine_Api::_() -> ultimatenews() -> getAllCategoryparents(array('category_id' => $category_id, ));

		// Posting form
		if (isset($_POST['category_name'])) {
			$data_array = $_POST;
			if (trim($data_array['category_name'] == "")) {
				$form -> addError("Please insert category name - it is required.");
				return;
			}
            $data_array['category_id'] = $category_id;
            $isValid = $this -> isValidDataCategory($data_array);
            if ($isValid == false) {
                $form -> addError("The Category name already exists.");
                $form -> populate($data_array);
                return;
            }
			$table = Engine_Api::_() -> getDbTable('categoryparents', 'ultimatenews');
			if ($data_array['is_active'] == "")
				$data_array['is_active'] = 0;
			$where = $table -> getAdapter() -> quoteInto('category_id = ?', $category_id);
			$table -> update(array('category_name' => $data_array['category_name'], 'category_description' => $data_array['category_description'], 'is_active' => $data_array['is_active']), $where);

			if (is_numeric($category_id)) {
				$category = Engine_Api::_() -> getDbTable('categories', 'ultimatenews');
				$content = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');

				$where_category = $category -> getAdapter() -> quoteInto('category_parent_id = ?', $category_id);
				$category -> update(array('is_active' => $data_array['is_active']), $where_category);

				$categories = Engine_Api::_() -> ultimatenews() -> getAllCategories(array('category_parent' => $category_id, ));
				foreach ($categories as $categoryitem) {
					$where_content = $content -> getAdapter() -> quoteInto('category_id = ?', $categoryitem['category_id']);
					$content -> update(array('is_active' => $data_array['is_active']), $where_content);
				}
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Category Edited.')));
		} else {
			foreach ($form->getElements() as $name => $element) {
				if (isset($category[0][$name])) {
					$element -> setValue($category[0][$name]);
				}
			}
		}
	}

	private function getAtomFeed($url, $cateogry_id, $owner_id) {
		$url_arr = parse_url($url);
		if ($url_arr['scheme'] != 'http') {
			$url = str_replace($url_arr['scheme'] . '://', 'http://', $url);
		}
		$feed = Zend_Feed::import($url);
		$data = array('title' => $feed -> title(), 'link' => $feed -> id(), 'dateModified' => $feed -> updated(), 'description' => $feed -> subtitle(), 'author' => $feed -> author(), 'entries' => array(), );
		$count_feed = count($feed);
		foreach ($feed as $entry) {
			if ($entry -> updated() == null) {
				$time = strtotime($feed -> updated()) + $count_feed;
				$count_feed--;
				$pub = "";
			} else {
				$time = strtotime($entry -> updated());
				$pub = $entry -> updated();
			}
			$is_active = 1;
			$category = Engine_Api::_() -> ultimatenews() -> getAllCategories(array('category_id' => $cateogry_id, ));
			if ($category[0]['is_active'] == 0) {
				$is_active = 0;
			}
			if ($data['author'] == "") {
				$url_arr = parse_url($data['link']);

				$host = @$url_arr['host'];

				if (strpos($host, 'http') || $host == "") {
					$url = str_replace('http://', '', $feed['link']);

					$index = strpos($url, '/');

					$host = substr($url, 0, $index);
				}
				$data['author'] = $host;
			}
			$edata = array('category_id' => $cateogry_id, 'owner_type' => "user", 'owner_id' => $owner_id, 'title' => $entry -> title(), 'description' => $entry -> summary(), 'content' => $entry -> content(), 'image' => "", 'link_detail' => $entry -> id(), 'author' => $data['author'], 'pubDate' => $time, 'pubDate_parse' => $pub, 'posted_date' => date('Y-m-d H:i:s'), 'is_active' => $is_active);

			//insert data to database
			$db = Engine_Api::_() -> getDbtable('contents', 'ultimatenews') -> getAdapter();
			$db -> beginTransaction();

			try {
				//check Ultimatenews exist by link
				$content = Engine_Api::_() -> ultimatenews() -> getAllContent(array('link_detail' => $edata['link_detail'], ));
				if (count($content) > 0) {
					$table = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');
					$where = $table -> getAdapter() -> quoteInto('content_id = ?', $content[0]['content_id']);
					$table -> update($edata, $where);
				} else {
					// Create content
					$table = $this -> _helper -> api() -> getDbtable('contents', 'ultimatenews');
					$content = $table -> createRow();
					$content -> setFromArray($edata);
					$content -> save();
				}
				$edata = null;
			} catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}
		}
	}

	private function parseDescription(&$description) {
		$result = '';
		preg_match_all('/<img[^>]+>/i', $description, $result);
		$img = array();
		if (isset($result[0])) {
			foreach ($result[0] as $img_tag) {
				preg_match_all('/(alt|title|src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
				if (isset($img[$img_tag][2][0])) {
					list($width, $height) = getimagesize(str_replace('"', '', $img[$img_tag][2][0]));
					if ($width >= 40 && $height >= 40) {
						$description = str_replace($img_tag, "", $description);
						return str_replace('"', '', $img[$img_tag][2][0]);
					}
				};
			}
		}
	}

	private function catch_that_image(&$des) {
		$matches = '';
		preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $des, $matches);
		$first_img = @$matches[1][0];

		if (empty($first_img)) {
			return '';
		}
		return $first_img;
	}

	public function removenewsAction() {
		$this -> _helper -> layout -> setLayout('default-simple');

		if ($this -> _getParam('cat'))
			$this -> view -> cat = $cat = $this -> _getParam('cat');

		if ($this -> getRequest() -> isPost()) {

			$categories = $this -> _getParam('cat');
			if ($categories != "") {
				$categories = str_replace(array("(", ")", "on"), "", $categories);
				$category_arr = array_filter(explode(",", $categories));
				foreach ($category_arr as $key => $value) {
					$category = Engine_Api::_() -> getItem('ultimatenews_category', $value);
					$category -> removeNews();
				}
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Remove news successful.')));
			}
		}
	}

	public function getdataAction() {
		try {
			set_time_limit(0);
			$categories = $this -> _getParam('cat');

			//get categories to read data
			if ($categories != "") {
				$categories = str_replace(array("(", ")", "on"), "", $categories);
				$category_arr = array_filter(explode(",", $categories));
				$where = "(" . implode(",", $category_arr) . ")";
				$categories = Engine_Api::_() -> ultimatenews() -> getCategoriesById($where);
			}

			if (count($categories) > 0) {
				foreach ($categories as $category) {
					//get data from remote server
					try 
					{
						//get user subscribe;
                        $users = array();
                        if ($category['subscribe']) {
                            $users = Zend_Json::decode($category['subscribe']);
                        }
						$rss = Engine_Api::_() -> getDbTable('categories', 'ultimatenews') -> find($category['category_id']) -> current();

						$option = array();
						$option['uri'] = urlencode($category['url_resource']);
						$contentSetting = $category['full_content'];
						if ($contentSetting == '0')
							$option['rssfeed'] = 1;
						$url = YOUNET_NEWS_HOST . '?' . http_build_query($option, null, '&');
						$feed = Engine_Api::_() -> ultimatenews() -> getData($url);
						if ($feed) {
							$feed = Zend_Json::decode($feed);
						} else {
							continue;
						}
						// Get feed category
						$tagStr = '';
						foreach ($rss->tags()->getTagMaps() as $tagMap) {
							$tag = $tagMap -> getTag();
							if (!isset($tag -> text))
								continue;
							if ('' !== $tagStr)
								$tagStr .= ', ';
							$tagStr .= $tag -> text;
						}
						$tags = preg_split('/[,]+/', $tagStr);
						if (is_array($feed['rows']) && !empty($feed['rows'])) {
							foreach ($feed['rows'] as $entry) {
								$pubdate = time();
								if ($entry['item_pubDate'])
									$pubdate = strtotime($entry['item_pubDate']);
								else
									$entry['item_pubDate'] = date('Y-m-d H:i:s');

								$edata = array('category_id' => $category['category_id'], 'owner_type' => "user", 'owner_id' => $category['owner_id'], 'title' => $entry['item_title'], 'description' => $entry['item_description'], 'content' =>  $entry['item_content'], 'image' => $entry['item_image'], 'link_detail' => $entry['item_url_detail'], 'author' => '', 'pubDate' => $pubdate, 'pubDate_parse' => $entry['item_pubDate'], 'posted_date' => date('Y-m-d H:i:s'), 'is_active' => "1");
								if ($edata['image'] == "") {
									preg_match('/src="([^"]*)"/i', $edata['description'], $matches);
									if ($matches[1]) {
										$edata['image'] = $this -> saveImg($matches[1], md5($matches[1]));
									}
								} else {
									$storage_file = $this -> saveImg($this -> getImageURL($edata['image']), md5($edata['image']));
									$edata['image'] = $storage_file -> storage_path;
									$edata['photo_id'] = $storage_file -> file_id;
								}
								//insert data to database
								$db = Engine_Api::_() -> getDbtable('contents', 'ultimatenews') -> getAdapter();
								$db -> beginTransaction();
								try {
									//check Ultimatenews exist by link
									$content = Engine_Api::_() -> ultimatenews() -> getAllContent(array('link_detail' => $edata['link_detail'], 'title' => $edata['title']));

									if (count($content) > 0) {

									} else {
										// Create content
										$table = $this -> _helper -> api() -> getDbtable('contents', 'ultimatenews');
										$content = $table -> createRow();
										$content -> setFromArray($edata);
										$content -> save();

										// add tags
										$owner = Engine_Api::_() -> getItem('user', $category['owner_id']);
										$content -> tags() -> setTagMaps($owner, $tags);

										//set auth
										$auth = Engine_Api::_() -> authorization() -> context;
										$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
										$auth_view = "everyone";
										$viewMax = array_search($auth_view, $roles);
										foreach ($roles as $i => $role)
											$auth -> setAllowed($content, $role, 'view', ($i <= $viewMax));
									}

									$db -> commit();
									$edata = null;

									if (is_object($content)) {
										//add activity feed on user wall
										foreach ($users as $user_id) {
											$user = Engine_Api::_() -> user() -> getUser($user_id);
											if (Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $user, 'subscribe')) {
												$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $rss, 'subscribe_new_new');

												if ($action != null) {
													Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $content);
												}
											}
										}
									}
								} catch (Exception $e) {
									$db -> rollBack();
								}
							}
						}
					} catch (exception $ex) {
						print_r($ex);
						exit ;
						$this -> getAtomFeed($category['url_resource'], $category['category_id'], Engine_Api::_() -> user() -> getViewer() -> getIdentity());
					}
				}
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => false, 'format' => 'smoothbox', 'messages' => array('Get data successful.')));
		} catch (exception $ex) {
			$this -> _forward('failed', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => false, 'format' => 'smoothbox', 'messages' => array('Get data fail.')));
			throw $ex;
		}
	}

	public function activerssAction() {
		if ($this -> getRequest() -> isPost()) {
			$value = $this -> getRequest() -> getPost();
			$categories_id = explode(',', $value['categories_active']);
			$table = Engine_Api::_() -> getDbTable('categories', 'ultimatenews');
			$content = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');
			try {
				foreach ($categories_id as $category_id) 
				{
					if (is_numeric($category_id)) 
					{
						$category = Engine_Api::_() -> getItem('ultimatenews_category', $category_id);
						if(!$category || ($category && $category -> approved != 1))
						{
							continue;
						}
						$where = $table -> getAdapter() -> quoteInto('category_id = ?', $category_id);
						$table -> update(array('is_active' => $value['is_active_name']), $where);

						$where_content = $content -> getAdapter() -> quoteInto('category_id = ?', $category_id);
						$content -> update(array('is_active' => $value['is_active_name']), $where_content);
						$categoriesparent_id = $category -> category_parent_id;
						$inactive = false;
						if ($categoriesparent_id > 0) {
							$inactive = Engine_Api::_() -> ultimatenews() -> checkcategoriesparentinactive($categoriesparent_id);

							if ($inactive == true) {
								$where = $table -> getAdapter() -> quoteInto('category_id = ?', $category_id);
								$table -> update(array('is_active' => 0), $where);
							}
						}
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 0;
				$_SESSION['result'] = 0;
			}
			$this -> view -> result = 2;
		}
		$_SESSION['result'] = $this -> view -> result;
		$this -> _helper -> redirector -> gotoRoute(array('module' => 'ultimatenews', 'controller' => 'manage', 'action' => 'feed'), 'admin_default', true);
	}
	
	public function approveRssAction() 
	{
		if ($this -> getRequest() -> isPost()) 
		{
			$value = $this -> getRequest() -> getPost();
			$categories_id = explode(',', $value['categories_approve']);
			$table = Engine_Api::_() -> getDbTable('categories', 'ultimatenews');
			try 
			{
				foreach ($categories_id as $category_id) 
				{
					if (is_numeric($category_id)) 
					{
						$where = $table -> getAdapter() -> quoteInto('category_id = ? AND approved = 0', $category_id);
						$table -> update(array('approved' => $value['is_approve_name']), $where);
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 0;
				$_SESSION['result'] = 0;
			}
			$this -> view -> result = 2;
		}
		$_SESSION['result'] = $this -> view -> result;
		$this -> _helper -> redirector -> gotoRoute(array('action' => 'feed'));
	}
	
	public function caactiverssAction() {
		if ($this -> getRequest() -> isPost()) {
			$value = $this -> getRequest() -> getPost();

			$categories_id = explode(',', $value['categories_active']);
			$table = Engine_Api::_() -> getDbTable('categoryparents', 'ultimatenews');
			$category = Engine_Api::_() -> getDbTable('categories', 'ultimatenews');
			$content = Engine_Api::_() -> getDbTable('contents', 'ultimatenews');
			try {
				foreach ($categories_id as $category_id) {
					if (is_numeric($category_id)) {
						$where = $table -> getAdapter() -> quoteInto('category_id = ?', $category_id);
						$table -> update(array('is_active' => $value['is_active_name']), $where);

						$where_category = $category -> getAdapter() -> quoteInto('category_parent_id = ?', $category_id);
						$category -> update(array('is_active' => $value['is_active_name']), $where_category);

						$categories = Engine_Api::_() -> ultimatenews() -> getAllCategories(array('category_parent' => $category_id, ));
						foreach ($categories as $categoryitem) {
							$where_content = $content -> getAdapter() -> quoteInto('category_id = ?', $categoryitem['category_id']);
							$content -> update(array('is_active' => $value['is_active_name']), $where_content);
						}
					}
				}
			} catch (Exception $e) {
				$this -> view -> result = 0;
				$_SESSION['result'] = 0;
			}
			$this -> view -> result = 2;
		}

		$_SESSION['result'] = $this -> view -> result;
		$this -> _redirect("admin/ultimatenews/manage/categories", array('result' => $this -> view -> result));
	}

	public function featuredAction() {
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

		$_SESSION['result'] = $this -> view -> result;
		if ($this -> getRequest() -> page > 1 && !empty($this -> getRequest() -> page))
			$this -> _redirect("admin/ultimatenews/manage/index/page/" . $this -> getRequest() -> page . "?result=2");
		else
			$this -> _redirect("admin/ultimatenews/manage?result=2");
	}

	public function approveAction() 
	{
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

		$_SESSION['result'] = $this -> view -> result;
		if ($this -> getRequest() -> page > 1 && !empty($this -> getRequest() -> page))
			$this -> _redirect("admin/ultimatenews/manage/index/page/" . $this -> getRequest() -> page . "?result=2");
		else
			$this -> _redirect("admin/ultimatenews/manage?result=2");
	}

	public function settingAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ultimatenews_admin_main', array(), 'ultimatenews_admin_main_timeframe');

		$this -> view -> form = $form = new Ultimatenews_Form_Admin_Manage_Setting();

		$timeFrame = Engine_Api::_() -> ultimatenews() -> getTimeframe();

		// Posting form
		if ($this -> getRequest() -> isPost()) {
			if ($form -> isValid($this -> getRequest() -> getPost())) {
				$data_array = $form -> getValues();

				$data = array();
				$data['minutes'] = $data_array['minutes'];
				$data['hour'] = $data_array['hour'];
				$data['month'] = $data_array['month'];
				$data['day'] = $data_array['day'];
				$data['weekday'] = $data_array['weekday'];

				$table = Engine_Api::_() -> getDbTable('timeframe', 'ultimatenews');
				$where = $table -> getAdapter() -> quoteInto('timeframe_id = ?', $data_array['id']);
				$table -> update($data, $where);

				$this -> view -> mess = "Set timeframe successful!";
			}
		} else {
			foreach ($form->getElements() as $name => $element) {
				if (isset($timeFrame[0][$name])) {
					$element -> setValue($timeFrame[0][$name]);
				} elseif ($name == "id") {
					$element -> setValue($timeFrame[0]['timeframe_id']);
				}
			}
			$this -> view -> mess = "";
		}
	}

	public function deleteAction() {
		// In smoothbox

		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> category_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				$group = Engine_Api::_() -> getItem('categories', $id);
				$group -> delete();
				$db -> commit();
			} catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		// Output
		$this -> renderScript('admin-manage/delete.tpl');
	}

	public function getImageURL($url) {
		if (strpos($url, '-/h') > 0) {
			$type = substr($url, strrpos($url, '.'));
			$image_url = substr($url, strpos($url, '-/h') + 2, strrpos($url, '.') - (strpos($url, '-/h') + 2)) . $type;
			$image_url = str_replace("%3A", ":", $image_url);
			return $image_url;
		} else {
			return $url;
		}
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

	public function createNewsAction() {
		set_time_limit(0);
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ultimatenews_admin_main', array(), 'ultimatenews_admin_main_create_news');
		$this -> view -> form = $form = new Ultimatenews_Form_Create();
		$form -> removeElement('submit');
		$form -> addElement('Button', 'submit', array('label' => 'Save', 'type' => 'submit', 'decorators' => array('ViewHelper')));

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

		$postArr = $this -> getRequest() -> getPost();

		if (!$form -> isValid($postArr)) {
			$form -> categoryparent -> setValue($postArr['categoryparent']);
			$form -> category -> setValue($postArr['category']);
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
		$body = $values['content'];
		$body = preg_replace('#<script[^>]*>.*?</script>#is', '', $body);
		$body = preg_replace('#<style[^>]*>.*?</style>#is','',$body);
		$body = preg_replace('#{[^}]*}#is','',$body);
		
		//Be prepare for adding news
		$edata = array('category_id' => $values['category'], 'owner_type' => "user", 'owner_id' => Engine_Api::_() -> user() -> getViewer() -> getIdentity(), 'title' => $values['title'],
		'content' => $body,
		'pubDate' => time(), 'pubDate_parse' => date('Y-m-d H:i:s'), 'posted_date' => date('Y-m-d H:i:s'), 'is_active' => $values['is_active']);
		// Check auto approved
		if(Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', Engine_Api::_() -> user() -> getViewer(), 'approve_news'))
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

}
?>