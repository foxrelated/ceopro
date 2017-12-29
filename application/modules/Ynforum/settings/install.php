<?php

/**
 * @category   Application_Extensions
 * @package    Ynforum
 */
class Ynforum_Installer extends Engine_Package_Installer_Module
{
	public function onInstall()
	{
		parent::onInstall();

		$this -> _addUserProfileContent();
		$this -> _addForumIndexPage();
		$this -> _addForumViewPage();
		$this -> _addForumDetailPage();
        $this -> _addForumOwnerId();
		
		$this -> _addGenericPage('ynforum_forum_topic-create', 'Post Topic', 'Advanced Forum Topic Create Page', 'This is the forum topic create page.');

		//Disable SE Forum
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'forum';");

		$this -> _synchronizeStatisticData();
		
		// add some icons default
		// check version
		$select = "SELECT * FROM engine4_core_modules WHERE name = 'ynforum'";
    	$module = $db->fetchRow($select);
		if(strcmp('4.04p1', $module['version']) == 0)
		{
			$db -> query("CREATE TABLE IF NOT EXISTS `engine4_ynforum_icons` (
				  `icon_id` int(11) NOT NULL AUTO_INCREMENT,
				  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
				  `photo_id` int(11) DEFAULT NULL,
				  `creation_date` datetime NOT NULL,
				  `modified_date` datetime NOT NULL,
				  PRIMARY KEY (`icon_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;");
			for($index = 1; $index <= 28; $index ++)
			{
				// Insert icon
				$db -> insert('engine4_ynforum_icons', array(
					'title' => 'Icon '.$index,
					'creation_date' => date('Y-m-d H:i:s'),
					'modified_date' => date('Y-m-d H:i:s'),
				));
				$icon_id = $db -> lastInsertId();
				
				// Insert file
				$db -> insert('engine4_storage_files', array(
					'parent_id' => $icon_id,
					'parent_type' => 'ynforum_icon',
					'user_id' => 1,
					'creation_date' => date('Y-m-d H:i:s'),
					'modified_date' => date('Y-m-d H:i:s'),
					'service_id' => 1,
					'storage_path' => 'application/modules/Ynforum/externals/images/icons/'.$index.'.png',
					'extension' => 'png',
					'name' => 'm_'.$index.'.png',
					'mime_major' => 'image',
					'mime_minor' => 'png',
				));
				$file_id = $db -> lastInsertId();
				
				$db -> query("UPDATE engine4_ynforum_icons SET `photo_id` = {$file_id} WHERE `icon_id` = {$icon_id}");
			}
		}
	}

    protected function _addForumOwnerId()
    {
        $db = $this -> getDb();
        $superAdminId = $db->fetchRow('SELECT `user_id` FROM  `engine4_users` WHERE  `level_id` = 1 LIMIT 1');
        if (isset($superAdminId['user_id'])) {
            $superAdminId = $superAdminId['user_id'];
        }
        if ($superAdminId) {
            $db->query('UPDATE  `engine4_forum_categories` SET  `owner_id` =  ' . $superAdminId . ' WHERE  `owner_id` IS NULL;');
        }
    }

	private function _synchronizeStatisticData()
	{
		$this -> _updateForumCountForCategory();
		$this -> _updateApprovedPostCountForTopic();
		$this -> _updateApprovedPostCountForForum();
		$this -> _updateApprovedTopicCountForForum();
		$this -> _updateApprovedPostCountForUser();

		$this -> _updateLastPostForYnForums();
		$this -> _updateLastPostForYnTopics();
	}

	public function onEnable()
	{
		parent::onEnable();

		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'forum';");

		$this -> _synchronizeStatisticData();
	}

	public function onDisable()
	{
		parent::onDisable();

		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 1 WHERE `engine4_core_modules`.`name` = 'forum';");

		$this -> _updateLastPostForSEForums();
		$this -> _updateLastPostForSETopics();
	}

	protected function _updateLastPostForSEForums()
	{
		$db = $this -> getDb();
		$query = "UPDATE `engine4_forum_forums` AS `forums` " . "SET `forums`.`lastpost_id` = " . "(SELECT `posts1`.`post_id` FROM `engine4_forum_posts` AS `posts1` WHERE `posts1`.`forum_id` = `forums`.`forum_id` ORDER BY `posts1`.`post_id` DESC LIMIT 0, 1 ), " . "`forums`.`lastposter_id` = " . "(SELECT `posts2`.`user_id` FROM `engine4_forum_posts` AS `posts2` WHERE `posts2`.`forum_id` = `forums`.`forum_id` ORDER BY `posts2`.`post_id` DESC LIMIT 0, 1);";
		$db -> query($query);
	}

	protected function _updateLastPostForSETopics()
	{
		$db = $this -> getDb();
		$query = "UPDATE `engine4_forum_topics` AS `topics` " . "SET `topics`.`lastpost_id` = " . "(SELECT `posts1`.`post_id` FROM `engine4_forum_posts` AS `posts1` WHERE `posts1`.`topic_id` = `topics`.`topic_id` ORDER BY `posts1`.`post_id` DESC LIMIT 0, 1 ), " . "`topics`.`lastposter_id` = " . "(SELECT `posts2`.`user_id` FROM `engine4_forum_posts` AS `posts2` WHERE `posts2`.`topic_id` = `topics`.`topic_id` ORDER BY `posts2`.`post_id` DESC LIMIT 0, 1);";
		$db -> query($query);
	}

	protected function _updateLastPostForYnForums()
	{
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_forum_forums` AS `forums` " . "SET `forums`.`lastpost_id` = " . "(SELECT `posts1`.`post_id` FROM `engine4_forum_posts` AS `posts1` WHERE `posts1`.`forum_id` = `forums`.`forum_id` AND `posts1`.`approved` = 1 ORDER BY `posts1`.`post_id` DESC LIMIT 0, 1 ), " . "`forums`.`lastposter_id` = " . "(SELECT `posts2`.`user_id` FROM `engine4_forum_posts` AS `posts2` WHERE `posts2`.`forum_id` = `forums`.`forum_id` AND `posts2`.`approved` = 1 ORDER BY `posts2`.`post_id` DESC LIMIT 0, 1);");
	}

	protected function _updateLastPostForYnTopics()
	{
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_forum_topics` AS `topics` " . "SET `topics`.`lastpost_id` = " . "(SELECT `posts1`.`post_id` FROM `engine4_forum_posts` AS `posts1` WHERE `posts1`.`topic_id` = `topics`.`topic_id` AND `posts1`.`approved` = 1 ORDER BY `posts1`.`post_id` DESC LIMIT 0, 1 ), " . "`topics`.`lastposter_id` = " . "(SELECT `posts2`.`user_id` FROM `engine4_forum_posts` AS `posts2` WHERE `posts2`.`topic_id` = `topics`.`topic_id` AND `posts2`.`approved` = 1 ORDER BY `posts2`.`post_id` DESC LIMIT 0, 1);");
	}

	protected function _updateApprovedPostCountForUser()
	{
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_forum_signatures` AS `signatures` " . "SET `signatures`.`approved_post_count`= " . "(SELECT count(*) FROM `engine4_forum_posts` AS `posts` WHERE `posts`.`user_id` = `signatures`.`user_id` AND `posts`.`approved` = 1);");

		$db -> query("UPDATE `engine4_forum_signatures` AS `signatures` " . "SET `signatures`.`post_count`= " . "(SELECT count(*) FROM `engine4_forum_posts` AS `posts` WHERE `posts`.`user_id` = `signatures`.`user_id`);");
	}

	protected function _updateForumCountForCategory()
	{
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_forum_categories` AS `categories` " . "SET `categories`.`forum_count`= " . "(SELECT count(*) FROM `engine4_forum_forums` AS `forums` WHERE `forums`.`category_id` = `categories`.`category_id`);");
	}

	protected function _updateApprovedTopicCountForForum()
	{
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_forum_forums` AS `forums` " . "SET `forums`.`approved_topic_count`= " . "(SELECT count(*) FROM `engine4_forum_topics` AS `topics` WHERE `topics`.`forum_id` = `forums`.`forum_id` AND `topics`.`approved` = 1);");

		$db -> query("UPDATE `engine4_forum_forums` AS `forums` " . "SET `forums`.`topic_count`= " . "(SELECT count(*) FROM `engine4_forum_topics` AS `topics` WHERE `topics`.`forum_id` = `forums`.`forum_id`);");
	}

	protected function _updateApprovedPostCountForForum()
	{
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_forum_forums` AS `forums` " . "SET `forums`.`approved_post_count`= " . "(SELECT count(*) FROM `engine4_forum_posts` AS `posts` WHERE `posts`.`forum_id` = `forums`.`forum_id` AND `posts`.`approved` = 1);");

		$db -> query("UPDATE `engine4_forum_forums` AS `forums` " . "SET `post_count`= " . "(SELECT count(*) FROM `engine4_forum_posts` AS `posts` WHERE `posts`.`forum_id` = `forums`.`forum_id`);");
	}

	protected function _updateApprovedPostCountForTopic()
	{
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_forum_topics` AS `topics` " . "SET `topics`.`approved_post_count`= " . "(SELECT count(*) FROM `engine4_forum_posts` AS `posts` WHERE `posts`.`topic_id` = `topics`.`topic_id` AND `posts`.`approved` = 1);");

		$db -> query("UPDATE `engine4_forum_topics` AS `topics` " . "SET `topics`.`post_count`= " . "(SELECT count(*) FROM `engine4_forum_posts` AS `posts` WHERE `posts`.`topic_id` = `topics`.`topic_id`);");
	}

	protected function _addForumIndexPage()
	{
		$db = $this -> getDb();

		// check page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynforum_index_index') -> limit(1) -> query() -> fetchColumn();		
		
		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynforum_index_index',
				'displayname' => 'Advanced Forum Main Page',
				'title' => 'Advanced Forum Main',
				'description' => 'This is the main advanced forum page.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'params' => '["[]"]',
			));
			$main_id = $db -> lastInsertId();

			// Insert middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'params' => '["[]"]',
			));
			$middle_id = $db -> lastInsertId();

			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $middle_id,
				'params' => '["[]"]',
				'order' => 1,
			));
			
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.list-statistic-top-users',
				'page_id' => $page_id,
				'parent_content_id' => $middle_id,
				'params' => '{"title":"","name":"ynforum.list-statistic-top-users"}',
				'order' => 2,
			));

			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'page_id' => $page_id,
				'parent_content_id' => $middle_id,
				'params' => '{"max":6}',
				'order' => 3,
			));
			$tab_container_id = $db -> lastInsertId();

			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.list-hottest-topics',
				'page_id' => $page_id,
				'parent_content_id' => $tab_container_id,
				'params' => '{"title":"Hottest Topics","name":"ynforum.list-hottest-topics"}',
				'order' => 1,
			));

			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.list-most-viewed-topics',
				'page_id' => $page_id,
				'parent_content_id' => $tab_container_id,
				'params' => '{"title":"Most Viewed Topics"}',
				'order' => 2,
			));

			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.list-newest-topics',
				'page_id' => $page_id,
				'parent_content_id' => $tab_container_id,
				'params' => '{"title":"Newest Topics"}',
				'order' => 3,
			));
			
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.statistics',
				'page_id' => $page_id,
				'parent_content_id' => $middle_id,
				'params' => '{}',
				'order' => 4,
			));
		}
		else 
		{
			$query = "UPDATE `engine4_core_content` SET `order` = 1 WHERE `name` = 'core.content' AND `page_id` = {$page_id}";
			$db -> query($query);
			$query = "UPDATE `engine4_core_content` SET `order` = 2 WHERE `name` = 'ynforum.list-statistic-top-users' AND `page_id` = {$page_id}";
			$db -> query($query);
			$query = "UPDATE `engine4_core_content` SET `order` = 3 WHERE `name` = 'core.container-tabs' AND `page_id` = {$page_id}";
			$db -> query($query);
		}
		//check widget mywatch-topic
		$tab_container_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('name = ?', 'ynforum.list-most-viewed-topics') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
		$content_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynforum.list-newest-topics') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
		if(!$content_id)
		{
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.list-newest-topics',
				'page_id' => $page_id,
				'parent_content_id' => $tab_container_id,
				'params' => '{"title":"Newest Topics"}',
				'order' => 3,
			));
		}
		$content_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynforum.list-mywatch-topics') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
		if(!$content_id)
		{
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.list-mywatch-topics',
				'page_id' => $page_id,
				'parent_content_id' => $tab_container_id,
				'params' => '{"title":"My Watch Topics"}',
				'order' => 4,
			));
		}
		$middle_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('name = ?', 'ynforum.list-statistic-top-users') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
		$content_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynforum.statistics') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
		if(!$content_id)
		{
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynforum.statistics',
				'page_id' => $page_id,
				'parent_content_id' => $middle_id,
				'params' => '{}',
				'order' => 4,
			));
		}
	}

	protected function _addForumViewPage()
	{
		$db = $this -> getDb();
		// check page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynforum_forum_view') -> limit(1) -> query() -> fetchColumn();
		if($page_id)
		{
			$content_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynforum.profile-header') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			if(!$content_id)
			{
				//remove page
				$db -> query("DELETE FROM `engine4_core_pages` where `engine4_core_pages`.`page_id` = " . $page_id);
				$page_id = 0;
			}
		}
		if(!$page_id)
		{	
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynforum_forum_view',
				'displayname' => 'Advanced Forum View Page',
				'title' => 'Advanced Forum View',
				'description' => 'This is the view advanced forum page.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();
			// Top container
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'container', 
				'name' => 'top', 
				'parent_content_id' => null, 
				'order' => 1, 
				'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'container', 
				'name' => 'middle', 
				'parent_content_id' => $top_id, 
				'order' => 1, 
				'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'widget', 
				'name' => 'ynforum.profile-header', 
				'parent_content_id' => $middle_id, 
				'order' => 1, 
				'params' => '', ));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'widget', 
				'name' => 'ynforum.profile-announcements', 
				'parent_content_id' => $middle_id, 
				'order' => 2, 
				'params' => '', ));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'widget', 
				'name' => 'ynforum.profile-polls', 
				'parent_content_id' => $middle_id, 
				'order' => 3, 
				'params' => '', ));
			
			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'order' => 2, 
				'page_id' => $page_id,
			));
			$main_id = $db -> lastInsertId();
			
			// Main-Right container
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'container', 
				'name' => 'right', 
				'parent_content_id' => $main_id, 
				'order' => 1, 
				'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			

			// Insert middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2, 
			));
			$middle_id = $db -> lastInsertId();

			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $middle_id,
			));
			
			//Insert Right
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'widget', 
				'name' => 'ynforum.profile-events', 
				'parent_content_id' => $right_id, 
				'order' => 1, 
				'params' => '{"title":"Forum\'s Events"}', ));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id, 
				'type' => 'widget', 
				'name' => 'ynforum.profile-groups', 
				'parent_content_id' => $right_id, 
				'order' => 2, 
				'params' => '{"title":"Forum\'s Groups"}', ));
		}
	}

	protected function _addForumDetailPage()
	{
		$db = $this -> getDb();

		// check page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynforum_topic_view') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynforum_topic_view',
				'displayname' => 'Advanced Forum Detail Topic Page',
				'title' => 'Advanced Forum Detail Topic',
				'description' => 'This is the view topic advanced forum page.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
			));
			$main_id = $db -> lastInsertId();

			// Insert middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$middle_id = $db -> lastInsertId();

			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $middle_id,
			));
		}
	}

	protected function _addUserProfileContent()
	{
		//
		// install content areas
		//
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// VALUES
		// profile page
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$page_id = $select -> query() -> fetchObject() -> page_id;

		// forum.profile-forum-posts
		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'forum.profile-forum-posts');
		$info = $select -> query() -> fetch();

		if (empty($info))
		{

			// container_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
			$container_id = $select -> query() -> fetchObject() -> content_id;

			// middle_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
			$middle_id = $select -> query() -> fetchObject() -> content_id;

			// tab_id (tab container) may not always be there
			$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
			$tab_id = $select -> query() -> fetchObject();
			if ($tab_id && @$tab_id -> content_id)
			{
				$tab_id = $tab_id -> content_id;
			}
			else
			{
				$tab_id = null;
			}

			// tab on profile
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'forum.profile-forum-posts',
				'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
				'order' => 9,
				'params' => '{"title":"Forum Posts","titleCount":true}',
			));
		}
	}

}
