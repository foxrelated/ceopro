<?php
class Ynmember_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
        $this->_addAdvancedMemberBrowseReviewsPage();
		$this->_addAdvancedMemberReviewDetailPage();
		$this->_addAdvancedMemberUserPage();
		$this->_addAdvancedMemberHomePage();
		$this->_addAdvancedMemberPrivacyPage();
		$this->_addAdvancedMemberMyFriendPage();
		$this->_addAdvancedMemberFeaturePage();
		$this->_addAdvancedMemberRatingPage();
		$this->_addAdvancedMemberBirthDayPage();
		$this->_editProfilePage();
		$this->_editCoverPhotoUser();
		$this->_repositionCoverPhotoUser();
		$this->_addColumns();
        parent::onInstall();
	}
	protected function _addColumns()
    { 
        $db = $this -> getDb(); 
        try { 
            $cols = $db->describeTable('engine4_users'); 
 
            if( !isset($cols['like_count']) ) { 
                $db->query('ALTER TABLE `engine4_users` ' . 
                    'ADD COLUMN `like_count` int( 11 ) NOT NULL default "0"'); 
            } 
            if( !isset($cols['rating']) ) { 
                $db->query('ALTER TABLE `engine4_users` ' . 
                    'ADD COLUMN `rating` int( 11 ) NOT NULL default "0"'); 
            } 
            if( !isset($cols['review_count']) ) { 
                $db->query('ALTER TABLE `engine4_users` ' . 
                    'ADD COLUMN `review_count` int( 11 ) NOT NULL default "0"'); 
            } 
            if( !isset($cols['member_of_day']) ) { 
                $db->query('ALTER TABLE `engine4_users` ' . 
                    'ADD COLUMN `member_of_day` TINYINT(1) DEFAULT "0"');
            } 
        } catch( Exception $e ) { 
        } 
    } 
	public function _repositionCoverPhotoUser() {
		$sql = "ALTER TABLE `engine4_users` ADD COLUMN `cover_top` int(11) DEFAULT '0'";
        $db = $this -> getDb();
        try {
            $info = $db -> describeTable('engine4_users');
            if ($info && !isset($info['cover_top'])) {
                $db -> query($sql);
            }
        }
        catch (Exception $e) {}
	}
	
	public function _editCoverPhotoUser()
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
	
	protected function _editProfilePage()
	{
		// get page profile member
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$info = $select -> query() -> fetch();
		$db -> query("DELETE FROM `engine4_core_content` WHERE `name` = 'user.profile-photo' AND `page_id` = " . $info['page_id']);
		$db -> query("DELETE FROM `engine4_core_content` WHERE `name` = 'user.profile-fields' AND `page_id` = " . $info['page_id']);
		$page_id = $info['page_id'];
		
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> where('name = ?', 'top') -> limit(1);
		$info = $select -> query() -> fetch();
		if(!$info)
		{
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
			 // Insert top-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_id,
		      ));
	      $top_middle_id = $db -> lastInsertId('engine4_core_content');
		}

		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('type = ?', 'container') -> where('page_id = ?', $page_id) -> where('name = ?', 'left') -> limit(1);
		$info = $select -> query() -> fetch();
		$main_left_id = $info['content_id'];
		
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('name = ?', 'ynmember.general-rating') -> limit(1);
		$info = $select -> query() -> fetch();
		if(!$info)
		{
			//general rating
	        $db->insert('engine4_core_content', array(
	            'type' => 'widget',
	            'name' => 'ynmember.general-rating',
	            'page_id' => $page_id,
	            'parent_content_id' => $main_left_id,
	        ));
		}

		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('name = ?', 'ynmember.profile-statistics') -> limit(1);
		$info = $select -> query() -> fetch();
		if(!$info)
		{
			//profile statistics
	        $db->insert('engine4_core_content', array(
	            'type' => 'widget',
	            'name' => 'ynmember.profile-statistics',
	            'page_id' => $page_id,
	            'parent_content_id' => $main_left_id,
	        ));
		}
		
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('name = ?', 'ynmember.profile-cover') -> limit(1);
		$info = $select -> query() -> fetch();
		if(!$info)
		{
			//Profile cover 
	        $db->insert('engine4_core_content', array(
	            'type' => 'widget',
	            'name' => 'ynmember.profile-cover',
	            'page_id' => $page_id,
	            'parent_content_id' => $top_middle_id,
	            'order' => 3,
	        ));
		}
		
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('name = ?', 'core.container-tabs') -> limit(1);
		$info = $select -> query() -> fetch();
		$content_tab_id = $info['content_id'];
		
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('name = ?', 'ynmember.profile-fields') -> limit(1);
		$info = $select -> query() -> fetch();
		if(!$info)
		{
			//Profile fields 
	        $db->insert('engine4_core_content', array(
	            'type' => 'widget',
	            'name' => 'ynmember.profile-fields',
	            'page_id' => $page_id,
	            'parent_content_id' => $content_tab_id,
	            'params' => '{"title":"Info"}',
	        ));
        }
	}

	protected function _addAdvancedMemberBirthDayPage()
	{
	    $db = $this->getDb();
	
	    // profile page
	    $page_id = $db->select()
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ynmember_member_birthday')
	      ->limit(1)
	      ->query()
	      ->fetchColumn();
	      
	    if( !$page_id ) {
	      
	      // Insert page
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynmember_member_birthday',
	        'displayname' => 'Advanced Member Browse BirthDay Page',
	        'title' => 'Advanced Member Member Browse BirthDay Page',
	        'description' => '',
	        'custom' => 0,
	      ));
	      $page_id = $db->lastInsertId();
	      
	      // Insert top
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'top',
	        'page_id' => $page_id,
	        'order' => 1,
	      ));
	      $top_id = $db->lastInsertId();
	      
	      // Insert main
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'main',
	        'page_id' => $page_id,
	        'order' => 2,
	      ));
	      $main_id = $db->lastInsertId();
	      
	      // Insert top-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $top_id,
	      ));
	      $top_middle_id = $db->lastInsertId();
	      
	      // Insert main-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_id,
	        'order' => 2,
	      ));
	      $main_middle_id = $db->lastInsertId();
		  
	      //Insert menu
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.browse-menu',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1,
        ));
		
		//Featured Members 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.featured-members',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
		  
		  //Insert browse members
	      $db->insert('engine4_core_content', array(
	        'type' => 'widget',
	        'name' => 'ynmember.browse-birthday-members',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_middle_id,
	        'order' => 1,
	      ));
	    }
	}
	
	protected function _addAdvancedMemberRatingPage()
	{
	    $db = $this->getDb();
	
	    // profile page
	    $page_id = $db->select()
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ynmember_member_rating')
	      ->limit(1)
	      ->query()
	      ->fetchColumn();
	      
	    if( !$page_id ) {
	      
	      // Insert page
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynmember_member_rating',
	        'displayname' => 'Advanced Member Members Rating Page',
	        'title' => 'Advanced Member Members Rating Page',
	        'description' => '',
	        'custom' => 0,
	      ));
	      $page_id = $db->lastInsertId();
	      
	      // Insert top
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'top',
	        'page_id' => $page_id,
	        'order' => 1,
	      ));
	      $top_id = $db->lastInsertId();
	      
	      // Insert main
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'main',
	        'page_id' => $page_id,
	        'order' => 2,
	      ));
	      $main_id = $db->lastInsertId();
	      
	      // Insert top-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $top_id,
	      ));
	      $top_middle_id = $db->lastInsertId();
	      
	      // Insert main-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_id,
	        'order' => 2,
	      ));
	      $main_middle_id = $db->lastInsertId();
		  
		   //Insert main-right
	        $db->insert('engine4_core_content', array(
	            'type' => 'container',
	            'name' => 'right',
	            'page_id' => $page_id,
	            'parent_content_id' => $main_id,
	            'order' => 1,
	        ));
	        $main_right_id = $db->lastInsertId();
	      
		  //Insert search-member
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
		  
		   //member birthday
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-birthday',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Birthday Today"}',
            'order' => 2,
        ));
		  
		  //people you may know
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.people-may-know',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"People You May Know"}',
            'order' => 3,
        ));
		
		//most reviewed member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-reviewed-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Most Reviewed Members"}',
            'order' => 4,
        ));
		
		//top rated member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-rating-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Top Rated Members"}',
            'order' => 5,
        ));
		
		//member of day
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-of-day',
            'params' => '{"title":"Member of Day"}',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'order' => 6,
        ));
		  
	      //Insert menu
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.browse-menu',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1,
        ));
	      
	     //Featured Members 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.featured-members',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
	      
	      // Insert browse member
	      $db->insert('engine4_core_content', array(
	        'type' => 'widget',
	        'name' => 'ynmember.browse-members',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_middle_id,
	        'order' => 1,
	      ));
		  
	    }
	}
	
	protected function _addAdvancedMemberFeaturePage()
	{
	    $db = $this->getDb();
	
	    // profile page
	    $page_id = $db->select()
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ynmember_member_feature')
	      ->limit(1)
	      ->query()
	      ->fetchColumn();
	      
	    if( !$page_id ) {
	      
	      // Insert page
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynmember_member_feature',
	        'displayname' => 'Advanced Member Feature Members Page',
	        'title' => 'Advanced Member Feature Members Page',
	        'description' => '',
	        'custom' => 0,
	      ));
	      $page_id = $db->lastInsertId();
	      
	       // Insert top
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'top',
	        'page_id' => $page_id,
	        'order' => 1,
	      ));
	      $top_id = $db->lastInsertId();
	      
	      // Insert main
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'main',
	        'page_id' => $page_id,
	        'order' => 2,
	      ));
	      $main_id = $db->lastInsertId();
	      
	      // Insert top-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $top_id,
	      ));
	      $top_middle_id = $db->lastInsertId();
	      
	      // Insert main-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_id,
	        'order' => 2,
	      ));
	      $main_middle_id = $db->lastInsertId();
		  
		   //Insert main-right
	        $db->insert('engine4_core_content', array(
	            'type' => 'container',
	            'name' => 'right',
	            'page_id' => $page_id,
	            'parent_content_id' => $main_id,
	            'order' => 1,
	        ));
	        $main_right_id = $db->lastInsertId();
	      
		    //Insert search-member
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
		  
		   //member birthday
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-birthday',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Birthday Today"}',
            'order' => 2,
        ));
		  
		  //people you may know
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.people-may-know',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"People You May Know"}',
            'order' => 3,
        ));
		
		//most reviewed member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-reviewed-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Most Reviewed Members"}',
            'order' => 4,
        ));
		
		//top rated member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-rating-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Top Rated Members"}',
            'order' => 5,
        ));
		
		//member of day
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-of-day',
            'params' => '{"title":"Member of Day"}',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'order' => 6,
        ));
		  
	      //Insert menu
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.browse-menu',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1,
        ));
	      
	    //Featured Members 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.featured-members',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
	      
	      // Insert browse member
	      $db->insert('engine4_core_content', array(
	        'type' => 'widget',
	        'name' => 'ynmember.browse-members',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_middle_id,
	        'order' => 1,
	      ));
	    }
	}
	
	protected function _addAdvancedMemberMyFriendPage()
	{
	    $db = $this->getDb();
	
	    // profile page
	    $page_id = $db->select()
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ynmember_member_myfriend')
	      ->limit(1)
	      ->query()
	      ->fetchColumn();
	      
	    if( !$page_id ) {
	      
	      // Insert page
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynmember_member_myfriend',
	        'displayname' => 'Advanced Member My Friend Page',
	        'title' => 'Advanced Member My Friend Page',
	        'description' => '',
	        'custom' => 0,
	      ));
	      $page_id = $db->lastInsertId();
	      
	       // Insert top
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'top',
	        'page_id' => $page_id,
	        'order' => 1,
	      ));
	      $top_id = $db->lastInsertId();
	      
	      // Insert main
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'main',
	        'page_id' => $page_id,
	        'order' => 2,
	      ));
	      $main_id = $db->lastInsertId();
	      
	      // Insert top-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $top_id,
	      ));
	      $top_middle_id = $db->lastInsertId();
	      
	      // Insert main-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_id,
	        'order' => 2,
	      ));
	      $main_middle_id = $db->lastInsertId();
		  
		   //Insert main-right
	        $db->insert('engine4_core_content', array(
	            'type' => 'container',
	            'name' => 'right',
	            'page_id' => $page_id,
	            'parent_content_id' => $main_id,
	            'order' => 1,
	        ));
	        $main_right_id = $db->lastInsertId();
	      
		  //Insert search-member
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
		  
		   //member birthday
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-birthday',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Birthday Today"}',
            'order' => 2,
        ));
		  
		  //people you may know
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.people-may-know',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"People You May Know"}',
            'order' => 3,
        ));
		
		//most reviewed member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-reviewed-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Most Reviewed Members"}',
            'order' => 4,
        ));
		
		//top rated member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-rating-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Top Rated Members"}',
            'order' => 5,
        ));
		
		//member of day
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-of-day',
            'params' => '{"title":"Member of Day"}',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'order' => 6,
        ));
		  
	      //Insert menu
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.browse-menu',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1,
        ));
	      
	     //Featured Members 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.featured-members',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
	      
	      // Insert browse member
	      $db->insert('engine4_core_content', array(
	        'type' => 'widget',
	        'name' => 'ynmember.members-listing',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_middle_id,
	        'order' => 1,
	      ));
	    }
	}
	
    protected function _addAdvancedMemberPrivacyPage()
	{
	    $db = $this->getDb();
	
	    // profile page
	    $page_id = $db->select()
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ynmember_index_privacy')
	      ->limit(1)
	      ->query()
	      ->fetchColumn();
	      
	    if( !$page_id ) {
	      
	      // Insert page
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynmember_index_privacy',
	        'displayname' => 'Advanced Member Privacy Settings Page',
	        'title' => 'Advanced Member Privacy Settings Page',
	        'description' => '',
	        'custom' => 0,
	      ));
	      $page_id = $db->lastInsertId();
	      
	      // Insert top
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'top',
	        'page_id' => $page_id,
	        'order' => 1,
	      ));
	      $top_id = $db->lastInsertId();
	      
	      // Insert main
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'main',
	        'page_id' => $page_id,
	        'order' => 2,
	      ));
	      $main_id = $db->lastInsertId();
	      
	      // Insert top-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $top_id,
	      ));
	      $top_middle_id = $db->lastInsertId();
	      
	      // Insert main-middle
	      $db->insert('engine4_core_content', array(
	        'type' => 'container',
	        'name' => 'middle',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_id,
	        'order' => 2,
	      ));
	      $main_middle_id = $db->lastInsertId();
	      
	      // Insert menu
	      $db->insert('engine4_core_content', array(
	        'type' => 'widget',
	        'name' => 'user.settings-menu',
	        'page_id' => $page_id,
	        'parent_content_id' => $top_middle_id,
	        'order' => 1,
	      ));
	      
	      // Insert content
	      $db->insert('engine4_core_content', array(
	        'type' => 'widget',
	        'name' => 'core.content',
	        'page_id' => $page_id,
	        'parent_content_id' => $main_middle_id,
	        'order' => 1,
	      ));
	    }
	}
    
	protected function _addAdvancedMemberHomePage() {
        $db = $this->getDb();
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmember_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
        	
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmember_index_index',
                'displayname' => 'Advanced Member Home Page',
                'title' => 'Advanced Member Home Page',
                'description' => 'This page lists all members.',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();
            
            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();
            
            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();  
            
			
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
			
			 //Insert search-member
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
		  
		   //member birthday
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-birthday',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Birthday Today"}',
            'order' => 2,
        ));
		  
		  //people you may know
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.people-may-know',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"People You May Know"}',
            'order' => 3,
        ));
		
		//most reviewed member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-reviewed-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Most Reviewed Members"}',
            'order' => 4,
        ));
		
		//top rated member
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.most-rating-members',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'params' => '{"title":"Top Rated Members"}',
            'order' => 5,
        ));
		
		//member of day
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'ynmember.member-of-day',
            'params' => '{"title":"Member of Day"}',
            'page_id' => $page_id,
            'parent_content_id' => $main_right_id,
            'order' => 6,
        ));
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
			//Featured Members 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.featured-members',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
			
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.members-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                      
        }
    }
    
	protected function _addAdvancedMemberBrowseReviewsPage() {
        $db = $this->getDb();
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmember_review_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
        	
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmember_review_index',
                'displayname' => 'Advanced Member Browse Reviews Page',
                'title' => 'Advanced Member Browse Reviews Page',
                'description' => 'Advanced Member Browse Reviews Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();
            
            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();
            
            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();  
            
			
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
			
			//Insert search-review
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.search-review',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
            ));
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
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
    }
	
	protected function _addAdvancedMemberReviewDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmember_review_detail')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmember_review_detail',
                'displayname' => 'Advanced Member Review Detail Page',
                'title' => 'Advanced Member Review Detail Page',
                'description' => 'Advanced Member Review Detail Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();
            
            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();
            
            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();  
            
			
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
			
			//Insert tag
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.member-review-for',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'params' => '{"title":"This Member Also Review For"}',
                'order' => 3,
            ));
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                      
			
			//Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.comments',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));       
        }
    }
	
	protected function _addAdvancedMemberUserPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmember_review_user')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmember_review_user',
                'displayname' => 'Advanced Member Review User Page',
                'title' => 'Advanced Member Review User Page',
                'description' => 'Advanced Member Review User Page',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId();
            
            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();
            
			 //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
			
            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();  
            
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
			//Featured Members 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.profile-cover',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
			
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));                      
			
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $main_middle_id,
				'order' => 2,
				'params' => '{"max":"8"}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');
			
			 //General Review
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmember.general-review',
				'parent_content_id' => $tab_id,
				'order' => 1,
				'params' => '{"title":"General Reviews"}',
			));
			
			 //Full Review
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmember.full-review',
				'parent_content_id' => $tab_id,
				'order' => 2,
				'params' => '{"title":"Full Reviews"}',
			));
        }
    }
}