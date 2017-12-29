<?php
class Ynmultilisting_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
    	$this -> _addUserProfileContent();
    	$this -> _addAllWishListPage();
        $this -> _addWishListDetailPage();
        $this -> _addMyWishListPage();
        $this -> _addComparisonPage();
    	$this -> _addFaqsPage();
		$this -> _addPackagePage();
		$this -> _addCreatePage();
        $this -> _addCreateStep2Page();
		$this -> _addEditPage();
		$this -> _addListingDetailPage();
    	$this -> _addHomePage();
    	$this -> _addBrowsePage();
    	$this -> _addManagePage();
    	$this -> _addBrowseReviewPage();
		$this -> _addReviewDetailPage();
        $this -> _addImportFromFilePage();
        $this -> _addImportFromModulePage();
		$this -> _addImportFromModuleExecPage();
		$this -> _addMobileViewDetailPage();

		parent::onInstall();
    }
    
	protected function _addMobileViewDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_profile_mobile')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_profile_mobile',
                'displayname' => 'Multiple Listing Mobile Detail Page',
                'title' => 'Multiple Listing Mobile Detail Page',
                'description' => 'This page show the informations of Multiple Listing',
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
            
             //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert cover style 1
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-cover-style1',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));  
			
            // Insert tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $main_container_id = $db -> lastInsertId();
            
             // Insert related listings widget
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-related-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"Related Listings"}',
            ));
            
            //Insert profile info widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 1,
                'params' => '{"title":"Info"}',
            ));
            
            //Insert activity widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'activity.feed',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 2,
                'params' => '{"title":"Activity"}',
            ));
            
            //Insert review widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-reviews',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 3,
                'params' => '{"title":"Reviews", "titleCount": true}',
            ));
            
			//Insert profile photo widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-albums',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 4,
                'params' => '{"title":"Photos", "titleCount": true}',
            ));
			
            //Insert profile video widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-videos',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 5,
                'params' => '{"title":"Videos", "titleCount": true}',
            ));

            //Insert profile ultimate video widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-ultimate-videos',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 6,
                'params' => '{"title":"Ultimate Videos", "titleCount": true}',
            ));
            
			//Insert profile discussion widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-discussions',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 7,
                'params' => '{"title":"Discussions", "titleCount": true}',
            ));
			
			//Insert location widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-location',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Location"}',
            ));
			
			//Insert about widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-about',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"About Us"}',
            ));
			
			//Insert tag widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Tags"}',
            ));
        }
    }
	
	protected function _addUserProfileContent() {
    	//
    	// install content areas
    	//
    	$db = $this->getDb();
    	$select = new Zend_Db_Select($db);

    	// profile page
    	$select
        	->from('engine4_core_pages')
        	->where('name = ?', 'user_profile_index')
        	->limit(1);
    	$page_id = $select->query()->fetchObject()->page_id;

    	// ynlistings.profile-listings
    
    	// Check if it's already been placed
    	$select = new Zend_Db_Select($db);
    	$select
        	->from('engine4_core_content')
        	->where('page_id = ?', $page_id)
        	->where('type = ?', 'widget')
        	->where('name = ?', 'ynmultilisting.profile-listings');
   	 	$info = $select->query()->fetch();
    	if( empty($info) ) {
    
        	// container_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('page_id = ?', $page_id)
            	->where('type = ?', 'container')
            	->limit(1);
        	$container_id = $select->query()->fetchObject()->content_id;

        	// middle_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('parent_content_id = ?', $container_id)
            	->where('type = ?', 'container')
            	->where('name = ?', 'middle')
            	->limit(1);
        	$middle_id = $select->query()->fetchObject()->content_id;

        	// tab_id (tab container) may not always be there
        	$select
            	->reset('where')
            	->where('type = ?', 'widget')
            	->where('name = ?', 'core.container-tabs')
            	->where('page_id = ?', $page_id)
            	->limit(1);
        	$tab_id = $select->query()->fetchObject();
        	if( $tab_id && @$tab_id->content_id ) {
            	$tab_id = $tab_id->content_id;
        	} else {
            	$tab_id = null;
        	}

        	// tab on profile
        	$db->insert('engine4_core_content', array(
            	'page_id' => $page_id,
            	'type'    => 'widget',
            	'name'    => 'ynmultilisting.profile-listings',
            	'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        		'order'   => 999,
        		'params'  => '{"title":"Listings","titleCount":true}',
      		));

    	}
  	}

	protected function _addCreateStep2Page() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_index_create-step-two')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_index_create-step-two',
                'displayname' => 'Multiple Listing Create New Listing Main Page',
                'title' => 'Multiple Listing Create New Listing Main Page',
                'description' => 'Multiple Listing Create New Listing Main Page',
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
            
			//Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

	/**
     * @author LONGL - SE Dev
     * setup layout for Browse Review Page
     */
	public function _addBrowseReviewPage()
    {
    	$db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_review_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_review_index',
                'displayname' => 'Multiple Listing Browse Review Page',
                'title' => 'Multiple Listing Browse Review Page',
                'description' => 'This page show the listings on browse review page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));  

            //Insert search review widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.search-review',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }
    
	/**
     * @author LONGL - SE Dev
     * setup layout for My Listings Page
     */
	public function _addManagePage()
    {
    	$db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_index_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_index_manage',
                'displayname' => 'Multiple Listing Manage Page',
                'title' => 'Multiple Listing Manage Page',
                'description' => 'This page show the listings on my listings page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            )); 

            //Insert search listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.search-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }
    
    /**
     * @author LONGL - SE Dev
     * setup layout for Home Page
     */
	public function _addBrowsePage()
    {
    	$db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_index_browse')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_index_browse',
                'displayname' => 'Multiple Listing Browse Page',
                'title' => 'Multiple Listing Browse Page',
                'description' => 'This page show the listings on search page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert browse listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.browse-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));

            //Insert highlight listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.highlight-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            	'params' => '{"title":"Highlight Listing"}',
            ));
            
            //Insert top listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.top-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            	'params' => '{"title":"Top Listing"}',
            ));
            
            //Insert search listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.search-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
            ));
            
            //Insert list categories widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.list-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
            	'params' => '{"title":"List Categories"}',
            ));
            
            //Insert quick link categories widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.quick-link-link',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
            	'params' => '{"title":"Quick Links Link Only", "show_name":"1", "show_onwer":"1", "show_category":"1", "show_price":"1", "change_speed":"5"}',
            ));
        }
    }
    
    /**
     * @author LONGL - SE Dev
     * setup layout for Home Page
     */
	public function _addHomePage()
    {
    	$db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_index_index',
                'displayname' => 'Multiple Listing Home Page',
                'title' => 'Multiple Listing Home Page',
                'description' => 'This page show the listings on home page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert featured listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.featured-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert quick link widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.quick-link-slide',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"title":""}',
            )); 

            //Insert middle categories widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.middle-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"Shop by Category"}',
            ));
            
            // Insert tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 4,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $main_container_id = $db -> lastInsertId();
            
            //Insert recent listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.recent-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 1,
                'params' => '{"title":"Recent Listing"}',
            ));
            
            //Insert most reviewed listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.most-reviewed-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 2,
                'params' => '{"title":"Most Reviewed"}',
            ));
            
            //Insert most rated listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.most-rated-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 3,
                'params' => '{"title":"High Rated"}',
            ));
            
            //Insert search listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.search-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert profile quick create link
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.subscribe-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            	'params' => '{"title":"Subscribe Listing"}',
            ));
            
            //Insert most viewed listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.most-viewed-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Most Viewed"}',
            ));
            
            //Insert most liked listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.most-liked-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Most Liked"}',
            ));
            
            //Insert most discussed listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.most-discussed-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Most Discussed"}',
            ));
            
            //Insert most commented listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.most-commented-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 6,
                'params' => '{"title":"Most Commented"}',
            ));
            
            //Insert most recent review widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.recent-review',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 7,
                'params' => '{"title":"Recent Reviews"}',
            ));
            
            //Insert most recent review widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 8,
                'params' => '{"title":"Tags"}',
            ));                      
        }
    }
    
    //HoangND on top
    protected function _addAllWishListPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_wishlist_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_wishlist_index',
                'displayname' => 'Multiple Listing All Wish Lists Page',
                'title' => 'Multiple Listing All Wish Lists Page',
                'description' => 'This page show all Wish Lists',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.wishlist-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert search wish list widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.wishlist-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
			//Insert create wish list link widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.wishlist-create-link',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
        }
    }
    
    protected function _addWishListDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_wishlist_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_wishlist_view',
                'displayname' => 'Multiple Listing Wish List Detail Page',
                'title' => 'Multiple Listing Wish List Detail Page',
                'description' => 'This page show Wish List Detail',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
    
    protected function _addMyWishListPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_wishlist_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_wishlist_manage',
                'displayname' => 'Multiple Listing My Wish Lists Page',
                'title' => 'Multiple Listing My Wish Lists Page',
                'description' => 'This page show My Wish Lists',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
    
    protected function _addComparisonPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_compare_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_compare_index',
                'displayname' => 'Multiple Listing Comparison Page',
                'title' => 'Multiple Listing Comparison Page',
                'description' => 'This page show Comparison Page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

    protected function _addFaqsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_faqs_index',
                'displayname' => 'Multiple Listing FAQs Page',
                'title' => 'Multiple Listing FAQs Page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

	protected function _addPackagePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_index_package')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_index_package',
                'displayname' => 'Multiple Listing Choose Package Page',
                'title' => 'Multiple Listing Choose Package Page',
                'description' => 'Multiple Listing Choose Package Page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

	protected function _addCreatePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_index_create')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_index_create',
                'displayname' => 'Multiple Listing Create Page',
                'title' => 'Multiple Listing Create Page',
                'description' => 'Multiple Listing Create Page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
	
	protected function _addEditPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_listing_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_listing_edit',
                'displayname' => 'Multiple Listing Edit Page',
                'title' => 'Multiple Listing Edit Page',
                'description' => 'Multiple Listing Edit Page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

	protected function _addListingDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_profile_index',
                'displayname' => 'Multiple Listing Detail Page',
                'title' => 'Multiple Listing Detail Page',
                'description' => 'This page show the informations of Multiple Listing',
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
            
             //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
			//Insert cover other styles 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-cover-styles',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 3,
            ));  
			
            //Insert cover style 1
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-cover-style1',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));  
			
            // Insert tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $main_container_id = $db -> lastInsertId();
            
             // Insert related listings widget
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-related-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"Related Listings"}',
            ));
            
            //Insert profile info widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 1,
                'params' => '{"title":"Info"}',
            ));
            
            //Insert activity widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'activity.feed',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 2,
                'params' => '{"title":"Activity"}',
            ));
            
            //Insert review widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-reviews',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 3,
                'params' => '{"title":"Reviews", "titleCount": true}',
            ));
            
			//Insert profile photo widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-albums',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 4,
                'params' => '{"title":"Photos", "titleCount": true}',
            ));
			
            //Insert profile video widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-videos',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 5,
                'params' => '{"title":"Videos", "titleCount": true}',
            ));
            
			//Insert profile discussion widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-discussions',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 6,
                'params' => '{"title":"Discussions", "titleCount": true}',
            ));
			
			//Insert location widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-location',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Location"}',
            ));
			
			//Insert about widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-profile-about',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"About Us"}',
            ));
			
			//Insert tag widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Tags"}',
            ));
        }
    }

    protected function _addImportFromFilePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_import_file')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_import_file',
                'displayname' => 'Multiple Listing Import From File Page',
                'title' => 'Multiple Listing Import From File Page',
                'description' => 'This page for importing listings from files',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

    protected function _addImportFromModulePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_import_module')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_import_module',
                'displayname' => 'Multiple Listing Import From Module Page',
                'title' => 'Multiple Listing Import From Module Page',
                'description' => 'This page for importing listings from modules',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

	protected function _addImportFromModuleExecPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_import_module-exec')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_import_module-exec',
                'displayname' => 'Multiple Listing Import From Module Execution Page',
                'title' => 'Multiple Listing Import From Module Execution Page',
                'description' => 'This page for importing listings from modules',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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

	protected function _addReviewDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmultilisting_review_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmultilisting_review_view',
                'displayname' => 'Multiple Listing Review Detail Page',
                'title' => 'Multiple Listing Review Detail Page',
                'description' => 'Multiple Listing Review Detail Page',
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
            
            //Insert listing types menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.listing-type-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmultilisting.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
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
}