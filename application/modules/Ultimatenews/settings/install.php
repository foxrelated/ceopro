<?php
class Ultimatenews_Package_Installer extends Engine_Package_Installer_Module
{
    public function onInstall()
    {
    	$db = $this->getDb();
	    //News  home
	    $query = "ALTER TABLE `engine4_authorization_permissions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL";
	    $db->query($query);
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ultimatenews_index_list')
	      ->limit(1);
	      ;
	    $page_id = $select->query()->fetchColumn();
	
	    if(!$page_id) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ultimatenews_index_list',
	        'displayname' => 'Ultimate News Home Page',
	        'title' => 'Ultimate News Home Page',
	        'description' => 'This is Ultimate News home page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');  
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.menu-ultimatenews',
	        'parent_content_id' => $middle_id,
	        'order' => 3,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'left',
	        'parent_content_id' => $container_id,
	        'order' => 4,
	        'params' => '',
	      ));
	      $left_id = $db->lastInsertId('engine4_core_content');
	      
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'right',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $right_id = $db->lastInsertId('engine4_core_content');
			// Middle column
			$db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.featured-ultimatenews',
	        'parent_content_id' => $middle_id,
	        'order' => 4,
	        'params' => '{"title":"Featured News"}',
	        ));
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.list-ultimatenews',
	        'parent_content_id' => $middle_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      // left column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.categories-ultimatenews',
	        'parent_content_id' => $left_id,
	        'order' => 1,
	        'params' => '{"title":"Categories"}',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.lasted-ultimatenews',
	        'parent_content_id' => $left_id,
	        'order' => 2,
	        'params' => '{"title":"Recent News","nomobile":"1"}',
	      ));
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.most-commented-ultimatenews',
	        'parent_content_id' => $left_id,
	        'order' => 3,
	        'params' => '{"title":"Most Commented News","nomobile":"1"}',
	      ));
	      // right column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.search-ultimatenews',
	        'parent_content_id' => $right_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.top-ultimatenews',
	        'parent_content_id' => $right_id,
	        'order' => 3,
	        'params' => '{"title":"Top News"}',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.most-liked-ultimatenews',
	        'parent_content_id' => $right_id,
	        'order' => 4,
	        'params' => '{"title":"Most Liked News"}',
	      ));
	    }
		else {
			$tab_container_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('name = ?', 'ultimatenews.search-ultimatenews') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			$content_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ultimatenews.tag-news') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			if(!$content_id)
			{
				$db->insert('engine4_core_content', array(
		        'page_id' => $page_id,
		        'type' => 'widget',
		        'name' => 'ultimatenews.tag-news',
		        'parent_content_id' => $tab_container_id,
		        'order' => 2,
		        'params' => '{"title":"Tags"}',
		      ));
			}
		}
	    //News detail
	     $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ultimatenews_index_detail')
	      ->limit(1);
	      ;
	    $page_id = $select->query()->fetchColumn();
	
	     if(!$page_id) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ultimatenews_index_detail',
	        'displayname' => 'Ultimate News Detail Page',
	        'title' => 'Ultimate News Detail Page',
	        'description' => 'This is Ultimate News Detail Page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
		  
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');  
		  
		  
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.menu-ultimatenews',
	        'parent_content_id' => $middle_id,
	        'order' => 3,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
		  
		   $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'left',
	        'parent_content_id' => $container_id,
	        'order' => 4,
	        'params' => '',
	      ));
	      $left_id = $db->lastInsertId('engine4_core_content');

	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.article-detail',
	        'parent_content_id' => $middle_id,
	        'order' => 3,
	        'params' => '',
	      ));
		  //left
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.categories-ultimatenews',
	        'parent_content_id' => $left_id,
	        'order' => 1,
	        'params' => '{"title":"Categories"}',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ultimatenews.lasted-ultimatenews',
	        'parent_content_id' => $left_id,
	        'order' => 2,
            'params' => '{"title":"Recent News","nomobile":"1"}',
	      ));
	    }
    	else {
			$tab_container_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('name = ?', 'ultimatenews.categories-ultimatenews') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			$content_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ultimatenews.tag-news') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			if(!$content_id)
			{
				$db->insert('engine4_core_content', array(
		        'page_id' => $page_id,
		        'type' => 'widget',
		        'name' => 'ultimatenews.tag-news',
		        'parent_content_id' => $tab_container_id,
		        'order' => 2,
		        'params' => '{"title":"Tags"}',
		      ));
			}
		}

		// My RSS Subscription
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_your-subscribe')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_your-subscribe',
				'displayname' => 'Ultimate News My RSS Subscription Page',
				'title' => 'Ultimate News My RSS Subscription Page',
				'description' => 'This is Ultimate News My RSS Subscription Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
		//My Favourite News
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_favorite')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_favorite',
				'displayname' => 'Ultimate News My Favourite News Page',
				'title' => 'Ultimate News My Favourite News Page',
				'description' => 'This is Ultimate News My Favourite News Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
		//Manage Feeds
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_manage-feed')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_manage-feed',
				'displayname' => 'Ultimate News Manage Feeds Page',
				'title' => 'Ultimate News Manage Feeds Page',
				'description' => 'This is Ultimate News Manage Feeds Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
		//My Feeds
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_my-feed')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_my-feed',
				'displayname' => 'Ultimate News My Feeds Page',
				'title' => 'Ultimate News My Feeds Page',
				'description' => 'This is Ultimate News My Feeds Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
		// Add feeds
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_create-feed')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_create-feed',
				'displayname' => 'Ultimate News Add Feeds Page',
				'title' => 'Ultimate News Add Feeds Page',
				'description' => 'This is Ultimate News Add Feeds Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}

		//Manage News
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_manage')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_manage',
				'displayname' => 'Ultimate News Manage News Page',
				'title' => 'Ultimate News Manage News Page',
				'description' => 'This is Ultimate News Manage News Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
        //My News
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_my-news')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_my-news',
				'displayname' => 'Ultimate News My News Page',
				'title' => 'Ultimate News My News Page',
				'description' => 'This is Ultimate News My News Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}

		//Add News
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_create-news')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_create-news',
				'displayname' => 'Ultimate News Create News Page',
				'title' => 'Ultimate News Create News Page',
				'description' => 'This is Ultimate News Create News Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
		//Feed Detail
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_pages', 'page_id')
			->where('name = ?', 'ultimatenews_index_feed')
			->limit(1);
		;
		$page_id = $select->query()->fetchColumn();

		if(!$page_id)
		{
			$db->insert('engine4_core_pages', array(
				'name' => 'ultimatenews_index_feed',
				'displayname' => 'Ultimate News Feed Detail Page',
				'title' => 'Ultimate News Feed Detail Page',
				'description' => 'This is Ultimate News Feed Detail Page.',
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');
			// Insert top-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
			));

			$main_id = $db->lastInsertId('engine4_core_content');
			// Insert main-middle
			$db->insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();
			//Insert Menu
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ultimatenews.menu-ultimatenews',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
				'params' => '',
			));
			//Insert content
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
		parent::onInstall();
        $this -> _ynBuildStructure();
    }

    /**
     * rebuild structure from structure file
     * structure file is builded from rip export
     * @return void
     */
    protected function _ynBuildStructure()
    {
        $filename = dirname(__FILE__) . '/structure.php';
        $structure =
        include $filename;

        if (isset($structure['module']) && !empty($structure['module']))
        {
            $this -> _ynBuildModule($structure['module']);
        }

        if (isset($structure['pages']) && !empty($structure['pages']))
        {
            $this -> _ynBuildPages($structure['pages']);
        }

        if (isset($structure['menus']) && !empty($structure['menus']))
        {
            $this -> _ynBuildMenus($structure['menus']);
        }

        if (isset($structure['menuitems']) && !empty($structure['menuitems']))
        {
            $this -> _ynBuildMenuItems($structure['menuitems']);
        }

        if (isset($structure['mails']) && !empty($structure['mails']))
        {
            $this -> _ynBuildMails($structure['mails']);
        }

        if (isset($structure['jobtypes']) && !empty($structure['jobtypes']))
        {
            $this -> _ynBuildJobTypes($structure['jobtypes']);
        }

        if (isset($structure['actiontypes']) && !empty($structure['actiontypes']))
        {
            $this -> _ynBuildActionTypes($structure['actiontypes']);
        }
        if (isset($structure['permissions']) && !empty($structure['permissions']))
        {
            $this -> _ynBuildPermission($structure['permissions']);
        }

    }

    /**
     * update package information from this page, we are welcome all experted
     * information.
     */
    protected function _ynBuildModule($row)
    {
        $name = $row['name'];
        $db = $this -> getDb();

        if ($db -> fetchOne("select count(*) from engine4_core_modules where name='{$name}'"))
        {
            unset($row['name']);
            $db -> update('engine4_core_modules', $row, "name='{$name}'");
        }
        else
        {
            $db -> insert('engine4_core_modules', $row);
        }
    }

    /**
     * rebuild menu
     */
    protected function _ynBuildMenus($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_menus where name='" . $row['name'] . "'"))
            {
                unset($row['id']);
                $db -> insert('engine4_core_menus', $row);
            }
        }
    }

    /**
     * rebuild menu items
     */
    protected function _ynBuildMenuItems($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_menuitems where name='" . $row['name'] . "'"))
            {
                unset($row['id']);
                $db -> insert('engine4_core_menuitems', $row);
            }
        }

    }

    /**
     * rebuild mail
     */
    protected function _ynBuildMails($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_mailtemplates where type='" . $row['type'] . "'"))
            {
                unset($row['mailtemplate_id']);
                $db -> insert('engine4_core_mailtemplates', $row);
            }
        }
    }

    /**
     * rebuild mail
     */
    protected function _ynBuildJobTypes($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_jobtypes where type='" . $row['type'] . "'"))
            {
                unset($row['jobtype_id']);
                $db -> insert('engine4_core_jobtypes', $row);
            }
        }
    }

    /**
     * rebuild mail
     */
    protected function _ynBuildNotificationTypes($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_activity_notificationtypes where type='" . $row['type'] . "'"))
            {
                $db -> insert('engine4_activity_notificationtypes', $row);
            }
        }
    }

    /**
     * rebuild mail
     */
    protected function _ynBuildActionTypes($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_activity_actiontypes where type='" . $row['type'] . "'"))
            {
                $db -> insert('engine4_activity_actiontypes', $row);
            }
        }
    }

    protected function _ynBuildPermission($rows)
    {
        $db = $this -> getDb();

        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            list($level, $type, $name, $value, $params) = $row;

            if ($value === NULL)
            {
                $value = 'NULL';
            }

            if ($params == NULL)
            {
                $params = 'NULL';
            }
            else
            {
                $params = $db -> quote($params);
            }

            $sql = "INSERT IGNORE INTO `engine4_authorization_permissions`
                      SELECT
                        level_id as `level_id`,
                        '{$type}' as `type`,
                        '{$name}' as `name`,
                        '$value' as `value`,
                        $params as `params`
                      FROM `engine4_authorization_levels` WHERE `type` IN('$level');
                ";
            $db -> query($sql);
        }

    }

    /**
     * rebuidl pages
     */
    protected function _ynBuildPages($pageStructure)
    {
        $db = $this -> getDb();

        foreach ($pageStructure as $name => $page)
        {
            // check page
            $page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', $name) -> limit(1) -> query() -> fetchColumn();
            if ($page_id)
            {
                continue;
            }
            else
            {
                echo 'process name ' . $name;
                $this -> _ynAddOnePage($page);
            }
        }

    }

    protected function _ynAddOnePage($page)
    {
        $db = $this -> getDb();
        // Insert page
        $db -> insert('engine4_core_pages', array(
            'name' => $page['name'],
            'displayname' => $page['displayname'],
            'url' => $page['url'],
            'title' => $page['title'],
            'description' => $page['description'],
            'keywords' => $page['keywords'],
            'custom' => $page['custom'],
            'fragment' => $page['fragment'],
            'layout' => $page['layout'],
            'levels' => $page['levels'],
            'provides' => $page['provides']
        ));

        $page_id = $db -> lastInsertId();

        if (!$page_id)
        {
            return false;
        }

        if (isset($page['ynchildren']) && !empty($page['ynchildren']))
        {
            $this -> _ynAddPageContent($page_id, null, $page['ynchildren']);
        }
        return true;
    }

    protected function _ynAddPageContent($page_id, $parent_content_id = null, $contents)
    {
        $db = $this -> getDb();
        foreach ($contents as $content)
        {
            if (empty($content))
            {
                continue;
            }
            $db -> insert('engine4_core_content', array(
                'page_id' => $page_id,
                'parent_content_id' => $parent_content_id,
                'type' => $content['type'],
                'name' => $content['name'],
                'order' => $content['order'],
                'params' => $content['params'],
                'attribs' => $content['attribs']
            ));

            $pid = $db -> lastInsertId();

            if (!$pid)
            {
                throw new Engine_Package_Installer_Exception("can not insert to page content!");
            }
            /**
             * recursiver insert to content
             */
            if (isset($content['ynchildren']) && !empty($content['ynchildren']))
            {
                $this -> _ynAddPageContent($page_id, $pid, $content['ynchildren']);
            }
        }
    }

}
