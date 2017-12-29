<?php

class Ynadvsearch_Installer extends Engine_Package_Installer_Module {
	function onInstall() {
		
		//
		// install content areas
		//
		$db     = $this->getDb();
   		$select = new Zend_Db_Select($db);

      	// BROWSE STORE
	     $select = new Zend_Db_Select($db);
	     $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynadvsearch_search_index')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
		
		if (!empty($info))
		{
			$db -> query("DELETE FROM `engine4_core_content` WHERE `page_id` = " . $info['page_id']);
			$db -> query("DELETE FROM `engine4_core_pages` WHERE `page_id` = " . $info['page_id']);
		}
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynadvsearch_search_index',
	        'displayname' => 'YouNet Advanced Search Page',
	        'title' => 'YouNet Advanced Search Page',
	        'description' => 'YouNet Advanced Search Page',
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
           
           //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $middle_id,
                'order' => 1,
            ));
           
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynadvsearch.search-field',
	        'parent_content_id' => $middle_id,
	        'order' => 2,
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
	        'order' => 5,
	        'params' => '',
	      ));
	      $left_id = $db->lastInsertId('engine4_core_content');
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynadvsearch.search-results',
	        'parent_content_id' => $middle_id,
	        'order' => 6,
	        'params' => '',
	      ));
     
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynadvsearch.modules-list',
	        'parent_content_id' => $left_id,
	        'order' => 1,
	        'params' => '',
	      ));
          
          $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'ynadvsearch.top-keywords',
            'parent_content_id' => $left_id,
            'order' => 2,
            'params' => '{"title":"Top Keywords"}',
          ));
	      
        //TODO add new page here
        $this->_addFaqsPage();
		$this->addMemberPage();
		$this->addAlbumPage();
		$this->addEventPage();
		$this->addGroupPage();
		parent::onInstall();
	}
    
    protected function _addFaqsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_faqs_index',
                'displayname' => 'YouNet Advanced Search FAQs Page',
                'title' => 'FAQs',
                'description' => 'This page show the FAQs',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.main-menu',
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
	
	public function addMemberPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_user-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_user-search',
                'displayname' => 'YouNet Advanced Members Search Page',
                'title' => 'Members Search Page',
                'description' => 'This page display Members Search Page',
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
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert search form
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.member-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));
            
            //Insert search form YN
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmember.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //insert ynadvsearch form
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynadvsearch.search-field',
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
    }

	public function addAlbumPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_album-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_album-search',
                'displayname' => 'YouNet Advanced Albums Search Page',
                'title' => 'Albums Search Page',
                'description' => 'This page display Albums Search Page',
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
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert search form
            
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.album-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));
			
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.yn-album-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //insert ynadvsearch form
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynadvsearch.search-field',
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
    }
	
	public function addEventPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_event-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_event-search',
                'displayname' => 'YouNet Advanced Events Search Page',
                'title' => 'Events Search Page',
                'description' => 'This page display Events Search Page',
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
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert search form
            
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.event-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));
			
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynevent.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //insert ynadvsearch form
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynadvsearch.search-field',
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
    }

	public function addGroupPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_group-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_group-search',
                'displayname' => 'YouNet Advanced Groups Search Page',
                'title' => 'Groups Search Page',
                'description' => 'This page display Groups Search Page',
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
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert search form SE
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.group-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));
            
            //Insert search form YN
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.yn-group-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //insert ynadvsearch form
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynadvsearch.search-field',
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
    }
}