<?php
//Get parser setting
$parser = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ultimate.parser', 1);
$serviceLink = ($parser) ? "http://newsservice.younetco.com/v1.1" : Engine_Api::_() -> ultimatenews() -> getCurrentHost() . "application/modules/Ultimatenews/Api/newsservice";

//Definde Constants
defined('YOUNET_NEWS_FEED_PARSER') or define('YOUNET_NEWS_FEED_PARSER', "{$serviceLink}/getfeed.php");
defined('YOUNET_NEWS_HOST') or define('YOUNET_NEWS_HOST', "{$serviceLink}/parser.php");
class Ultimatenews_ManageController extends Core_Controller_Action_Standard 
{
	public function editAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_feed')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
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

	public function getdataAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_news')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
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
						$users = Zend_Json::decode($category['subscribe']);
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

								$edata = array('category_id' => $category['category_id'], 'owner_type' => "user", 'owner_id' => $category['owner_id'], 'title' => $entry['item_title'], 'description' => $entry['item_description'], 'content' => $entry['item_content'], 'image' => $entry['item_image'], 'link_detail' => $entry['item_url_detail'], 'author' => '', 'pubDate' => $pubdate, 'pubDate_parse' => $entry['item_pubDate'], 'posted_date' => date('Y-m-d H:i:s'), 'is_active' => "1");
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

	public function activerssAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'create_feed')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
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
		if($this -> _getParam('actionBack', ''))
		{
			$this -> _helper -> redirector -> gotoRoute(array('action' => $this -> _getParam('actionBack')), 'ultimatenews_general', true);
		}
		else {
			$this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-feed'), 'ultimatenews_general', true);
		}
	}
	public function approveRssAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'manage_feed')) 
		{
			return $this -> _helper -> requireAuth -> forward();
		}
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
		$this -> _helper -> redirector -> gotoRoute(array('action' => 'manage-feed'), 'ultimatenews_general', true);
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
}
