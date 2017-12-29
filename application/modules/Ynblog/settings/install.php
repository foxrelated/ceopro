<?php
class Ynblog_Installer extends Engine_Package_Installer_Module {
	public function onEnable() {
		parent::onEnable();
		$db = $this->getDb();
		$db->query("UPDATE `engine4_core_modules` SET `enabled`=0 WHERE  `name`='blog';");

	}

	public function onDisable() {
		parent::onDisable();
		$db = $this->getDb();
		$db->query("UPDATE `engine4_core_modules` SET `enabled`=1 WHERE  `name`='blog';");
	}

	function onInstall() {

		parent::onInstall();

		$this -> _addPhotoIdColumn();
		$this -> _addBlogProfile();
		$this -> _addBlogBrowsePage();
		$this -> _addBlogListingPage();
		$this -> _addBlogListPage();
		$this -> _addBlogViewPage();
		$this -> _addBlogCreatePage();
		$this -> _addBlogManagePage();
		$this -> _alterBlogsTable();
		$this -> _mergeDataLinks();
		// Query for safe
		try
		{
			$db = $this -> getDb();
			$db -> query("ALTER TABLE `engine4_blog_blogs` ADD `become_count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `comment_count`");
		}
		catch(Exception $e)
		{

		}
	}
    protected  function _addPhotoIdColumn()
    {
        $db = $this -> getDb();
        try {
            $cols = $db->describeTable('engine4_blog_blogs');
            if( !isset($cols['photo_id']) ) {
                $db->query('ALTER TABLE `engine4_blog_blogs` ' .
                    'ADD COLUMN `photo_id` int( 11 ) NOT NULL default "0" after `owner_id`');
            }
        } catch( Exception $e ) {
		}
	}

	/**
	 * Merge data from table `engine4_blogimporter_links` from old Blog Importer Blugin
	 */
	 protected function _mergeDataLinks() {
		if ($this->_hasModule('blogimporter'))
        {
            $db = $this -> getDb();
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_blogimporter_links');
			$rows = $select -> query()->fetchAll();
            if (count($rows) > 0)
            {
                foreach ($rows as $row)
                {
                    $db -> insert('engine4_blog_links',
                            array('user_id' => $row['user_id'],
                                'link_url' => $row['link_url'],
                                'last_run' => $row['last_run'],
                                'cronjob_enabled' => $row['cronjob_enabled'],
                                ));
                }
            }
        }
	}

	protected function _hasModule($name)
	{
		$db = $this-> getDb();
		$select = new Zend_Db_Select($db);
		$select->from('engine4_core_modules')->where('name = ?',$name);
		$row = $select ->query()->fetch();
		if($row)
		{
			return true;
		}
		return false;
	}
	protected function _alterBlogsTable() {
		$db = $this -> getDb();
		try {
			$db -> query('ALTER TABLE `engine4_blog_blogs` ADD `pub_date` VARCHAR( 100 ) NULL AFTER `modified_date`;');
			$db -> query("ALTER TABLE `engine4_blog_blogs` ADD COLUMN `link_detail` varchar(300) default NULL AFTER `pub_date`;");
		} catch(Exception $e) {
		}
	}

	/*----- Blog Profile Widget -----*/
	protected function _addBlogProfile() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		/*----- User Profile Page -----*/
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$page_id = $select -> query() -> fetchObject() -> page_id;

		//Check and remove SE profile blogs widget
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynblog.profile-blogs');
		$info = $select -> query() -> fetch();

		if (!empty($info)) {
			$db -> query("DELETE FROM `engine4_core_content` where `engine4_core_content`.`page_id` =" . $page_id . " and `engine4_core_content`.`name` = 'ynblog.profile-blogs'");
		}

		// Add profile blogs widget
		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynblog.profile-blogs');
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			// Get container_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
			$container_id = $select -> query() -> fetchObject() -> content_id;

			// Get middle_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
			$middle_id = $select -> query() -> fetchObject() -> content_id;

			// Get tab_id (tab container) may not always be there
			$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
			$tab_id = $select -> query() -> fetchObject();
			if ($tab_id && @$tab_id -> content_id) {
				$tab_id = $tab_id -> content_id;
			} else {
				$tab_id = null;
			}

			// Add profile blogs widget
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.profile-blogs', 'parent_content_id' => ($tab_id ? $tab_id : $middle_id), 'order' => 6, 'params' => '{"title":"Blogs","titleCount":true,"mode_grid":1,"mode_list":1,"view_mode":"list"}', ));
		}
	}

	/*------ Blog Browse Page -----*/
	protected function _addBlogBrowsePage() {
		$db = $this -> getDb();
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynblog_index_index') -> limit(1) -> query() -> fetchColumn();
		
		if($page_id)
		{
			// Check left column
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') 
				-> where('page_id = ?', $page_id) 
				-> where('type = ?', 'container') 
				-> where('name = ?', 'left');
			$info = $select -> query() -> fetch();
			if(empty($info))
			{
				// Clear this page
				$db -> query("DELETE FROM `engine4_core_pages` where `engine4_core_pages`.`page_id` =" . $page_id);
				$page_id = 0;
			}
		}
		
		// Add page if it does not exist
		if (!$page_id) 
		{
			$db -> insert('engine4_core_pages', array('name' => 'ynblog_index_index', 'displayname' => 'YN - Advanced Blog - Browse Page', 'title' => 'Blogs Browse Page', 'description' => 'This is Blogs Browse Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// Add containers
			// Top container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-menu', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '', ));

			// Main container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// Main-Right container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 2, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			
			// Main-Left container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'left', 'parent_content_id' => $container_id, 'order' => 1, 'params' => '', ));
			$left_id = $db -> lastInsertId('engine4_core_content');

			// Main-Middle containter
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 3, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			// Main-Middle Widgets

			// Featured Blogs
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.featured-blogs', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '{"title":"Featured Blogs"}', ));

			// New Blogs & Top Blogs Tab
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.container-tabs', 'parent_content_id' => $middle_id, 'order' => 2, 'params' => '{"max":"6","title":"","name":"core.container-tabs"}', ));
			$tab1_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.new-blogs', 'parent_content_id' => $tab1_id, 'order' => 3, 'params' => '{"title":"New Blogs","mode_grid":1,"mode_list":1,"view_mode":"list"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.top-blogs', 'parent_content_id' => $tab1_id, 'order' => 4, 'params' => '{"title":"Top Blogs","mode_grid":1,"mode_list":1,"view_mode":"list"}', ));

			// Most Viewed Blogs & Most Commented Blogs Tab
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.container-tabs', 'parent_content_id' => $middle_id, 'order' => 5, 'params' => '{"max":"6"}', ));
			$tab2_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.most-viewed-blogs', 'parent_content_id' => $tab2_id, 'order' => 6, 'params' => '{"title":"Most Viewed Blogs","mode_grid":1,"mode_list":1,"view_mode":"list"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.most-commented-blogs', 'parent_content_id' => $tab2_id, 'order' => 7, 'params' => '{"title":"Most Commented Blogs","mode_grid":1,"mode_list":1,"view_mode":"list"}', ));

			// Main-Left Widgets
			// Top Bloggers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.top-bloggers', 'parent_content_id' => $left_id, 'order' => 1, 'params' => '{"title":"Top Bloggers"}', ));
			
			// Blog Categories
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blog-categories', 'parent_content_id' => $left_id, 'order' => 2, 'params' => '{"title":"Categories"}', ));

			//Blog Statistics
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-statistic', 'parent_content_id' => $left_id, 'order' => 3, 'params' => '{"title":"Statistic"}', ));
			
			// Main-Right Widgets
			
			// Blog Search
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-search', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '{"title":"Blogs Search"}', ));

			// View By Date Blogs
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.view-by-date-blogs', 'parent_content_id' => $right_id, 'order' => 2, 'params' => '{"title":"View By Date"}', ));

			// Blog Tags
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-tags', 'parent_content_id' => $right_id, 'order' => 3, 'params' => '{"title":"Tags", "max":"20"}', ));
		}
	}

	/*------ Blog Browse Page -----*/
	protected function _addBlogListingPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynblog_index_listing') -> limit(1);
		$info = $select -> query() -> fetch();

		// Add page if it does not exist
		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynblog_index_listing', 'displayname' => 'YN - Advanced Blog - Listing Page', 'title' => 'Blog Listing page', 'description' => 'This is blog listing page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers

			// Top container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			//Insert Main container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));

			// Top menu
			$container_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-menu', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '', ));

			// Main - Right container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 1, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');

			// Main - Middle container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 2, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			// Middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-listing', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '{"mode_grid":1,"mode_list":1,"view_mode":"list"}', ));

			// Right column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-search', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '{"title":"Search Blogs"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blog-categories', 'parent_content_id' => $right_id, 'order' => 2, 'params' => '{"title":"Categories"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.view-by-date-blogs', 'parent_content_id' => $right_id, 'order' => 3, 'params' => '{"title":"View By Date"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-tags', 'parent_content_id' => $right_id, 'order' => 4, 'params' => '{"title":"Tags"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-statistic', 'parent_content_id' => $right_id, 'order' => 5, 'params' => '{"title":"Statistics"}', ));
		} else {
            // replace core content with blog listing
            $db -> update('engine4_core_content', array('name' => 'ynblog.blogs-listing', 'params' => '{"mode_grid":1,"mode_list":1,"view_mode":"list"}'), array('page_id = ?' => $info['page_id'], 'name = ?' => 'core.content'));
        }
	}

	/*------ User Blog List Page -----*/
	protected function _addBlogListPage() {
		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynblog_index_list') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) {
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynblog_index_list', 'displayname' => 'YN - Advanced Blog - List Page', 'title' => 'Blog List', 'description' => 'This page will lists a member\'s blog entries.', 'provides' => 'subject=user', ));
			$page_id = $db -> lastInsertId();

			//Insert top container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId();

			// Insert main container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$main_id = $db -> lastInsertId();

			// Top menu
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $top_id, 'order' => 1, ));
			$top_middle_id = $db -> lastInsertId();

			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.blogs-menu', 'page_id' => $page_id, 'parent_content_id' => $top_middle_id, 'order' => 1, ));

			/*--- Insert right container ---*/
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'right', 'page_id' => $page_id, 'parent_content_id' => $main_id, 'order' => 1, 'params' => '', ));
			$right_id = $db -> lastInsertId();

			/*--- Insert middle container ---*/
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $main_id, 'order' => 2, 'params' => '', ));
			$middle_id = $db -> lastInsertId();

			// Right column
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.owner-photo', 'page_id' => $page_id, 'parent_content_id' => $right_id, 'order' => 1, ));
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.blogs-side-menu', 'page_id' => $page_id, 'parent_content_id' => $right_id, 'order' => 2, ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-search', 'parent_content_id' => $right_id, 'order' => 3, 'params' => '{"title":"Search Blogs"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blog-categories', 'parent_content_id' => $right_id, 'order' => 4, 'params' => '{"title":"Categories"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-tags', 'parent_content_id' => $right_id, 'order' => 5, 'params' => '{"title":"User\'s Tags"}', ));

			// Insert middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-listing', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '{"mode_grid":1,"mode_list":1,"view_mode":"list"}', ));
		} else {
            // replace core content with blog listing
            $db -> update('engine4_core_content', array('name' => 'ynblog.blogs-listing', 'params' => '{"mode_grid":1,"mode_list":1,"view_mode":"list"}'), array('page_id = ?' => $page_id, 'name = ?' => 'core.content'));
        }
	}

	/*------ Specific Blog View Page -----*/
	protected function _addBlogViewPage() {
		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynblog_index_view') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) 
		{
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynblog_index_view', 'displayname' => 'YN - Advanced Blog - View Page', 'title' => 'Blog View', 'description' => 'This page displays a blog entry.', 'provides' => 'subject=blog', ));
			$page_id = $db -> lastInsertId();

			//Insert top container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId();

			// Insert main container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$main_id = $db -> lastInsertId();

			// Top menu
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $top_id, 'order' => 1, 'params' => '', ));
			$top_middle_id = $db -> lastInsertId();

			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.blogs-menu', 'page_id' => $page_id, 'parent_content_id' => $top_middle_id, 'order' => 1, ));

			// Insert right
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'right', 'page_id' => $page_id, 'parent_content_id' => $main_id, 'order' => 1, 'params' => '', ));
			$right_id = $db -> lastInsertId();

			// Insert middle
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $main_id, 'order' => 2, ));
			$middle_id = $db -> lastInsertId();

			// Insert right column
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.owner-photo', 'page_id' => $page_id, 'parent_content_id' => $right_id, 'order' => 1, ));
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.blogs-side-menu', 'page_id' => $page_id, 'parent_content_id' => $right_id, 'order' => 2, ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-search', 'parent_content_id' => $right_id, 'order' => 3, 'params' => '{"title":"Search Blogs"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blog-categories', 'parent_content_id' => $right_id, 'order' => 4, 'params' => '{"title":"Categories"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.blogs-tags', 'parent_content_id' => $right_id, 'order' => 5, 'params' => '{"title":"User\'s Tags"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.detail-other-blogs', 'parent_content_id' => $right_id, 'order' => 6, 'params' => '{"title":"Other Blogs"}', ));

			// Insert middle column
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'core.content', 'page_id' => $page_id, 'parent_content_id' => $middle_id, 'order' => 1, ));
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.detail-related-blogs', 'page_id' => $page_id, 'parent_content_id' => $middle_id, 'order' => 2, 'params' => '{"title":"Related Blogs"}',));
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'core.comments', 'page_id' => $page_id, 'parent_content_id' => $middle_id, 'order' => 3, ));
		}
		else
		{
			// Get Right + Middle
			$right_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'right') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			$main_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'main') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			$middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'middle') -> where('type = ?', 'container') -> where('parent_content_id = ?', $main_id) -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			if($right_id)
			{
				// check exists
				if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynblog.detail-other-blogs') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
					$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynblog.detail-other-blogs', 'parent_content_id' => $right_id, 'order' => 99, 'params' => '{"title":"Other Blogs"}', ));
			}
			if($middle_id)
			{
				// check exists
				if(!$db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynblog.detail-related-blogs') -> where('type = ?', 'widget') -> where('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn())
					$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.detail-related-blogs', 'page_id' => $page_id, 'parent_content_id' => $middle_id, 'order' => 99, 'params' => '{"title":"Related Blogs"}',));
			}
			
		}
	}

	/*------ Specific Blog Create Page -----*/
	protected function _addBlogCreatePage() {
		$db = $this -> getDb();
		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynblog_index_create') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) {
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynblog_index_create', 'displayname' => 'YN - Advanced Blog - Create Page', 'title' => 'Blog Create Page', 'description' => 'This page allows user to create a new blog.', 'provides' => 'subject=blog', ));
			$page_id = $db -> lastInsertId();

			//Insert top container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId();

			// Insert main container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$main_id = $db -> lastInsertId();

			// Top menu widget
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $top_id, 'order' => 1, 'params' => '', ));
			$top_middle_id = $db -> lastInsertId();

			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.blogs-menu', 'page_id' => $page_id, 'parent_content_id' => $top_middle_id, 'order' => 1, ));

			// Content widget
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $main_id, 'order' => 1, 'params' => '', ));
			$content_middle_id = $db -> lastInsertId();

			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'core.content', 'page_id' => $page_id, 'parent_content_id' => $content_middle_id, 'order' => 1, ));

		}
	}

	/*------ Specific Blog Create Page -----*/
	protected function _addBlogManagePage() {
	    $db = $this -> getDb();
	    // profile page
	    $page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynblog_index_manage') -> limit(1) -> query() -> fetchColumn();

	    // insert if it doesn't exist yet
	    if (!$page_id) {
	        // Insert page
	        $db -> insert('engine4_core_pages', array('name' => 'ynblog_index_manage', 'displayname' => 'YN - Advanced Blog - Manage Page', 'title' => 'Blog Manage Page', 'description' => 'This page lists a user\'s blog entries.', 'provides' => 'subject=blog', ));
	        $page_id = $db -> lastInsertId();

	        //Insert top container
	        $db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
	        $top_id = $db -> lastInsertId();

	        // Insert main container
	        $db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
	        $main_id = $db -> lastInsertId();

	        // Top menu widget
	        $db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $top_id, 'order' => 1, 'params' => '', ));
	        $top_middle_id = $db -> lastInsertId();

	        $db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynblog.blogs-menu', 'page_id' => $page_id, 'parent_content_id' => $top_middle_id, 'order' => 1, ));

	        // Content widget
	        $db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $main_id, 'order' => 1, 'params' => '', ));
	        $content_middle_id = $db -> lastInsertId();

	        $db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'core.content', 'page_id' => $page_id, 'parent_content_id' => $content_middle_id, 'order' => 1, ));

	    }
	}

}
?>