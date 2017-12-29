<?php
class Advgroup_Installer extends Engine_Package_Installer_Module
{
	function onInstall()
	{
		$this -> _addGroupCreatePage();
		$this -> _addGroupManagePage();
		$this -> _addMobiGroupProfilePage();
		
		// fix view privacy in group
		$db = $this -> getDb();
		$sql = "DELETE IGNORE FROM `engine4_authorization_allow` WHERE `action`= 'view' and `role` LIKE 'owner%'";
		try
		{
			$db -> query($sql);
		}
		catch( Exception $e )
		{
		}
		//
		// install content areas
		//
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		//
		//update latitude and longitude
		//
		$info = $db -> describeTable('engine4_group_groups');
		$sql = "ALTER TABLE `engine4_group_groups` ADD COLUMN `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL AFTER `view_count`";
		$db = $this -> getDb();
		try
		{
			if ($info && !isset($info['latitude']))
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

		$sql = "ALTER TABLE `engine4_group_groups` ADD COLUMN `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL AFTER `view_count`";
		$db = $this -> getDb();
		try
		{
			if ($info && !isset($info['longitude']))
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

		if ($info && !isset($info['longitude']) && !isset($info['latitude']))
		{
			$select -> from('engine4_group_groups');
			$groups = $select -> query() -> fetchAll();
			foreach ($groups as $group)
			{
				$location = Zend_Json::decode($group['location']);
				$latitude = $location['latitude'];
				$longitude = $location['longtitude'];
				if($latitude && $longitude)
				{
					$query = "UPDATE `engine4_group_groups` SET `latitude` = '" . $latitude . "', `longitude` = '" . $longitude . "' WHERE `engine4_group_groups`.`group_id` = " . $group['group_id'];
					$db -> query($query);
				}
			}
		}

		//
		// Group main page
		//

		//---------HOME PAGE-----------
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'advgroup_index_browse') -> limit(1);
		$info = $select -> query() -> fetch();

		if (!empty($info))
		{
			$db -> query("DELETE FROM `engine4_core_content` WHERE `page_id` = " . $info['page_id']);
			$db -> query("DELETE FROM `engine4_core_pages` WHERE `page_id` = " . $info['page_id']);
		}

		$db -> insert('engine4_core_pages', array(
			'name' => 'advgroup_index_browse',
			'displayname' => 'Advanced Group Home Page',
			'title' => 'Advanced Group Home Page',
			'description' => 'The Homepage of Advgroup module.',
			'custom' => '0',
		));

		$page_id = $db -> lastInsertId('engine4_core_pages');

		//containers
		//Top
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'top',
			'parent_content_id' => null,
			'order' => 1,
			'params' => '',
		));
		$top_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'middle',
			'parent_content_id' => $top_id,
			'order' => 6,
			'params' => '',
		));
		$middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-menu',
			'parent_content_id' => $middle_id,
			'order' => 1,
			'params' => '',
		));

		//Main
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'main',
			'parent_content_id' => null,
			'order' => 2,
			'params' => '',
		));
		$container_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'middle',
			'parent_content_id' => $container_id,
			'order' => 6,
			'params' => '',
		));
		$middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'right',
			'parent_content_id' => $container_id,
			'order' => 5,
			'params' => '',
		));
		$right_id = $db -> lastInsertId('engine4_core_content');

		//Middle
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.featured-groups',
			'parent_content_id' => $middle_id,
			'order' => 7,
			'params' => '{"title":"Featured Groups"}',
		));

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.list-most-items',
			'parent_content_id' => $middle_id,
			'order' => 9,
		));

		//Right
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-search',
			'parent_content_id' => $right_id,
			'order' => 7,
			'params' => '',
		));
		// -- Quick navigation
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-quick-navigation',
			'parent_content_id' => $right_id,
			'order' => 8,
			'params' => '',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.overall-statistic',
			'parent_content_id' => $right_id,
			'order' => 9,
			'params' => '{"title":"Statistics"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-category-search',
			'parent_content_id' => $right_id,
			'order' => 10,
			'params' => '{"title":"Categories"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-tags',
			'parent_content_id' => $right_id,
			'order' => 11,
			'params' => '{"title":"Tags"}',
		));

		//---------LISTING PAGE-----------
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		$select -> from('engine4_core_pages') -> where('name = ?', 'advgroup_index_listing') -> limit(1);
		$info = $select -> query() -> fetch();

		if (!empty($info))
		{
			$db -> query("DELETE FROM `engine4_core_content` WHERE `page_id` = " . $info['page_id']);
			$db -> query("DELETE FROM `engine4_core_pages` WHERE `page_id` = " . $info['page_id']);
		}

		$db -> insert('engine4_core_pages', array(
			'name' => 'advgroup_index_listing',
			'displayname' => 'Advanced Group Listing Page',
			'title' => 'Advanced Group Listing Page',
			'description' => 'The listing page of Advgroup module.',
			'custom' => '0',
		));

		$page_id = $db -> lastInsertId('engine4_core_pages');

		//containers
		//Top
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'top',
			'parent_content_id' => null,
			'order' => 1,
			'params' => '',
		));
		$top_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'middle',
			'parent_content_id' => $top_id,
			'order' => 6,
			'params' => '',
		));
		$middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-menu',
			'parent_content_id' => $middle_id,
			'order' => 7,
			'params' => '',
		));

		//Main
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'main',
			'parent_content_id' => null,
			'order' => 2,
			'params' => '',
		));
		$container_id = $db -> lastInsertId('engine4_core_content');

		//Middle
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'middle',
			'parent_content_id' => $container_id,
			'order' => 6,
			'params' => '',
		));
		$middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-listing',
			'parent_content_id' => $middle_id,
			'order' => 7,
			'params' => '',
		));

		//Right
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'right',
			'parent_content_id' => $container_id,
			'order' => 5,
			'params' => '',
		));
		$right_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-search',
			'parent_content_id' => $right_id,
			'order' => 7,
			'params' => '',
		));
		// -- Quick navigation
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-quick-navigation',
			'parent_content_id' => $right_id,
			'order' => 8,
			'params' => '',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.overall-statistic',
			'parent_content_id' => $right_id,
			'order' => 9,
			'params' => '{"title":"Statistics"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-category-search',
			'parent_content_id' => $right_id,
			'order' => 10,
			'params' => '{"title":"Categories"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-tags',
			'parent_content_id' => $right_id,
			'order' => 11,
			'params' => '{"title":"Tags"}',
		));

		//-------PROFILE PAGE----------
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'advgroup_profile_index') -> limit(1);
		;

		$info = $select -> query() -> fetch();
		if (!empty($info))
		{
			$db -> query("DELETE FROM `engine4_core_content` WHERE `page_id` = " . $info['page_id']);
			$db -> query("DELETE FROM `engine4_core_pages` WHERE `page_id` = " . $info['page_id']);
		}

		$db -> insert('engine4_core_pages', array(
			'name' => 'advgroup_profile_index',
			'displayname' => 'Advanced Group Profile',
			'title' => 'Advanced Group Profile',
			'description' => 'This is the profile for an group.',
			'custom' => 0,
			'provides' => 'subject=group',
		));
		$page_id = $db -> lastInsertId('engine4_core_pages');

		//insert top container
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'top',
			'parent_content_id' => null,
			'order' => 1,
			'params' => '',
		));
		$top_id = $db -> lastInsertId('engine4_core_content');
		//insert top-middle container
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'middle',
			'parent_content_id' => $top_id,
			'order' => 6,
			'params' => '',
		));
		$top_middle_id = $db -> lastInsertId('engine4_core_content');

		//insert group-menu
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-menu',
			'parent_content_id' => $top_middle_id,
			'order' => 2,
			'params' => '',
		));

		// containers
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'main',
			'parent_content_id' => null,
			'order' => 2,
			'params' => '',
		));
		$container_id = $db -> lastInsertId('engine4_core_content');
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'middle',
			'parent_content_id' => $container_id,
			'order' => 3,
			'params' => '',
		));
		$middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'container',
			'name' => 'right',
			'parent_content_id' => $container_id,
			'order' => 2,
			'params' => '',
		));
		$right_id = $db -> lastInsertId('engine4_core_content');
		// middle column

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-cover',
			'parent_content_id' => $middle_id,
			'order' => 2,
			'params' => '',
		));

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-info',
			'parent_content_id' => $middle_id,
			'order' => 3,
			'params' => '',
		));

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-group-announcements',
			'parent_content_id' => $middle_id,
			'order' => 4,
		));

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'core.container-tabs',
			'parent_content_id' => $middle_id,
			'order' => 5,
			'params' => '{"max":"8"}',
		));
		$tab_id = $db -> lastInsertId('engine4_core_content');
		// middle tabs

		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.feed',
			'parent_content_id' => $tab_id,
			'order' => 1,
			'params' => '{"title":"Updates"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-members',
			'parent_content_id' => $tab_id,
			'order' => 2,
			'params' => '{"title":"Members","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-discussions',
			'parent_content_id' => $tab_id,
			'order' => 3,
			'params' => '{"title":"Discussions","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-events',
			'parent_content_id' => $tab_id,
			'order' => 4,
			'params' => '{"title":"Events","titleCount":true}',
		));		
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-albums',
			'parent_content_id' => $tab_id,
			'order' => 5,
			'params' => '{"title":"Albums","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-polls',
			'parent_content_id' => $tab_id,
			'order' => 6,
			'params' => '{"title":"Polls","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.recent-group-videos',
			'parent_content_id' => $tab_id,
			'order' => 7,
			'params' => '{"title":"Videos","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-wikis',
			'parent_content_id' => $tab_id,
			'order' => 8,
			'params' => '{"title":"Wikis","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-useful-links',
			'parent_content_id' => $tab_id,
			'order' => 9,
			'params' => '{"title":"Useful Links","titleCount":true}',
		));	
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-musics',
			'parent_content_id' => $tab_id,
			'order' => 10,
			'params' => '{"title":"Musics","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-mp3musics',
			'parent_content_id' => $tab_id,
			'order' => 11,
			'params' => '{"title":"Mp3Musics","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-sponsors',
			'parent_content_id' => $tab_id,
			'order' => 12,
			'params' => '{"title":"Sponsors","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-filesharing',
			'parent_content_id' => $tab_id,
			'order' => 13,
			'params' => '{"title":"FileSharing","titleCount":true}',
		));
        $db -> insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'advgroup.profile-listings',
            'parent_content_id' => $tab_id,
            'order' => 14,
            'params' => '{"title":"Listings","titleCount":true}',
        ));
		$db -> insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'advgroup.profile-social-music-albums',
            'parent_content_id' => $tab_id,
            'order' => 15,
            'params' => '{"title":"Social Music Albums","titleCount":true}',
        ));
		$db -> insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'advgroup.profile-social-music-songs',
            'parent_content_id' => $tab_id,
            'order' => 16,
            'params' => '{"title":"Social Music Songs","titleCount":true}',
        ));		

		//Right Column
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-ynultimatevideo-videos',
			'parent_content_id' => $tab_id,
			'order' => 17,
			'params' => '{"title":"Ultimate Videos","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-ynvideochannel-videos',
			'parent_content_id' => $tab_id,
			'order' => 18,
			'params' => '{"title":"VideoChannel Videos","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.suggested-poll',
			'parent_content_id' => $right_id,
			'order' => 2,
			'params' => '{"title":"Suggested Poll"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-albums',
			'parent_content_id' => $right_id,
			'order' => 2,
			'params' => '{"title":"Recent Albums","titleCount":true}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.profile-slideshow-photos',
			'parent_content_id' => $right_id,
			'order' => 3,
			'params' => '{"title":"Slide show Photos"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.recent-group-videos',
			'parent_content_id' => $right_id,
			'order' => 4,
			'params' => '{"title":"Recent Videos"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.groups-top-posters',
			'parent_content_id' => $right_id,
			'order' => 5,
			'params' => '{"title":"Top Posters"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.group-top-members',
			'parent_content_id' => $right_id,
			'order' => 6,
			'params' => '{"title":"Most Active Members"}',
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.sub-groups',
			'parent_content_id' => $right_id,
			'order' => 7,
		));
		$db -> insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'advgroup.list-maylike-groups',
			'parent_content_id' => $right_id,
			'order' => 8,
			'params' => '{"title":"May-like Groups"}',
		));
		//
		//-------- MOBILE PROFILE PAGE -----------

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'mobi_advgroup_profile') -> limit(1);
		;
		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'mobi_advgroup_profile',
				'displayname' => 'Mobile Advanced Group Profile',
				'title' => 'Mobile Advanced Group Profile',
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
				'name' => 'advgroup.profile-status',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-photo',
				'parent_content_id' => $middle_id,
				'order' => 4,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-info',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-group-announcements',
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
				'params' => '{"max":6}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'activity.feed',
				'parent_content_id' => $tab_id,
				'order' => 8,
				'params' => '{"title":"What\'s New"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-members',
				'parent_content_id' => $tab_id,
				'order' => 9,
				'params' => '{"title":"Members","titleCount":true}',
			));
		}
		parent::onInstall();

		//Disable SE Group
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'group';");

		//Migrate data from SE Group to Advanced Group
		$this -> updateGroupConfiguration('1');

		//Update album table for advgroup compatible
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_group_albums') -> where('user_id = 0');
		$albums = $select -> query() -> fetchAll();
		if (count($albums) > 0)
		{
			foreach ($albums as $album)
			{
				$select = new Zend_Db_Select($db);
				$select -> from('engine4_group_groups') -> where('group_id = ?', $album['group_id']);
				$group = $select -> query() -> fetch();
				if ($group)
				{
					$db -> query("UPDATE `engine4_group_albums` SET `user_id` =" . $group['user_id'] . ", `title`= 'Group Profile' WHERE `engine4_group_albums`.`album_id` =" . $album['album_id'] . ";");
				}
			}
		}
	}

	public function onEnable()
	{
		parent::onEnable();
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'group';");
		$this -> updateGroupConfiguration('1');

		//Update album table for advgroup compatible
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_group_albums') -> where('user_id = 0');
		$albums = $select -> query() -> fetchAll();
		if (count($albums) > 0)
		{
			foreach ($albums as $album)
			{
				$select = new Zend_Db_Select($db);
				$select -> from('engine4_group_groups') -> where('group_id = ?', $album['group_id']);
				$group = $select -> query() -> fetch();
				if ($group)
				{
					$db -> query("UPDATE `engine4_group_albums` SET `user_id` =" . $group['user_id'] . ", `title`= 'Group Profile' WHERE `engine4_group_albums`.`album_id` =" . $album['album_id'] . ";");
				}
			}
		}
	}

	public function onDisable()
	{
		parent::onDisable();
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 1 WHERE `engine4_core_modules`.`name` = 'group';");
		$this -> updateGroupConfiguration('0');
	}

	protected function updateGroupConfiguration($enable)
	{
		$db = $this -> getDb();
		//When disable module
		if ($enable == "0")
		{
			set_time_limit(0);
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'group_create', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='advgroup_create';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'group_join', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='advgroup_join';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'group_promote', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='advgroup_promote';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'group_topic_create', `object_type` = 'group_topic' WHERE `engine4_activity_actions`.`type` ='advgroup_topic_create';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'group_topic_reply', `object_type` = 'group_topic' WHERE `engine4_activity_actions`.`type` ='advgroup_topic_reply';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'group_photo_upload', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='advgroup_photo_upload';");
			$db -> query("UPDATE `engine4_activity_actions` SET `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='post' AND `object_type` = 'advgroup_group';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'group_topic' WHERE `engine4_activity_attachments`.`type` ='advgroup_topic';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'group_post' WHERE `engine4_activity_attachments`.`type` ='advgroup_post';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'group_photo' WHERE `engine4_activity_attachments`.`type` ='advgroup_photo';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'group_topic',`type` = 'group_discussion_response' WHERE `engine4_activity_notifications`.`type` ='advgroup_discussion_response';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'group_topic',`type` = 'group_discussion_reply' WHERE `engine4_activity_notifications`.`type` ='advgroup_discussion_reply';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type` = 'group_promote' WHERE `engine4_activity_notifications`.`type` ='advgroup_promote';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'group_photo' WHERE `engine4_activity_notifications`.`object_type` ='advgroup_photo';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'group_album' WHERE `engine4_activity_notifications`.`object_type` ='advgroup_album';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type`='group_invite' WHERE `engine4_activity_notifications`.`type` ='advgroup_invite';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type`='group_approve' WHERE `engine4_activity_notifications`.`type` ='advgroup_approve';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type`='group_accepted' WHERE `engine4_activity_notifications`.`type` ='advgroup_accepted';");
			$db -> query("UPDATE `engine4_activity_stream` SET `target_type` = 'group' WHERE `engine4_activity_stream`.`target_type` ='advgroup_group';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'group' WHERE `engine4_activity_stream`.`object_type` ='advgroup_group';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'group_topic' WHERE `engine4_activity_stream`.`object_type` ='advgroup_topic';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'group_join' WHERE `engine4_activity_stream`.`type` ='advgroup_join';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'group_create' WHERE `engine4_activity_stream`.`type` ='advgroup_create';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'group_photo_upload' WHERE `engine4_activity_stream`.`type` ='advgroup_photo_upload';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'group_promote' WHERE `engine4_activity_stream`.`type` ='advgroup_promote';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'group_topic_create' WHERE `engine4_activity_stream`.`type` ='advgroup_topic_create';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'group_topic_reply' WHERE `engine4_activity_stream`.`type` ='advgroup_topic_reply';");
			$db -> query("UPDATE `engine4_authorization_allow` SET `resource_type` = 'group' WHERE `engine4_authorization_allow`.`resource_type` ='advgroup_group';");
			$db -> query("UPDATE `engine4_authorization_allow` SET `role` = 'group_list' WHERE `engine4_authorization_allow`.`role` ='advgroup_list';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type` = 'group_photo' WHERE `engine4_core_comments`.`resource_type` ='advgroup_photo';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type` = 'group_album' WHERE `engine4_core_comments`.`resource_type` ='advgroup_album';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type` = 'group_photo' WHERE `engine4_core_likes`.`resource_type` ='advgroup_photo';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type` = 'group_album' WHERE `engine4_core_likes`.`resource_type` ='advgroup_album';");
			$db -> query("UPDATE `engine4_storage_files` SET `parent_type` = 'group_photo' WHERE `engine4_storage_files`.`parent_type` ='advgroup_photo';");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'group_topic' WHERE `engine4_core_search`.`type` = 'advgroup_topic'");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'group_post' WHERE `engine4_core_search`.`type` = 'advgroup_post'");
			$db -> query("UPDATE `engine4_core_content` SET `name` = 'group.profile-groups' WHERE `engine4_core_content`.`name` = 'advgroup.profile-groups'");
		}
		//When enable module
		if ($enable == "1")
		{
			set_time_limit(0);
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'advgroup_create', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='group_create';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'advgroup_join', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='group_join';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'advgroup_promote', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='group_promote';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'advgroup_topic_create', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='group_topic_create';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'advgroup_topic_reply', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='group_topic_reply';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'advgroup_photo_upload', `object_type` = 'group' WHERE `engine4_activity_actions`.`type` ='group_photo_upload';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'advgroup_topic' WHERE `engine4_activity_attachments`.`type` ='group_topic';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'advgroup_post' WHERE `engine4_activity_attachments`.`type` ='group_post';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'advgroup_photo' WHERE `engine4_activity_attachments`.`type` ='group_photo';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'advgroup_topic',`type` = 'advgroup_discussion_response' WHERE `engine4_activity_notifications`.`type` ='group_discussion_response';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'advgroup_topic',`type` = 'advgroup_discussion_reply' WHERE `engine4_activity_notifications`.`type` ='group_discussion_reply';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type` = 'advgroup_promote' WHERE `engine4_activity_notifications`.`type` ='group_promote';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'advgroup_photo' WHERE `engine4_activity_notifications`.`object_type` ='group_photo';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type` = 'advgroup_album' WHERE `engine4_activity_notifications`.`object_type` ='group_album';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type`='advgroup_invite' WHERE `engine4_activity_notifications`.`type` ='group_invite';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type`='advgroup_approve' WHERE `engine4_activity_notifications`.`type` ='group_approve';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `type`='advgroup_accepted' WHERE `engine4_activity_notifications`.`type` ='group_accepted';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'advgroup_topic' WHERE `engine4_activity_stream`.`object_type` ='group_topic';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'advgroup_join' WHERE `engine4_activity_stream`.`type` ='group_join';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'advgroup_create' WHERE `engine4_activity_stream`.`type` ='group_create';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'advgroup_photo_upload' WHERE `engine4_activity_stream`.`type` ='group_photo_upload';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'advgroup_promote' WHERE `engine4_activity_stream`.`type` ='group_promote';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'advgroup_topic_create' WHERE `engine4_activity_stream`.`type` ='group_topic_create';");
			$db -> query("UPDATE `engine4_activity_stream` SET `type` = 'advgroup_topic_reply' WHERE `engine4_activity_stream`.`type` ='group_topic_reply';");
			$db -> query("UPDATE `engine4_authorization_allow` SET `role` = 'advgroup_list' WHERE `engine4_authorization_allow`.`role` ='group_list';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type` = 'advgroup_photo' WHERE `engine4_core_comments`.`resource_type` ='group_photo';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type` = 'advgroup_album' WHERE `engine4_core_comments`.`resource_type` ='group_album';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type` = 'advgroup_photo' WHERE `engine4_core_likes`.`resource_type` ='group_photo';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type` = 'advgroup_album' WHERE `engine4_core_likes`.`resource_type` ='group_album';");
			$db -> query("UPDATE `engine4_storage_files` SET `parent_type` = 'advgroup_photo' WHERE `engine4_storage_files`.`parent_type` ='group_photo';");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'advgroup_topic' WHERE `engine4_core_search`.`type` = 'group_topic'");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'advgroup_post' WHERE `engine4_core_search`.`type` = 'group_post'");
			$db -> query("UPDATE `engine4_core_content` SET `name` = 'advgroup.profile-groups' WHERE `engine4_core_content`.`name` = 'group.profile-groups'");
		}
	}

	protected function _addGroupCreatePage()
	{
		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'advgroup_index_create') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'advgroup_index_create',
				'displayname' => 'Advanced Group Create Page',
				'title' => 'Advanced Group Create',
				'description' => 'This page allows users to create groups.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'advgroup.groups-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));

		}

	}

	protected function _addGroupManagePage()
	{
		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'advgroup_index_manage') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'advgroup_index_manage',
				'displayname' => 'Advanced Group Manage Page',
				'title' => 'My Groups',
				'description' => 'This page lists a user\'s groups.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 1,
			));
			$main_right_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'advgroup.groups-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));

			// Insert search
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'advgroup.groups-search',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 1,
			));

			// Insert gutter menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'advgroup.groups-quick-navigation',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 2,
			));
		}
	}

	protected function _addMobiGroupProfilePage()
	{
		$db = $this -> getDb();
		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'advgroup_mobiprofile_index') -> limit(1);
		;
		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'advgroup_mobiprofile_index',
				'displayname' => 'YouNet Mobile Advanced Group Profile',
				'title' => 'YouNet Mobile Advanced Group Profile',
				'description' => 'This is the mobile verison of a advanced group profile.',
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
				'name' => 'advgroup.profile-status',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-cover',
				'parent_content_id' => $middle_id,
				'order' => 4,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-info',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.sub-groups',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-group-announcements',
				'parent_content_id' => $middle_id,
				'order' => 7,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-options',
				'parent_content_id' => $middle_id,
				'order' => 8,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 9,
				'params' => '{"max":3}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $tab_id,
				'order' => 10,
				'params' => '{"title":"What\'s New"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-members',
				'parent_content_id' => $tab_id,
				'order' => 11,
				'params' => '{"title":"Members","titleCount":true}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advgroup.profile-photos',
				'parent_content_id' => $tab_id,
				'order' => 12,
				'params' => '{"title":"Photos","titleCount":true}',
			));
			/*
			 $db -> insert('engine4_core_content', array(
			 'page_id' => $page_id,
			 'type' => 'widget',
			 'name' => 'advgroup.profile-useful-links',
			 'parent_content_id' => $tab_id,
			 'order' => 12,
			 'params' => '{"title":"Links","titleCount":true}',
			 ));
			 $db -> insert('engine4_core_content', array(
			 'page_id' => $page_id,
			 'type' => 'widget',
			 'name' => 'advgroup.profile-events',
			 'parent_content_id' => $tab_id,
			 'order' => 13,
			 'params' => '{"title":"Events","titleCount":true}',
			 ));
			 $db -> insert('engine4_core_content', array(
			 'page_id' => $page_id,
			 'type' => 'widget',
			 'name' => 'advgroup.profile-discussions',
			 'parent_content_id' => $tab_id,
			 'order' => 14,
			 'params' => '{"title":"Discussions","titleCount":true}',
			 ));
			 */
		}
	}

}
