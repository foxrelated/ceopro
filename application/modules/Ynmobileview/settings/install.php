<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_Installer extends Engine_Package_Installer_Module
{
	public function onInstall()
	{
		$this -> _addMobiSiteHeader();
		$this -> _addMobiSiteFooter();
		$this -> _addMobiHomePage();
		$this -> _addMobiUserHomePage();
		$this -> _addMobiUserProfilePage();
		$this -> _addMobiEventProfilePage();
		$this -> _addMobiGroupProfilePage();
		$this -> _addMobiLoginPage();
		$this -> _addMobiMusicProfilePage();
		$this -> _updateCoverPhotoUser();

		if ($this -> _checkModuleMobi())
		{
			$db = $this -> getDb();
			$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'mobi';");
		}

		parent::onInstall();
	}
	
	public function _updateCoverPhotoUser()
	{
		$sql = "ALTER TABLE `engine4_users` ADD COLUMN `cover_id` int(11) UNSIGNED DEFAULT NULL";
        $db = $this -> getDb();
        try {
            $info = $db -> describeTable('engine4_users');
            if ($info && !isset($info['cover_id']))
            {
                try
                {
                    $db -> query($sql);
                }
                catch( Exception $e )
                {
                }
            }
        }
        catch (Exception $e)
        {
        }
	}

	function onEnable()
	{
		if ($this -> _checkModuleMobi())
		{
			$db = $this -> getDb();
			$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'mobi';");
		}
		parent::onEnable();
	}

	function onDisable()
	{
		if ($this -> _checkModuleMobi())
		{
			$db = $this -> getDb();
			$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 1 WHERE `engine4_core_modules`.`name` = 'mobi';");
		}
		parent::onDisable();
	}

	protected function _addMobiSiteHeader()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'header_ynmobileview') -> limit(1);

		$info = $select -> query() -> fetch();
		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'header_ynmobileview',
				'displayname' => 'YouNet Mobile Site Header',
				'title' => 'YouNet Mobile Site Header',
				'description' => 'This is the mobile site header.',
				'custom' => 0,
				'fragment' => 1
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-menu-logo',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-menu-main',
				'parent_content_id' => $container_id,
				'order' => 3,
				'params' => '',
			));

		}
	}

	protected function _addMobiSiteFooter()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'footer_ynmobileview') -> limit(1);

		$info = $select -> query() -> fetch();
		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'footer_ynmobileview',
				'displayname' => 'YouNet Mobile Site Footer',
				'title' => 'YouNet Mobile Site Footer',
				'description' => 'This is the mobile site footer.',
				'custom' => 0,
				'fragment' => 1
			));
		}
	}

	protected function _addMobiHomePage()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynmobileview_index_index') -> limit(1);

		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynmobileview_index_index',
				'displayname' => 'YouNet Mobile Home Page',
				'title' => 'YouNet Mobile Home Page',
				'description' => 'This is the mobile homepage.',
				'custom' => 0,
				'layout' => 'default',
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '{"max":3}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.featured-photos',
				'parent_content_id' => $tab_id,
				'order' => 4,
				'params' => '{"title":"Featured Photos"}',
			));
			
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '{"max":3}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $tab_id,
				'order' => 6,
				'params' => '{"title":"What\'s New"}',
			));
		}
	}

	protected function _addMobiUserHomePage()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynmobileview_index_userhome') -> limit(1);

		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynmobileview_index_userhome',
				'displayname' => 'YouNet Mobile Member Home Page',
				'title' => 'YouNet Mobile Member Home Page',
				'description' => 'This is the mobile member homepage.',
				'custom' => 0
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
		}
	}

	protected function _addMobiUserProfilePage()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynmobileview_index_profile') -> limit(1);

		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynmobileview_index_profile',
				'displayname' => 'YouNet Mobile Member Profile',
				'title' => 'YouNet Mobile Member Profile',
				'description' => 'This is the mobile verison of a member profile.',
				'custom' => 0
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'user.profile-photo',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '{"itemCountPerPage":"8"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'user.profile-status',
				'parent_content_id' => $middle_id,
				'order' => 4,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-profile-options',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => '{"max":6}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $tab_id,
				'order' => 7,
				'params' => '{"title":"What\'s New"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'user.profile-fields',
				'parent_content_id' => $tab_id,
				'order' => 8,
				'params' => '{"title":"Info"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'user.profile-friends',
				'parent_content_id' => $tab_id,
				'order' => 9,
				'params' => '{"title":"Friends","titleCount":true}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-profile-photos',
				'parent_content_id' => $tab_id,
				'order' => 10,
				'params' => '{"title":"Photos","titleCount":true}',
			));
		}
	}

	protected function _addMobiEventProfilePage()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynmobileview_event_profile') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynmobileview_event_profile',
				'displayname' => 'YouNet Mobile Event Profile',
				'title' => 'YouNet Mobile Event Profile',
				'description' => 'This is the mobile verison of an event profile.',
				'custom' => 0
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'event.profile-status',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'event.profile-photo',
				'parent_content_id' => $middle_id,
				'order' => 4,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'event.profile-rsvp',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'event.profile-info',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-event-options',
				'parent_content_id' => $middle_id,
				'order' => 7,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 8,
				'params' => '{"max":2}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $tab_id,
				'order' => 9,
				'params' => '{"title":"What\'s New"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'event.profile-members',
				'parent_content_id' => $tab_id,
				'order' => 10,
				'params' => '{"title":"Guests","titleCount":true}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'event.profile-photos',
				'parent_content_id' => $tab_id,
				'order' => 11,
				'params' => '{"title":"Photos","titleCount":true}',
			));
		}
	}

	protected function _addMobiGroupProfilePage()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynmobileview_group_profile') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynmobileview_group_profile',
				'displayname' => 'YouNet Mobile Group Profile',
				'title' => 'YouNet Mobile Group Profile',
				'description' => 'This is the mobile verison of a group profile.',
				'custom' => 0
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'group.profile-status',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'group.profile-photo',
				'parent_content_id' => $middle_id,
				'order' => 4,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'group.profile-info',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-group-options',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 7,
				'params' => '{"max":2}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $tab_id,
				'order' => 8,
				'params' => '{"title":"What\'s New"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'group.profile-members',
				'parent_content_id' => $tab_id,
				'order' => 9,
				'params' => '{"title":"Members","titleCount":true}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'group.profile-photos',
				'parent_content_id' => $tab_id,
				'order' => 10,
				'params' => '{"title":"Photos","titleCount":true}',
			));
		}
	}

	protected function _addMobiLoginPage()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynmobileview_index_login') -> limit(1);

		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynmobileview_index_login',
				'displayname' => 'YouNet Mobile Login Page',
				'title' => 'YouNet Mobile Login Page',
				'description' => 'This is the mobile login page.',
				'custom' => 0,
				'layout' => 'default',
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.content',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
		}
	}

	protected function _addMobiMusicProfilePage()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynmobileview_music_profile') -> limit(1);

		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynmobileview_music_profile',
				'displayname' => 'YouNet Mobile Music Profile',
				'title' => 'YouNet Mobile Music Profile',
				'description' => 'This is the music profile page.',
				'custom' => 0,
				'layout' => 'default',
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.content',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
		}
	}

	protected function _checkModuleMobi()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_modules') -> where('name = ?', 'mobi') -> limit(1);
		$check = $select -> query() -> fetch();
		if (empty($check))
			return false;
		return true;
	}

}
