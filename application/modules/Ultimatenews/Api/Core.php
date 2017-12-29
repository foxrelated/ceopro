<?php
class Ultimatenews_Api_Core extends Core_Api_Abstract 
{
    /**
     * check version of socicalenginer using
     * 
     * @return bool
     */     
    public function checkVersionSE() {
        $c_table = Engine_Api::_()->getDbTable('modules', 'core');
        $c_name = $c_table->info('name');
        $select = $c_table->select()
                        ->where("$c_name.name LIKE ?", 'core')->limit(1);

        $row = $c_table->fetchRow($select)->toArray();
        $strVersion = $row['version'];
        $intVersion = (int) str_replace('.', '', $strVersion);
        return $intVersion >= 410 ? true : false;
    }
    /**
     * @params: array()
     * get all feeds
     * 
     * @return array feeds
     */
    public function getAllCategories($params = array(), $returnModel = false) {
        $table = Engine_Api::_()->getDbtable('categories', 'ultimatenews');
        $select = $table->select();
        //content id
        if (isset($params['category_id'])) {
            $select->where('category_id = ?', $params['category_id']);
        }
        if (isset($params['category_active']))
            $select->where('is_active = ?', $params['category_active']);
        
        if (isset($params['approved']))
            $select->where('approved = ?', $params['approved']);
        

        if (isset($params['category_parent']) && $params['category_parent'] != -1)
            $select->where('category_parent_id = ?', $params['category_parent']);
        
        if (isset($params['limit']) && $params['limit'] > 0) {
            $select->limit($params['limit']);
        }

        $select->order(' updated_at ASC ');

        $row = $table->fetchAll($select);
        
        return ($returnModel) ? $row : $row->toArray();
    }
    /**
     * @params: array()
     * get all categories
     * 
     * @return array categories
     */
    public function getAllCategoryparents($params = array()) {
        $table = Engine_Api::_()->getDbtable('categoryparents', 'ultimatenews');
        $select = $table->select();
        //content id
        if (isset($params['category_id'])) {
            $select->where('category_id = ?', $params['category_id']);
        }
        if (isset($params['category_active']))
            $select->where('is_active = ?', $params['category_active']);
        //if (isset)
        $row = $table->fetchAll($select);
        return $row->toArray();
    }
    /**
     * @where: string
     * get feed by category_id
     * 
     * @return array feeds
     */
    public function getCategoriesById($where) {
        $table = Engine_Api::_()->getDbtable('categories', 'ultimatenews');
        $select = $table->select();
        //content id
        if (!empty($where)) {
            $select->where('category_id IN ' . $where);
        }
        $select->where('is_active = ?', 1);
        $select->where('approved = ?', 1);
        $row = $table->fetchAll($select);
        return $row->toArray();
    }
    /**
     * get feed select to show selection box
     * 
     * @return  options categories
     */
    public function getAllCategoriesSelect() {
        $table = Engine_Api::_()->getDbtable('categories', 'ultimatenews');
        $select = $table->select();

        $row = $table->fetchAll($select);
        $row = $row->toArray();
        $categories = array();
        $cat = "";
        $categories[0] = "All Categories";
        foreach ($row as $item) {
            $categories[$item['category_id']] = $item['category_name'];
        }
        return $categories;
    }
    /**
     * @level_id: int
     * get admin account
     * 
     * @return array admin accounts
     */
    public function getAdminAccount($level = 1) {
        $table = Engine_Api::_()->getItemTable('users');
        $select = $table->select();

        $select->where('level_id = ?', $level);
        $select->limit(' 1');


        $row = $table->fetchAll($select);
        return $row->toArray();
    }
    /**
     * 
     * get time frame
     * 
     * @return array time frame
     */
    public function getTimeframe() {
        $table = Engine_Api::_()->getDbtable('timeframe', 'ultimatenews');
        $select = $table->select();
        $select->limit(' 1');
        $row = $table->fetchAll($select);
        return $row->toArray();
    }
    /**
     * @params: array()
     * get all contents
     * 
     * @return array contents
     */
    public function getAllContent($params = array()) {
        $table = Engine_Api::_()->getDbtable('contents', 'ultimatenews');
        $select = $table->select();

        //content id
        if (isset($params['content_id'])) {
            $select->where('content_id = ?', $params['content_id']);
        }

        //Ultimatenews_item_id
        if (isset($params['ultimatenews_item_id'])) {
            $select->where('ultimatenews_item_id = ?', $params['ultimatenews_item_id']);
        }

        // Category
        if (isset($params['category_id']) && $params['category_id'] > 0) {
            $select->where('category_id = ?', $params['category_id']);
        }

        //link detail
        if (isset($params['link_detail'])) {
            $select->where('link_detail = ?', $params['link_detail']);
        }
        //link title
        if (isset($params['title'])) {
            $select->where('title = ?', $params['title']);
        }
        // Limit
        if (isset($params['limit']) && $params['limit'] > 0) {
            $select->limit($params['limit']);
        }

        $select->order(' category_id DESC ');

        $row = $table->fetchAll($select);
        return $row->toArray();
    }

    public function getCategoriesByNewsList($params) {
        $table = Engine_Api::_()->getDbTable('contents', 'ultimatenews');

        $select = $table->select()->from('engine4_ultimatenews_contents')->setIntegrityCheck(false);
        $select->joinLeft(
            "engine4_ultimatenews_categories", 
            "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id", 
            array(
                'logo' => 'engine4_ultimatenews_categories.category_logo', 
                'logo_icon' => 'engine4_ultimatenews_categories.logo', 
                'display_logo' => 'engine4_ultimatenews_categories.display_logo', 
                'mini_logo' => 'engine4_ultimatenews_categories.mini_logo',
                'feed_name'=>'engine4_ultimatenews_categories.category_name',
                'feed_url'=>'engine4_ultimatenews_categories.url_resource'
            ));
        if (isset($params['checkcomment'])) {
            $select->where("engine4_ultimatenews_contents.content_id IN (SELECT resource_id FROM engine4_core_comments WHERE engine4_core_comments.resource_type='ultimatenews_content' AND engine4_core_comments.resource_id = engine4_ultimatenews_contents.content_id)");
        }

        if (isset($params['getcommment'])) {

            $select->joinLeft("engine4_core_comments", "engine4_core_comments.resource_id= engine4_ultimatenews_contents.content_id AND engine4_core_comments.resource_type = \"ultimatenews_content\" ", array('total_comment' => "count('engine4_ultimatenews_contents.content_id')", 'resource_id' => 'engine4_core_comments.resource_id'));
            $select->group('engine4_ultimatenews_contents.content_id');
        }

        //content id
        if (isset($params['content_id'])) {
            $select->where('engine4_ultimatenews_contents.content_id = ?', $params['content_id']);
        }

        //link
        //content id
        if (isset($params['link'])) {
            $select->where('engine4_ultimatenews_contents.link = ?', $params['link']);
        }

        // is active
        if (isset($params['is_active'])) {
            $select->where('engine4_ultimatenews_categories.is_active = ?', $params['is_active']);
        }
        $timezone_server_H = date('H');
        $timezone_server_i = date('i');
        $timezone_server_s = date('s');
        $timezone_server = $timezone_server_H * 3600 + $timezone_server_i * 60 + $timezone_server_s;

        $oldTz = date_default_timezone_get();
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone_viewer = $timezone_server;
        if ($viewer->getIdentity() > 0) {
            date_default_timezone_set($viewer->timezone);
            $timezone_viewer_H = date('H');
            $timezone_viewer_i = date('i');
            $timezone_viewer_s = date('s');
            $timezone_viewer = $timezone_viewer_H * 3600 + $timezone_viewer_i * 60 + $timezone_viewer_s;
        }
        $subtimezone = ($timezone_server - $timezone_viewer);
        date_default_timezone_set($oldTz);
        $subtimezone = 0;
        if (isset($params['start_date']) && $params['start_date'] != '') {
            $start_date_ = $params['start_date'];
            $start_date = strtotime($start_date_) + $subtimezone;
            if ($start_date != $subtimezone)
                $select->where("engine4_ultimatenews_contents.pubDate >= $start_date ");
        }
        if (isset($params['end_date']) && $params['end_date'] != '') {
            $end_date_ = $params['end_date'];
            $end_date = strtotime($end_date_) + $subtimezone;

            if ($end_date != $subtimezone)
                $select->where("engine4_ultimatenews_contents.pubDate <= $end_date ");
        }
        
        // Category parent for listing news
        if ((!isset($params['category_id']) || $params['category_id'] == 0) && isset($params['category_parent']) && $params['category_parent'] != -1) {
            $select->where('engine4_ultimatenews_categories.category_parent_id = ?', $params['category_parent']);
        }
        
        // Category Parent
        if (isset($params['categoryparent']) && $params['categoryparent'] != "") {
            $select->where('engine4_ultimatenews_categories.category_parent_id = ?', $params['categoryparent']);
        }
        
        // Category
        if (isset($params['category_id']) && $params['category_id'] != "") {
            $select->where('engine4_ultimatenews_contents.category_id = ?', $params['category_id']);
        }

        // title
        if (!empty($params['title'])) {
            $title = $params['title'];
            $select->where('engine4_ultimatenews_contents.title LIKE ?', "%$title%");
        }
        
    }



    /**
     * @params: array()
     * get all contents for Paginator
     * 
     * @return select object or array contents(listing widget)
     */
    public function getContentsSelect($params = array()) {
        $table = Engine_Api::_()->getDbTable('contents', 'ultimatenews');

        $select = $table->select()->from('engine4_ultimatenews_contents')->setIntegrityCheck(false);
        $select->joinLeft(
            "engine4_ultimatenews_categories", 
            "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id", 
            array(
                'logo' => 'engine4_ultimatenews_categories.category_logo', 
                'logo_icon' => 'engine4_ultimatenews_categories.logo', 
                'display_logo' => 'engine4_ultimatenews_categories.display_logo', 
                'mini_logo' => 'engine4_ultimatenews_categories.mini_logo',
                'feed_name'=>'engine4_ultimatenews_categories.category_name',
                'feed_url'=>'engine4_ultimatenews_categories.url_resource'
            ));
        if (isset($params['checkcomment'])) {
            $select->where("engine4_ultimatenews_contents.content_id IN (SELECT resource_id FROM engine4_core_comments WHERE engine4_core_comments.resource_type='ultimatenews_content' AND engine4_core_comments.resource_id = engine4_ultimatenews_contents.content_id)");
        }

        if (isset($params['getcommment'])) {

            $select->joinLeft("engine4_core_comments", "engine4_core_comments.resource_id= engine4_ultimatenews_contents.content_id AND engine4_core_comments.resource_type = \"ultimatenews_content\" ", array('total_comment' => "count('engine4_ultimatenews_contents.content_id')", 'resource_id' => 'engine4_core_comments.resource_id'));
            $select->group('engine4_ultimatenews_contents.content_id');
        }

        //content id
        if (isset($params['content_id'])) {
            $select->where('engine4_ultimatenews_contents.content_id = ?', $params['content_id']);
        }

        //link
        //content id
        if (isset($params['link'])) {
            $select->where('engine4_ultimatenews_contents.link = ?', $params['link']);
        }

        // is active
        if (isset($params['is_active'])) 
        {
            $select->where('engine4_ultimatenews_categories.is_active = ?', $params['is_active']);
            $select->where('engine4_ultimatenews_contents.approved = 1');
        }
        $timezone_server_H = date('H');
        $timezone_server_i = date('i');
        $timezone_server_s = date('s');
        $timezone_server = $timezone_server_H * 3600 + $timezone_server_i * 60 + $timezone_server_s;

        $oldTz = date_default_timezone_get();
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone_viewer = $timezone_server;
        if ($viewer->getIdentity() > 0) {
            date_default_timezone_set($viewer->timezone);
            $timezone_viewer_H = date('H');
            $timezone_viewer_i = date('i');
            $timezone_viewer_s = date('s');
            $timezone_viewer = $timezone_viewer_H * 3600 + $timezone_viewer_i * 60 + $timezone_viewer_s;
        }
        $subtimezone = ($timezone_server - $timezone_viewer);
        date_default_timezone_set($oldTz);
        $subtimezone = 0;
        if (isset($params['start_date']) && $params['start_date'] != '') {
            $start_date_ = $params['start_date'];
            $start_date = strtotime($start_date_) + $subtimezone;
            if ($start_date != $subtimezone)
                $select->where("engine4_ultimatenews_contents.pubDate >= $start_date ");
        }
        if (isset($params['end_date']) && $params['end_date'] != '') {
            $end_date_ = $params['end_date'];
            $end_date = strtotime($end_date_) + $subtimezone;

            if ($end_date != $subtimezone)
                $select->where("engine4_ultimatenews_contents.pubDate <= $end_date ");
        }
        
        // Category parent for listing news
        if ((!isset($params['category_id']) || $params['category_id'] == 0) && isset($params['category_parent']) && $params['category_parent'] != -1) {
            $select->where('engine4_ultimatenews_categories.category_parent_id = ?', $params['category_parent']);
        }
        
        // Category Parent
        if (isset($params['categoryparent']) && $params['categoryparent'] != "") {
            $select->where('engine4_ultimatenews_categories.category_parent_id = ?', $params['categoryparent']);
        }
        
        // RSS Feed
        if (isset($params['category_id']) && $params['category_id'] != "") {
            $select->where('engine4_ultimatenews_contents.category_id = ?', $params['category_id']);
        }
        
        // Owner
        if (isset($params['owner_id']) && $params['owner_id']) {
            $select->where('engine4_ultimatenews_contents.owner_id = ?', $params['owner_id']);
        }
        
        // Favorite
        if (isset($params['favorite_owner_id']) && $params['favorite_owner_id']) 
        {
            $select -> where("engine4_ultimatenews_favorites.user_id = ?", $params['favorite_owner_id']);
            $select->join('engine4_ultimatenews_favorites', 'engine4_ultimatenews_favorites.content_id = engine4_ultimatenews_contents.content_id','');
        }

        // title
        if (!empty($params['title'])) {
            $title = $params['title'];
            $select->where('engine4_ultimatenews_contents.title LIKE ?', "%$title%");
        }
        //search
        if (!empty($params['search'])) {
            $select->where('engine4_ultimatenews_contents.title LIKE ? OR description LIKE ? OR content LIKE ?', $params['search'], $params['search'], $params['search']);
        }
        //check image
        if (isset($params['image']) && $params['image'] == 'yes') {
            $select->where('engine4_ultimatenews_contents.image <> ""');
        }
        
          // Get Tagmaps table
        $tags_table = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tags_name = $tags_table->info('name');
        //Tag filter
        if( !empty($params['tag_id']) )
        {
          $select
            ->joinLeft($tags_name, "$tags_name.resource_id = engine4_ultimatenews_contents.content_id","")
            ->where($tags_name.'.resource_type = ?', 'ultimatenews_content')
            ->where($tags_name.'.tag_id = ?', $params['tag_id']);
        }
        
        // Order
        if(!empty($params['group']))
        {
            $select->order('engine4_ultimatenews_contents.category_id DESC');
        }
        
        if (isset($params['order']) && $params['order'] != "")
        {
            if(isset($params['direction']) && $params['direction'] != "")
                $select->order($params['order']." ".$params['direction']);
            else {
                $select->order($params['order']);
            }
        }
        else
            $select->order('content_id DESC');
        if(!empty($params['group']))
        {   
            //check and gets 10 news per a feed
            $number_article = 10;
            if (!empty($params['number_article']) && $params['number_article'] > 0)
                $number_article = $params['number_article'];
            $arrs = $table->fetchAll($select);
            $feed_before = 0;
            $arr_temp  = array();
            $count = 0;
            foreach($arrs as $arr)
            {
                $feed_id = $arr->category_id;   
                $count ++;
                if($feed_before != $feed_id)
                {
                    $count = 0;
                }
                if($count < $number_article)
                {
                    $arr_temp[] = $arr;
                }
                $feed_before = $arr->category_id;
            }
            return $arr_temp;
        }
        else
        {
            if (isset($params['limit']) && $params['limit'] > 0) 
            {
                $select->limit($params['limit']);
            }   
            return $select; 
        }
    }
    /**
     * @params: array()
     * get content paginator
     * 
     * @return obj paginator
     */
    public function getContentsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getContentsSelect($params));
    }
    
    
    /**
     * @withFilter: boolean
     * @params: array()
     * get feeds select for Paginator
     * 
     * @return obj select
     */
    public function getCategoriesSelect($withFilter = false, $params = array()) {
        if (!$withFilter) 
        {
            $table = Engine_Api::_()->getItemTable('ultimatenews_category');
            $select = $table->select();
            // Category name
            if (isset($params['title'])) {
                $title = $params['title'];
                $select->where('category_name LIKE ?', "%$title%");
            }
            // Category id
            if (!empty($params['category_id'])) {
                $select->where('category_id = ?', $params['category_id']);
            }
             // Owner id
            if (!empty($params['owner_id'])) {
                $select->where('owner_id = ?', $params['owner_id']);
            }
            if (!empty($params['approved'])) {
                $select->where("approved = 1");
            }
            // Order
            if (isset($params['order']) && $params['order'] != "")
                $select->order($params['order']." ".$params['direction']);
            else
                $select->order('category_id DESC');
            return $select; 
        }
        else {
            $categoryTbl = Engine_Api::_()->getItemTable('ultimatenews_category');
            $categoryTblName = $categoryTbl->info('name');
            
            $contentTbl = new Ultimatenews_Model_DbTable_Contents();
            $contentTblName = $contentTbl->info('name');
            $selectContent = $contentTbl->select()->order('content_id DESC');
            
            $select = $categoryTbl->select()->setIntegrityCheck(false);
            $select->from ( $categoryTblName );
            $select->joinRight($selectContent, "$categoryTblName.category_id = t.category_id", array());
            
            //WHERE
            $timezone_server_H = date('H');
            $timezone_server_i = date('i');
            $timezone_server_s = date('s');
            $timezone_server = $timezone_server_H * 3600 + $timezone_server_i * 60 + $timezone_server_s;
    
            $oldTz = date_default_timezone_get();
            $viewer = Engine_Api::_()->user()->getViewer();
            $timezone_viewer = $timezone_server;
            if ($viewer->getIdentity() > 0) {
                date_default_timezone_set($viewer->timezone);
                $timezone_viewer_H = date('H');
                $timezone_viewer_i = date('i');
                $timezone_viewer_s = date('s');
                $timezone_viewer = $timezone_viewer_H * 3600 + $timezone_viewer_i * 60 + $timezone_viewer_s;
            }
            $subtimezone = ($timezone_server - $timezone_viewer);
            date_default_timezone_set($oldTz);
            $subtimezone = 0;
            if (isset($params['start_date']) && $params['start_date'] != '') {
                $start_date_ = $params['start_date'];
                $start_date = strtotime($start_date_) + $subtimezone;
                if ($start_date != $subtimezone)
                    //$select->where("$contentTblName.pubDate >= $start_date ");
                    $select->where("t.pubDate >= $start_date ");
            }
            
            if (isset($params['end_date']) && $params['end_date'] != '') {
                $end_date_ = $params['end_date'];
                $end_date = strtotime($end_date_) + $subtimezone;
    
                if ($end_date != $subtimezone)
                    $select->where("t.pubDate <= $end_date ");
            }
    
            // Category parent for listing news
            if ((!isset($params['category_id']) || $params['category_id'] == 0) && isset($params['category_parent']) && $params['category_parent'] != -1) {
                $select->where("$categoryTblName.category_parent_id = ?", $params['category_parent']);
            }
            
            // Owner id
            if (!empty($params['owner_id'])) {
                $select->where('$categoryTblName.owner_id = ?', $params['owner_id']);
            }
            
            // Category Parent
            if (isset($params['category_parent']) && $params['category_parent'] != "" && $params['category_parent'] != '-1') {
                $select->where("$categoryTblName.category_parent_id = ?", $params['category_parent']);
            }
            
            // Category
            if (isset($params['category_id']) && $params['category_id'] != "") {
                $select->where("t.category_id = ?", $params['category_id']);
            }

            // title
            if (!empty($params['title'])) {
                $title = $params['title'];
                $select->where("t.title LIKE ?", "%$title%");
            }
            
            // search
            if (!empty($params['search'])) {
                $select->where("title LIKE ? OR description LIKE ? OR content LIKE ?", $params['search'], $params['search'], $params['search']);
            }
            
            $select->where("$categoryTblName.is_active = 1");
            if (!empty($params['approved'])) 
            {
                $select->where("$categoryTblName.approved = 1");
            }
            
            $select->group("{$categoryTblName}.category_id");
            $select->order("t.content_id DESC");
            if (!empty($params['limit']))
                $select->limit($params['limit']);
            return $select;
        }
        
    }
    /**
     * @params: array()
     * get categories select for Paginator
     * 
     * @return obj select
     */
    public function getCategoryparentsSelect($params = array()) {
        $table = Engine_Api::_()->getItemTable('ultimatenews_categoryparent');
        $select = $table->select()->from('engine4_ultimatenews_categoryparents')->setIntegrityCheck(false)
        ->joinLeft("engine4_ultimatenews_categories","engine4_ultimatenews_categories.category_parent_id = engine4_ultimatenews_categoryparents.category_id","count(engine4_ultimatenews_categories.category_id) as total_feed");
        if (isset($params['title'])) {
            $title = $params['title'];
            $select->where('engine4_ultimatenews_categoryparents.category_name LIKE ?', "%$title%");
        }
        // Category id
        if (!empty($params['category_id'])) {
            $select->where('engine4_ultimatenews_categoryparents.category_id = ?', $params['category_id']);
        }
        if (isset($params['category_active']))
            $select->where('engine4_ultimatenews_categoryparents.is_active = ?', $params['category_active']);
        $select->group('engine4_ultimatenews_categoryparents.category_id');
        // Order
       if (isset($params['order']) && $params['order'] != "")
       {
            if($params['order'] != 'total_feed')
                $select->order("engine4_ultimatenews_categoryparents.".$params['order']." ".$params['direction']);
            else
                $select->order($params['order']." ".$params['direction']);
       }
        else
            $select->order('engine4_ultimatenews_categoryparents.category_id DESC');
        return $select;
    }
    
    /**
     * @params: array()
     * get Feeds Paginator
     * 
     * @return obj paginator
     */
    public function getCategoriesPaginator($params = array()) {
        $withFilter = false;
        return Zend_Paginator::factory($this->getCategoriesSelect($withFilter, $params));
    }
    
    /**
     * @params: array()
     * get Feeds Paginator
     * 
     * @return obj paginator
     */
    public function getCategoriesByNewsFilter($params = array()) {
        $withFilter = true;
        return Zend_Paginator::factory($this->getCategoriesSelect($withFilter, $params));
    }
    
    
    /**
     * @params: array()
     * get categories Paginator
     * 
     * @return obj paginator
     */
    public function getCategoryparentsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getCategoryparentsSelect($params));
    }
    /**
     * @category_id: int
     * check category parent active
     * 
     * @return bool
     */
    public function checkcategoriesparentinactive($category_id) {
        //Should not allow user active a feed when category of this feed is inactive
        $table = Engine_Api::_()->getDbtable('categoryparents', 'ultimatenews');

        $select = $table->select('engine4_ultimatenews_categoryparents ')->setIntegrityCheck(false)
                ->where('engine4_ultimatenews_categoryparents.category_id = ? ', $category_id)
                ->where('engine4_ultimatenews_categoryparents.is_active = ? ', 0)
                ->limit(1);
        $items = $table->fetchAll($select);
        if (count($items) > 0)
            return true;
        return false;
    }
    /**
     * get admin id for run auto cron job
     * 
     */
     public function getAdminId() 
     {
        $table = Engine_Api::_()->getItemTable('user');
        $select = $table->select()->where('level_id = ?', 1)->limit(1);
        $admin = $table->fetchRow($select);
        return $admin->user_id;
     }
     
    /**
     * @param: string 
     * get data from url
     * @return: string
     */
     
     function getData($url)
     {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
        $data = curl_exec($ch);
        $error_no = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        $start = strpos($data, '{');
        $end = strripos($data, '}');
        $data = substr($data, $start, $end - $start + 1);
        
        if(!$error_no)
        { 
            return $data;         
        } else 
        {
            echo $error; 
          return null;
        } 
    }
     
    function getCurrentHost()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
            ? 'https://'
            : 'http://';
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);
        // use port if non default
        $port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443))
            ? ':' . $parts['port']
            : '';
        $path = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'default');
        $path = str_replace("index.php/","",$path);
        $currentHostSite = $protocol . $parts['host'] . $port . $path;
        return $currentHostSite;
    }
    
    public function saveImg($url, $name, $owner_id = 0) 
    {
       $parent_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();   
        if($owner_id)
        {
            $parent_id = $owner_id;
        }
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array('parent_type' => 'category_logo', 'parent_id' => $parent_id, 'user_id' => $parent_id);

        $check_allow_url_fopen = ini_get('allow_url_fopen');
        if (($check_allow_url_fopen == 'on') || ($check_allow_url_fopen == 'On') || ($check_allow_url_fopen == '1')) {
            $gis = getimagesize($url);
            $type = $gis[2];
            switch($type) {
                case "1" :
                    $imorig = imagecreatefromgif($url);
                    break;
                case "2" :
                    $imorig = imagecreatefromjpeg($url);
                    break;
                case "3" :
                    $imorig = imagecreatefrompng($url);
                    break;
                default :
                    $imorig = imagecreatefromjpeg($url);
            }
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $imorig = imagecreatefromstring($data);
        }

        // Save
        $storage = Engine_Api::_() -> storage();
        $filename = $path . DIRECTORY_SEPARATOR . $name . '.png';

        $im = imagecreatetruecolor(150, 112);
        $x = imagesx($imorig);
        $y = imagesy($imorig);
        if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, 150, 112, $x, $y)) {
            imagejpeg($im, $filename);
        }
        try
        {
            $iMain = $storage -> create($path . '/' . $name . '.png', $params);
        }
        catch(exception $e)
        {}
        @unlink($filename);
        return $iMain;
    }
    
    public function getImageURL($url) 
    {
        if (strpos($url, '-/h') > 0) {
            $type = substr($url, strrpos($url, '.'));
            $image_url = substr($url, strpos($url, '-/h') + 2, strrpos($url, '.') - (strpos($url, '-/h') + 2)) . $type;
            $image_url = str_replace("%3A", ":", $image_url);
            return $image_url;
        } else {
            return $url;
        }
    }
}