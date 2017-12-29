<?php
class Ynadvsearch_SearchController extends Core_Controller_Action_Standard {
    
    public function init() {
        $query = $this->_getParam('query');
        if ($query) {
            $query = strtolower($query);
            $table = Engine_Api::_()->getItemTable('ynadvsearch_keyword');
            $select = $table->select()->where('title = ?', $query);
            $keyword = $table->fetchRow($select);
            $db = Engine_Api::_()->getDbtable('keywords', 'ynadvsearch')->getAdapter();
            $db->beginTransaction();
            try {
                if ($keyword) {
                    $keyword->count++;
                    $now = new DateTime();
                    $keyword->modified_date = $now->format('Y-m-d H:i:s');
                }
                else {
                    $keyword = $table->createRow();
                    $keyword->title = $query;
                }
                $keyword->save();
            }
            catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }       
    
            $db->commit();
        }
        
        $action = $this->_getParam('action');
        if ($action) {
            if ($action == 'mp-musicalbums-search' || $action == 'mp-musicplaylists-search') {
                $action = 'mp-music-search';
            }
            $session = new Zend_Session_Namespace('mobile');
            if ($action != 'index') 
            {
                $originalStyle = Engine_Api::_() -> ynadvsearch() -> originalStyle($action);
                $listType = Engine_Api::_() -> ynadvsearch() -> getTypesOfAction($action);
                $hasOriginal = false;
                foreach ($listType as $type) {
                    $hasOriginal = Engine_Api::_() -> ynadvsearch() -> hasOriginal($type);
                    if ($hasOriginal) break;
                }
                $url = $this->view->baseUrl().'/application/modules/Ynadvsearch/externals/styles/'.$action.'.css';
                $responsiveUrl = $this->view->baseUrl().'/application/modules/Ynadvsearch/externals/styles/'.$action.'-responsive.css';
                $mobileUrl = $this->view->baseUrl().'/application/modules/Ynadvsearch/externals/styles/'.$action.'-mobile.css';
                if(!$originalStyle && $hasOriginal) 
                {
                    if ($session -> mobile) {
                        $this->view->headLink()->appendStylesheet($mobileUrl);
                    }
                    else if  ( defined("YNRESPONSIVE_ACTIVE")) {
                        $this->view->headLink()->appendStylesheet($responsiveUrl);
                    }
                    else {
                        $this->view->headLink()->appendStylesheet($url);
                    }
                }
            }
        }
    }
       
  public function index2Action()
  {
    $searchApi = Engine_Api::_()->getApi('search', 'ynadvsearch');
    $params = $this->_getAllParams();
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if( !$require_check ) {
      if( !$this->_helper->requireUser()->isValid() ) return;
    }
    $searchModulesTable = new Ynadvsearch_Model_DbTable_Modules;
    $modulesObject = $searchModulesTable->getEnabledModules();
    $modules = $modulesObject->toArray();
    $types = array();
    $page = (int) $this->_getParam('page',1);
    if(isset($params['submit'])) {
        $mods = $params['moduleynsearch'];
        foreach ($mods as $value => $key) {
            foreach($modules as $module) {
                if ($module['name'] == $value) {
                    $module['checked'] = $key;
                    $results[] = $module;
                    break;
                }
            }
            if ($key == 1) {
                $types[] = $searchModulesTable->getTypes($value);
            }
        }
        $page = 1;
        $this->view->modules = $results;
    }
    else {
        if (@$this->_getParam('module_result')) {
            $this->view->modules = $this->_getParam('module_result');
        }
        else {
            $this->view->modules = $modules;
        }
    }
    $this->view->query = $query = (string) @$params['query'];
    $qty = $this->_getParam('qty');
    if (!is_numeric($qty) || $qty <= 0) {
        $qty = 10;
    }
    $this->view->qty = $qty; 
    if (!$types) {
        $types = @$params['type'];
    }
    if (!$types && (@$params['is_search'])) {
        $this->view->checkAll = 1;
        $types = $searchModulesTable->getAllEnabledTypes($modulesObject);
    }
    $this->view->types = $types;
    if( $query ) {
        $this->view->paginator = $paginator = $searchApi->getPaginator($query, $types);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->page = $page;
        $paginator->setItemCountPerPage($qty);

    }
    if (@$params['checkall']) {
        $this->view->checkAll = 1;
    }
  }
  public function getLabelType($type){
    return strtoupper('ITEM_TYPE_' . $type);
  }
  
  public function indexAction() {
    $values = $this->_getAllParams();
    if(key_exists('query', $values))
    {
      Zend_Registry::set('Search_Params', $values['query']);
    }
    $this->_helper->content->setNoRender()->setEnabled();
    
  }

    public function userSearchAction() {
        $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
        if(!$require_check){
            if( !$this->_helper->requireUser()->isValid() ) return;
        }
        $this->_helper->content->setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['displayname'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['displayname'];
        
        $this->view->ynmember_enable = $ynmember_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynmember');
        if ($ynmember_enable) {
            if (!isset($params['itemCountPerPage'])) {
                $params['itemCountPerPage'] = 9;
            }
        }
        $this->view->params = $params;
        if ($ynmember_enable) {
            $this->view->paginator = $paginator = Engine_Api::_()->ynmember()->getMemberPaginator($params);
            $this->view->total_content = Engine_Api::_()->ynmember()->getMemberPaginator(array()) -> getTotalItemCount();
        }
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('user');
        if ($contentType) $this->view->label_content = $contentType->title;
    }
    
    public function blogSearchAction() {
        if (!Engine_Api::_() -> hasItemType('blog')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if( !$this->_helper->requireAuth()->setAuthParams('blog', null, 'view')->isValid() ) return;
        $this->_helper->content->setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
        
        $this->view->ynblog_enable = $ynblog_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynblog');
    }
    
    public function classifiedSearchAction() {
        if (!Engine_Api::_() -> hasItemType('classified')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if( !$this->_helper->requireAuth()->setAuthParams('classified', null, 'view')->isValid() ) return;
        $this->_helper->content->setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
    }
    
    public function pollSearchAction() {
        if (!Engine_Api::_() -> hasItemType('poll')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if( !$this->_helper->requireAuth()->setAuthParams('poll', null, 'view')->isValid() ) return;
        $this->_helper->content->setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
    }
    
    public function auctionSearchAction() {
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynauction')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $params = $this->getRequest()->getPost();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $where =" display_home = '1' AND stop = 0 AND approved = '1'";
        $params['where'] = $where;
        $this -> view -> params = $params;
        
        $this->view->paginator = Engine_Api::_()->ynauction()->getProductsPaginator($params);
        $this->view->total_content = Engine_Api::_()->ynauction()->getProductsPaginator(array('where' => $where)) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynauction_product');
        if ($contentType) $this->view->label_content = $contentType->title;
        
        $this->_helper->content->setEnabled();
    }
    
    public function contestSearchAction() {
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('yncontest')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if (!isset($params['contest_name'])) {
            $params['contest_name'] = '';
        }
        if($this -> _getParam('query'))
        {
            $params['contest_name'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['contest_name'];
        $params['approve_status'] = 'approved';
        $this->view->params = $params;
        
        Zend_Registry::set('contest_search_params', $params);
        $this->view->paginator = Engine_Api::_()->yncontest()->getContestPaginator($params);
        $this->view->total_content = Engine_Api::_()->yncontest()->getContestPaginator(array('approve_status' => 'approved')) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('yncontest_contest');
        if ($contentType) $this->view->label_content = $contentType->title;
        $this -> _helper -> content -> setEnabled();
    }
    
    public function forumSearchAction() {
        if (!Engine_Api::_() -> hasItemType('forum_topic')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
    }
    
    public function groupSearchAction() {
        if (!Engine_Api::_() -> hasItemType('group')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if( !$this->_helper->requireAuth()->setAuthParams('group', null, 'view')->isValid())
            return;
        $this -> _helper -> content -> setEnabled();
        
        $this->view->advgroup_enable = $advgroup_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('advgroup');
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        $params['search'] = 1;
        if ($advgroup_enable) {
            if($this -> _getParam('query'))
            {
                $params['text'] = $this -> _getParam('query');
            }
            $this -> view -> query = $params['text'];
        }
        else {
            if($this -> _getParam('query'))
            {
                $params['search_text'] = $this -> _getParam('query');
            }
            $this -> view -> query = $params['search_text'];
        }
        $this->view->params = $params;
        
        if ($advgroup_enable) {
            $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('group') ->getGroupPaginator($params);
            $this->view->total_content = Engine_Api::_()->getItemTable('group') ->getGroupPaginator(array('search' => 1)) -> getTotalItemCount();
        }
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('group');
        if ($contentType) $this->view->label_content = $contentType->title;
    }
    
    public function wikiSearchAction() {
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynwiki')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if (!isset($params['title'])) {
            $params['title'] = '';
        }
        if($this -> _getParam('query'))
        {
            $params['title'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['title'];
        $this->view->params = $params;
        
        $this -> view -> paginator = $paginator = Engine_Api::_() -> ynwiki() -> getPagesPaginator($params); 
        $this->view->total_content = Engine_Api::_() -> ynwiki() -> getPagesPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynwiki_page');
        if ($contentType) $this->view->label_content = $contentType->title;
    }
    
    public function storeStoreSearchAction() {
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('socialstore')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        $params['view_status'] = 'show';
        $params['approve_status'] = 'approved';
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
        Zend_Registry::set('store_search_params', $params);
        $this -> view -> paginator = Engine_Api::_()->getApi('store','socialstore')->getStoresPaginator($params);
        $this->view->total_content = Engine_Api::_()->getApi('store','socialstore')->getStoresPaginator(array('view_status' => 'show', 'approve_status' => 'approved')) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('social_store');
        if ($contentType) $this->view->label_content = $contentType->title;
        $this -> _helper -> content -> setEnabled();
    }
    
    public function storeProductSearchAction() {
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('socialstore')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        $params['view_status'] = 'show';
        $params['approve_status'] = 'approved';
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
        Zend_Registry::set('product_search_params', $params);
        $this -> view -> paginator = Engine_Api::_()->getApi('product','Socialstore')->getStoreSearchProductsPaginator($params);
        $this->view->total_content = Engine_Api::_()->getApi('product','Socialstore')->getStoreSearchProductsPaginator(array('view_status' => 'show', 'approve_status' => 'approved')) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('social_product');
        if ($contentType) $this->view->label_content = $contentType->title;
        $this -> _helper -> content -> setEnabled();
    }

    public function eventSearchAction() {
        if (!Engine_Api::_() -> hasItemType('event')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams('event', null, 'view') -> isValid())
            return;
        $this -> _helper -> content -> setEnabled();
        $this->view->ynevent_enable = $ynevent_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynevent');
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if ($ynevent_enable) {
            if($this -> _getParam('query'))
            {
                $params['keyword'] = $this -> _getParam('query');
            }
            $this -> view -> query = $params['keyword'];
        }
        else {
            if($this -> _getParam('query'))
            {
                $params['search_text'] = $this -> _getParam('query');
            }
            $this -> view -> query = $params['search_text'];
        }
        $this->view->params = $params;
    }
    
    public function videoSearchAction() {
        if (!Engine_Api::_() -> hasItemType('video')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if( !$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid()) 
            return;
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $this->view->ynvideo_enable = $ynvideo_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynvideo');
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['text'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['text'];
        $this->view->params = $params;
        
        if ($ynvideo_enable) {
            $videoSelect = Engine_Api::_()->ynvideo()->getVideosSelect($params);
            $this -> view -> paginator = Zend_Paginator::factory($videoSelect);
            $videoSelect = Engine_Api::_()->ynvideo()->getVideosSelect(array());
            $this->view->total_content = Zend_Paginator::factory($videoSelect) -> getTotalItemCount();
        }
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('video');
        if ($contentType) $this->view->label_content = $contentType->title;
    }
    
    public function albumSearchAction() {
        $sealbum_enable = Engine_Api::_()->ynadvsearch()->checkYouNetPlugin('album');
        $this->view->advalbum_enable = $advalbum_enable = Engine_Api::_()->ynadvsearch()->checkYouNetPlugin('advalbum');
        if (!$sealbum_enable && !$advalbum_enable) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if(!$advalbum_enable) {
            if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) 
            return;
        }
        $this -> _helper -> content -> setEnabled() ;
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
    }
    
    public function photoSearchAction() {
        $advalbum_enable = Engine_Api::_()->ynadvsearch()->checkYouNetPlugin('advalbum');
        if (!$advalbum_enable) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'view') -> isValid())
            return;
        
        $table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
        $atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
        $Name = $table -> info('name');
        $aName = $atable -> info('name');
        $select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1");
        
        $get = $this -> getRequest() -> getParams();
        $get['format'] = 'html';
        $get['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $get['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $get['search'];
        $this->view->params = $get;
        
        $tmp_select = $select;
        if (!empty($get['search']))
        {
            $select -> where("$Name.title LIKE ? OR $Name.description LIKE ?", '%' . $get['search'] . '%');
        }
        if (!empty($get['category_id']))
        {
            $select -> where("$aName.category_id = ?", $get['category_id']);
        }
        if ($get['color'] != "")
        {
            $cTable = Engine_Api::_() -> getDbtable('photocolors', 'advalbum');
            $cName = $cTable -> info('name');
            $select -> joinLeft($cName, "$cName.photo_id = $Name.photo_id", '') -> where("$cName.color_title = ?", $get['color']);
        }
        $arr_photos = $table -> getAllowedPhotos($select);
        $tmp_arr_photos = $table -> getAllowedPhotos($tmp_select);
        $this -> view -> result_count = count($arr_photos);
        $this->view->total_content = count($tmp_arr_photos);
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('advalbum_photo');
        if ($contentType) $this->view->label_content = $contentType->title;
        $this -> _helper -> content -> setEnabled();
    }
    
    public function filesharingSearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynfilesharing')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams('folder', null, 'view') -> isValid())
            return;
        
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        if (!isset($params['type'])) {
            $params['type'] = 'all';
        }
        if (!isset($params['orderby'])) {
            $params['orderby'] = 'creation_date';
        }
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
    }
    
    public function musicSearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('music')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) {
          return;
        }
        $params = $this->_getAllParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this -> view -> params = $params;
        
        $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($params);
        $this->view->total_content = Engine_Api::_()->music()->getPlaylistPaginator(array()) -> getTotalItemCount();
        $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('music_playlist');
        if ($contentType) $this->view->label_content = $contentType->title;
        $this -> _helper -> content -> setEnabled();
    }
    
    public function fundraisingSearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynfundraising')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this->view->params = $params;
    }
    
    public function mpMusicSearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('mp3music')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', null, 'view') -> isValid())
            return;
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['title'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['title'];
        $this->view->params = $params;
        $this -> view -> paginator = Engine_Api::_() -> mp3music() -> getSongPaginator($params); 
        $this->view->total_content = Engine_Api::_() -> mp3music() -> getSongPaginator(array()) -> getTotalItemCount();
        $this->view->label_content = $this -> view -> translate('songs');
        
        if (!empty($params['search']) && ($params['search'] == "playlists" || $params['search'] == "browse_playlists"))
            $this -> _helper -> redirector -> gotoRoute(array('action' => 'mp-musicplaylists-search', 'page' => 1, 'search' => $params['search'], 'title' => $params['title']), 'ynadvsearch_search', true);
        if (!empty($params['search']) && ($params['search'] == "album" || $params['search'] == "browse_new_albums"))
            $this -> _helper -> redirector -> gotoRoute(array('action' => 'mp-musicalbums-search', 'page' => 1, 'search' => $params['search'], 'title' => $params['title']), 'ynadvsearch_search', true);
        
        $this -> _helper -> content -> setEnabled();
    }
    
    public function mpMusicalbumsSearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('mp3music')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', null, 'view') -> isValid())
            return;
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['title'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['title'];
        $this->view->params = $params;
        $this -> view -> paginator = Engine_Api::_() -> mp3music() -> getAlbumPaginator($params); 
        $this->view->total_content = Engine_Api::_() -> mp3music() -> getAlbumPaginator(array()) -> getTotalItemCount();
        $this->view->label_content = $params['search'];
        $this -> _helper -> content -> setEnabled();
    }
    
    public function mpMusicplaylistsSearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('mp3music')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_playlist', null, 'view') -> isValid())
            return;
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['title'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['title'];
        $this->view->params = $params;
        $this -> view -> paginator = Engine_Api::_() -> mp3music() -> getTopPlaylistPaginator($params); 
        $this->view->total_content = Engine_Api::_() -> mp3music() -> getTopPlaylistPaginator(array()) -> getTotalItemCount();
        $this->view->label_content = $params['search'];
        $this -> _helper -> content -> setEnabled();
    }
    
    public function groupbuySearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('groupbuy')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $params = $this->getRequest()->getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['search'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['search'];
        $this -> view -> params = $params;
        $this -> view -> paginator = $paginator = Engine_Api::_() -> groupbuy() -> getDealsPaginator($params);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')-> groupbuy_page;
        $paginator->setItemCountPerPage($items_per_page);
        
        $this->view->total_content = Engine_Api::_() -> groupbuy() -> getDealsPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('groupbuy_deal');
        if ($contentType) $this->view->label_content = $contentType->title;
        $this -> _helper -> content -> setEnabled();
    }

    public function listingSearchAction(){
        if (!Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynlistings')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['listing_title'] = $this -> _getParam('query');
        }
        $this -> view -> query = $params['listing_title'];
        $this->view->params = $params;
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynlistings_listing') -> getListingsPaginator($params);
        $this->view->total_content = Engine_Api::_() -> getItemTable('ynlistings_listing') -> getListingsPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynlistings_listing');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

    public function jobpostingJobSearchAction(){
        if (!Engine_Api::_() -> hasItemType('ynjobposting_job')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if(!isset($params['status'])) {
            $params['status'] = 'published';
        }
        if($this -> _getParam('query'))
        {
            $params['job_title'] = $this -> _getParam('query');
        }
        if (isset($params['job_title']))
            $this -> view -> query = $params['job_title'];
        $this->view->params = $params;
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynjobposting_job') -> getJobsPaginator($params);
        $this->view->total_content = Engine_Api::_() -> getItemTable('ynjobposting_job') -> getJobsPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynjobposting_job');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

    public function jobpostingCompanySearchAction(){
        if (!Engine_Api::_() -> hasItemType('ynjobposting_company')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['keyword'] = $this -> _getParam('query');
        }
        if (isset($params['keyword']))
            $this -> view -> query = $params['keyword'];
        $this->view->params = $params;
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynjobposting_company') -> getCompaniesPaginator($params);
        $this->view->total_content = Engine_Api::_() -> getItemTable('ynjobposting_company') -> getCompaniesPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynjobposting_company');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

    public function businessSearchAction(){
        if (!Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['title'] = $this -> _getParam('query');
        }
        if (isset($params['title']))
            $this -> view -> query = $params['title'];
        $this->view->params = $params;
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynbusinesspages_business') -> getBusinessesPaginator($params);
        $this->view->total_content = Engine_Api::_() -> getItemTable('ynbusinesspages_business') -> getBusinessesPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynbusinesspages_business');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

	public function resumeSearchAction(){
        if (!Engine_Api::_() -> hasModuleBootstrap('ynresume')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['title'] = $this -> _getParam('query');
        }
        if (isset($params['title']))
            $this -> view -> query = $params['title'];
        $this->view->params = $params;
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynresume_resume') -> getResumesPaginator($params);
        $this->view->total_content = Engine_Api::_() -> getItemTable('ynresume_resume') -> getResumesPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynresume_resume');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

	public function multilistingSearchAction(){
        if (!Engine_Api::_() -> hasModuleBootstrap('ynmultilisting')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['listing_title'] = $this -> _getParam('query');
        }
        if (isset($params['listing_title']))
            $this -> view -> query = $params['listing_title'];
        $this->view->params = $params;
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynmultilisting_listing') -> getListingsPaginator($params);
        $this->view->total_content = Engine_Api::_() -> getItemTable('ynmultilisting_listing') -> getListingsPaginator(array()) -> getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynmultilisting_listing');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

	public function socialmusicSearchAction(){
        if (!Engine_Api::_() -> hasModuleBootstrap('ynmusic')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        
        $params = $this -> getRequest() -> getParams();
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['keyword'] = $this -> _getParam('query');
        }
        if (isset($params['keyword']))
            $this -> view -> query = $params['keyword'];
        $this->view->params = $params;
    }

    public function ultimatevideoSearchAction(){
        if (!Engine_Api::_() -> hasModuleBootstrap('ynultimatevideo')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        $params = $this -> getRequest() -> getParams();
        $searchParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
//        print_r($params);die;
        if (isset($searchParams['query']) && $searchParams['query']) {
            $searchParams['keyword'] = $searchParams['query'];
        }
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['keyword'] = $this -> _getParam('query');
        }
        if (isset($params['keyword']))
            $this -> view -> query = $params['keyword'];
        $this->view->params = $params;
        $this->view->search_params = $searchParams;

    }
    public function videochannelSearchAction(){
        if (!Engine_Api::_() -> hasModuleBootstrap('ynvideochannel')) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> _helper -> content -> setEnabled();
        $params = $this -> getRequest() -> getParams();
        $searchParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if (isset($searchParams['query']) && $searchParams['query']) {
            $searchParams['keyword'] = $searchParams['query'];
        }
        $params['format'] = 'html';
        $params['page'] = $this -> _getParam('page', 1);
        if($this -> _getParam('query'))
        {
            $params['keyword'] = $this -> _getParam('query');
        }
        if (isset($params['keyword']))
            $this -> view -> query = $params['keyword'];
        $this->view->params = $params;
        $this->view->search_params = $searchParams;
    }
}