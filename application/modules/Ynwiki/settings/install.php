<?php
class Ynwiki_Installer extends Engine_Package_Installer_Module {
	function onInstall() {
		//
		// install content areas
		//
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// profile page
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$page_id = $select -> query() -> fetchObject() -> page_id;

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynwiki.profile-edit-pages');

		$info = $select -> query() -> fetch();

		// ynwiki.profile-edit-pages

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
		if ($tab_id && @$tab_id -> content_id) {
			$tab_id = $tab_id -> content_id;
		} else {
			$tab_id = null;
		}

		// tab on profile
		if (empty($info)) {
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.profile-edit-pages', 'parent_content_id' => ($tab_id ? $tab_id : $middle_id), 'order' => 4, 'params' => '{"title":"Recently Edited Pages","titleCount":true}', ));
		}

		//Browse Wiki
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynwiki_index_browse') -> limit(1);
		;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynwiki_index_browse', 'displayname' => 'Wiki Home Page', 'title' => 'Wiki Home Page', 'description' => 'This is Wiki Home Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.menu-pages', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.wiki-space-pages', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '{"max":"10","title":"Wiki Spaces"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.container-tabs', 'parent_content_id' => $middle_id, 'order' => 2, 'params' => '{"max":"6","title":"","name":"core.container-tabs"}', ));
			$tab0_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.recent-updated-pages', 'parent_content_id' => $tab0_id, 'order' => 1, 'params' => '{"title":"Recently Updated"}', ));
			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.search-pages', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.tag-pages', 'parent_content_id' => $right_id, 'order' => 2, 'params' => '{"title":"Tags"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.recent-viewed-pages', 'parent_content_id' => $right_id, 'order' => 3, 'params' => '{"title":"Recently I Viewed"}', ));
		}
		//Listing Wiki
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynwiki_index_listing') -> limit(1);
		;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynwiki_index_listing', 'displayname' => 'Wikis Listing', 'title' => 'Wikis Listing', 'description' => 'This is Wikis Listing page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.menu-pages', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.listing-pages', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '{"title":"Wiki Search"}', ));
			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.search-pages', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.tag-pages', 'parent_content_id' => $right_id, 'order' => 2, 'params' => '{"title":"Tags"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.recent-viewed-pages', 'parent_content_id' => $right_id, 'order' => 3, 'params' => '{"title":"Recently I Viewed"}', ));
		}
		//Wiki Profile
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynwiki_index_view') -> limit(1);
		;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynwiki_index_view', 'displayname' => 'Wikis Profile', 'title' => 'Wikis Profile', 'description' => 'This is Wikis Profile page.', 'custom' => '1', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');
			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.menu-pages', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'left', 'parent_content_id' => $container_id, 'order' => 4, 'params' => '', ));
			$left_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.profile-options', 'parent_content_id' => $left_id, 'order' => 6, 'params' => '{"title":"Wiki Profile Options"}', ));
						
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynwiki.profile-detail', 'parent_content_id' => $middle_id, 'order' => 8, 'params' => '{"title":"Wiki Profile Detail"}', ));

		}

		parent::onInstall();
	}

}
?>