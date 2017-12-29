<?php
class Ynadvsearch_Api_Core extends  Core_Api_Abstract {
    
    public function countItemByItemType($item_type)
    {
        $table = Engine_Api::_() -> getItemTable($item_type);
        $items = $table -> fetchAll($table->select());
        return count($items);
    }
    
    public function checkYouNetPlugin($name) {
        return Engine_Api::_() -> hasModuleBootstrap($name);
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
    
    public function addBlogPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_blog-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_blog-search',
                'displayname' => 'YouNet Advanced Blogs Search Page',
                'title' => 'Blogs Search Page',
                'description' => 'This page display Blogs Search Page',
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
                'name' => 'ynadvsearch.blog-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));
            
            //Insert search form YN
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.yn-blog-search',
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
    
    public function addClassifiedPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_classified-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_classified-search',
                'displayname' => 'YouNet Advanced Classifieds Search Page',
                'title' => 'Classifieds Search Page',
                'description' => 'This page display Classifieds Search Page',
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
                'name' => 'classified.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addPollPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_poll-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_poll-search',
                'displayname' => 'YouNet Advanced Polls Search Page',
                'title' => 'Polls Search Page',
                'description' => 'This page display Polls Search Page',
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
                'name' => 'ynadvsearch.poll-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addAuctionPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_auction-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_auction-search',
                'displayname' => 'YouNet Advanced Auctions Search Page',
                'title' => 'Auctions Search Page',
                'description' => 'This page display Auctions Search Page',
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
                'name' => 'ynauction.search-ynauctions',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addContestPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_contest-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_contest-search',
                'displayname' => 'YouNet Advanced Contests Search Page',
                'title' => 'Contests Search Page',
                'description' => 'This page display Contests Search Page',
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
                'name' => 'yncontest.search-contest',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addForumPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_forum-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_forum-search',
                'displayname' => 'YouNet Advanced Forum Topics Search Page',
                'title' => 'Forum Topics Search Page',
                'description' => 'This page display Forum Topics Search Page',
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
                'name' => 'ynadvsearch.forum-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addWikiPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_wiki-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_wiki-search',
                'displayname' => 'YouNet Advanced Wikis Search Page',
                'title' => 'Wikis Search Page',
                'description' => 'This page display Wikis Search Page',
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
                'name' => 'ynadvsearch.wiki-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addStoreStorePage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_store-store-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_store-store-search',
                'displayname' => 'YouNet Advanced Store Stores Search Page',
                'title' => 'Store Stores Search Page',
                'description' => 'This page display Store Stores Search Page',
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
                'name' => 'ynadvsearch.store-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addStoreProductPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_store-product-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_store-product-search',
                'displayname' => 'YouNet Advanced Store Products Search Page',
                'title' => 'Store Products Search Page',
                'description' => 'This page display Store Products Search Page',
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
                'name' => 'socialstore.search-product',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

    public function addVideoPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_video-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_video-search',
                'displayname' => 'YouNet Advanced Videos Search Page',
                'title' => 'Videos Search Page',
                'description' => 'This page display Videos Search Page',
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
                'name' => 'ynadvsearch.video-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));
            
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynvideo.browse-search',
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
    
    public function addPhotoPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_photo-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_photo-search',
                'displayname' => 'YouNet Advanced Photos Search Page',
                'title' => 'Photos Search Page',
                'description' => 'This page display Photos Search Page',
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
                'name' => 'advalbum.photos-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

    public function addFileSharingPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_filesharing-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_filesharing-search',
                'displayname' => 'YouNet Advanced FileSharings Search Page',
                'title' => 'FileSharings Search Page',
                'description' => 'This page display FileSharings Search Page',
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
                'name' => 'ynadvsearch.filesharing-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addGroupBuyPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_groupbuy-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_groupbuy-search',
                'displayname' => 'YouNet Advanced GroupBuys Search Page',
                'title' => 'GroupBuys Search Page',
                'description' => 'This page display GroupBuys Search Page',
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
                'name' => 'groupbuy.search-deals',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

    public function addMusicPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_music-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_music-search',
                'displayname' => 'YouNet Advanced Musics Search Page',
                'title' => 'Musics Search Page',
                'description' => 'This page display Musics Search Page',
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
                'name' => 'ynadvsearch.music-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addMp3MusicPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_mp-music-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
             $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_mp-music-search',
                'displayname' => 'YouNet Advanced Mp3Musics Search Page',
                'title' => 'Mp3Musics Search Page',
                'description' => 'This page display Mp3Musics Search Page',
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
            
            //Insert search form
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.yn-music-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));         
        }
    }
    
    public function addMp3MusicAlbumsPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_mp-musicalbums-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
             $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_mp-musicalbums-search',
                'displayname' => 'YouNet Advanced Mp3Music Albums Search Page',
                'title' => 'Mp3Music Albums Search Page',
                'description' => 'This page display Mp3Music Albums Search Page',
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
            
            //Insert search form
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.yn-music-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
            
            //Insert search result
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'mp3music.browse-albums',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
            
        }
    }
    
    public function addMp3MusicPlaylistsPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_mp-musicplaylists-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
        if(!$page_id) {
              $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_mp-musicplaylists-search',
                'displayname' => 'YouNet Advanced Mp3Music Playlists Search Page',
                'title' => 'Mp3Music Playlists Search Page',
                'description' => 'This page display Mp3Music Playlists Search Page',
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
            
            //Insert search form
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynadvsearch.yn-music-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
            
            //Insert search result
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'mp3music.browse-playlists',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
            
        }
    }

    public function addFundraisingPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_fundraising-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_fundraising-search',
                'displayname' => 'YouNet Advanced Fundraisings Search Page',
                'title' => 'Fundraisings Search Page',
                'description' => 'This page display Fundraisings Search Page',
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
                'name' => 'ynadvsearch.fundraising-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addListingPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_listing-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_listing-search',
                'displayname' => 'YouNet Listings Search Page',
                'title' => 'Listings Search Page',
                'description' => 'This page display Listings Search Page',
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
                'name' => 'ynlistings.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

    public function addJobpostingJobPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_jobposting-job-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_jobposting-job-search',
                'displayname' => 'YouNet Job Posting Jobs Search Page',
                'title' => 'Job Posting Jobs Search Page',
                'description' => 'This page display Job Posting Jobs Search Page',
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
                'name' => 'ynjobposting.job-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    
    public function addJobpostingCompanyPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_jobposting-company-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_jobposting-company-search',
                'displayname' => 'YouNet Job Posting Companies Search Page',
                'title' => 'Job Posting Companies Search Page',
                'description' => 'This page display Job Posting Companies Search Page',
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
                'name' => 'ynjobposting.company-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

    public function addBusinessPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_business-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_business-search',
                'displayname' => 'YouNet Businesses Search Page',
                'title' => 'Businesses Search Page',
                'description' => 'This page display Businesses Search Page',
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
                'name' => 'ynbusinesspages.business-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
	
	public function addResumePage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_resume-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_resume-search',
                'displayname' => 'YouNet Resumes Search Page',
                'title' => 'Resumes Search Page',
                'description' => 'This page display Resumes Search Page',
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
                'name' => 'ynresume.resume-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

	public function addMultilistingPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_multilisting-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_multilisting-search',
                'displayname' => 'YouNet Multiple Listings Search Page',
                'title' => 'Multiple Listings Search Page',
                'description' => 'This page display Multiple Listings Search Page',
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
                'name' => 'ynadvsearch.multilisting-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
	
	public function addSocialmusicPage() {
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_socialmusic-search')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_socialmusic-search',
                'displayname' => 'YouNet Social Music Search Page',
                'title' => 'Social Music Search Page',
                'description' => 'This page display Social Music Search Page',
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
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

    public function addUltimateVideoPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_ultimatevideo-search')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_ultimatevideo-search',
                'displayname' => 'YouNet Ultimate Video Search Page',
                'title' => 'Ultimate Video Search Page',
                'description' => 'This page display Ultimate Video Search Page',
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
                'name' => 'ynultimatevideo.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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

    public function addVideochannelPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynadvsearch_search_videochannel-search')
            ->limit(1)
            ->query()
            ->fetchColumn();

        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynadvsearch_search_videochannel-search',
                'displayname' => 'YouNet Video Channel Search Page',
                'title' => 'Video Channel Search Page',
                'description' => 'This page display Video Channel Search Page',
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
                'name' => 'ynvideochannel.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
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
    public function getActionOfType($type) {
        switch ($type) {
            case 'advalbum_photo':
                return 'photo-search';
                break;
            case 'forum_topic':
                return 'forum-search';
                break;
            case 'groupbuy_deal':
                return 'groupbuy-search';
                break;
            case 'mp3music_playlist':
                return 'mp-music-search';
                break;
            case 'music_playlist':
                return 'music-search';
                break;
            case 'social_store':
                return 'store-store-search';
                break;
            case 'social_product':
                return 'store-product-search';
                break;
            case 'ynauction_product':
                return 'auction-search';
                break;
            case 'yncontest_contest':
                return 'contest-search';
                break;
            case 'ynfilesharing_folder':
                return 'filesharing-search';
                break;
            case 'ynfundraising_campaign':
                return 'fundraising-search';
                break;
            case 'ynwiki_page':
                return 'wiki-search';
                break;
            case 'ynlistings_listing':
                return 'listing-search';
                break;
            case 'ynjobposting_job':
                return 'jobposting-job-search';
                break;
            case 'ynjobposting_company':
                return 'jobposting-company-search';
                break;
            case 'ynbusinesspages_business':
                return 'business-search';
                break;
			case 'ynresume_resume':
                return 'resume-search';
                break;
			case 'ynmultilisting_listing':
                return 'multilisting-search';
                break;
			case 'ynmusic_song':
                return 'socialmusic-search';
                break;
			case 'ynultimatevideo_video':
                return 'ultimatevideo-search';
                break;
			case 'ynvideochannel_video':
                return 'ynvideochannel-search';
                break;

            default:
                return $type.'-search';
        }
    }
    
    public function getTypesOfAction($action) {
        $types = array();
        switch ($action) {
            case 'user-search':
                array_push($types, 'user');  
                break;
            
            case 'blog-search':
                array_push($types, 'blog');  
                break;
            
            case 'classified-search':
                array_push($types, 'classified');
                array_push($types, 'classified_album');  
                break;
            
            case 'poll-search':
                array_push($types, 'poll');  
                break; 
            
            case 'auction-search':
                array_push($types, 'ynauction_product');  
                break;
                
            case 'contest-search':
                array_push($types, 'yncontest_contest');  
                break;
            
            case 'forum-search':
                array_push($types, 'forum'); 
                array_push($types, 'forum_topic'); 
                array_push($types, 'forum_post');  
                break;
            
            case 'group-search':
                array_push($types, 'group');  
                break; 
            
            case 'wiki-search':
                array_push($types, 'ynwiki_page');  
                break;
                
            case 'store-store-search':
                array_push($types, 'social_store');  
                break;
            
            case 'store-product-search':
                array_push($types, 'social_product');  
                break;
            
            case 'event-search':
                array_push($types, 'event');  
                break;
            
            case 'album-search':
                array_push($types, 'album');
                array_push($types, 'advalbum_album');  
                break; 
            
            case 'photo-search':
                array_push($types, 'advalbum_photo');  
                break;
                
            case 'music-search':
                array_push($types, 'music_playlist');
                array_push($types, 'music_playlist_song');  
                break; 
            
            case 'fundraising-search':
                array_push($types, 'ynfundraising_campaign');  
                break;
                
            case 'mp-music-search':
                array_push($types, 'mp3music_playlist');
                array_push($types, 'mp3music_album');
                array_push($types, 'mp3music_album_song');  
                break;
            
            case 'groupbuy-search':
                array_push($types, 'groupbuy_deal');
                array_push($types, 'groupbuy_album');  
                break;
            
            case 'filesharing-search':
                array_push($types, 'folder');
                array_push($types, 'file'); 
                array_push($types, 'ynfilesharing_folder');  
                break;
                
            case 'video-search':
                array_push($types, 'video');  
                break;
            case 'listing-search':
                array_push($types, 'ynlistings_album'); 
                array_push($types, 'ynlistings_category'); 
                array_push($types, 'ynlistings_listing'); 
                array_push($types, 'ynlistings_faq');  
                break;
            case 'jobposting-job-search':
                array_push($types, 'ynjobposting_job'); 
                break;
           	case 'jobposting-company-search':
                array_push($types, 'ynjobposting_company'); 
                break;  
           	case 'business-search':
                array_push($types, 'ynbusinesspages_business'); 
                break;  
		  	case 'resume-search':
                array_push($types, 'ynresume_resume'); 
                break;   
			case 'multilisting-search':
                array_push($types, 'ynmultilisting_listing'); 
                break;
			case 'socialmusic-search':
                array_push($types, 'ynmusic_song');
				array_push($types, 'ynmusic_album');
				array_push($types, 'ynmusic_playlist');
				array_push($types, 'ynmusic_artist'); 
                break;                
			case 'ultimatevideo-search':
                array_push($types, 'ynultimatevideo_video');
                break;
			case 'videochannel-search':
                array_push($types, 'ynvideochannel_video');
                break;
        }
        return $types;
    }

    public function originalStyle($action) {
        $listItem = $this->getTypesOfAction($action);
        $table = Engine_Api::_()->getDbTable('contenttypes','ynadvsearch');
        $select = $table->select()->where('type IN (?)', $listItem);
        $result = $table->fetchRow($select);
        if ($result) {
            return $result->original_style;
        }
        else {
            return false;
        }
    }
    
    public function hasOriginal($type) {
        switch ($type) {
            case 'poll':
            case 'classified':
            case 'ynauction_product':
            case 'yncontest_contest':
            case 'social_store':
            case 'social_product':    
            case 'groupbuy_deal':
            case 'ynwiki_page':
            case 'ynfundraising_campaign':
            case 'ynfilesharing_folder':
                return true;
                break;
            
            default:
                return false;
                break;
             
        }
    }
    
    public function removeContentPage($content_type) {
        $action = $this->getActionOfType($content_type);
        $name = array();
        if ($action == 'mp-music-search') {
            array_push($name, 'ynadvsearch_search_mp-music-search');
            array_push($name, 'ynadvsearch_search_mp-musicalbums-search');
            array_push($name, 'ynadvsearch_search_mp-musicplaylists-search');
        }
        else {
            array_push($name, 'ynadvsearch_search_'.$action);
        }
        if (count($name) > 0) {
            $table = Engine_Api::_()->getDbTable('pages', 'core');
            $select = $table->select()->where('name IN (?)', $name);
            $pages = $table->fetchAll($select);
            foreach ($pages as $page) {
                $page->delete();
            }
        }
    }
    
}
