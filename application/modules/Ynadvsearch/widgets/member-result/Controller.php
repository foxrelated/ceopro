<?php

class Ynadvsearch_Widget_MemberResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $ynmember_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynmember');
        if($ynmember_enable) {
            return $this -> setNoRender();
        }
        $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
        $viewer = Engine_Api::_()->user()->getViewer();
        if(!$require_check){
            if (!$viewer->getIdentity()) $this->setNoRender();
        }
		$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);

        $this -> view -> formValues = $params;
		// Process options
        $tmp = array();
        $originalOptions = $params;
        foreach( $params as $k => $v ) {
          if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
            continue;
          } else if( false !== strpos($k, '_field_') ) {
            list($null, $field) = explode('_field_', $k);
            $tmp['field_' . $field] = $v;
          } else if( false !== strpos($k, '_alias_') ) {
            list($null, $alias) = explode('_alias_', $k);
            $tmp[$alias] = $v;
          } else {
            $tmp[$k] = $v;
          }
        }
        $params = $tmp;
    
        // Get table info
        $table = Engine_Api::_()->getItemTable('user');
        $userTableName = $table->info('name');
    
        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $searchTableName = $searchTable->info('name');
    
        //extract($options); // displayname
        $profile_type = @$params['profile_type'];
        $displayname = @$params['displayname'];
        if (!empty($params['extra'])) {
          extract($params['extra']); // is_online, has_photo, submit
        }
    
        // Contruct query
        $select = $table->select()
          //->setIntegrityCheck(false)
          ->from($userTableName)
          ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
          //->group("{$userTableName}.user_id")
          ->where("{$userTableName}.search = ?", 1)
          ->where("{$userTableName}.enabled = ?", 1);
          
        $searchDefault = true;  
          
        // Build the photo and is online part of query
        if( isset($has_photo) && !empty($has_photo) ) {
          $select->where($userTableName.'.photo_id != ?', "0");
          $searchDefault = false;
        }
    
        if( isset($is_online) && !empty($is_online) ) {
          $select
            ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
            ->group("engine4_user_online.user_id")
            ->where($userTableName.'.user_id != ?', "0");
          $searchDefault = false;
        }
    
        // Add displayname
        if( !empty($displayname) ) {
          $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
          $searchDefault = false;
        }
    
        // Build search part of query
        $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $params);
        foreach( $searchParts as $k => $v ) {
          $select->where("`{$searchTableName}`.{$k}", $v);
          
          if(isset($v) && $v != ""){
            $searchDefault = false;
          }
        }
        
        if($searchDefault){
          $select->order("{$userTableName}.lastlogin_date DESC");
        } else {
          $select->order("{$userTableName}.displayname ASC");
        }
    
        // Build paginator
        $page = (isset($params['page'])) ? $params['page'] : 1;
	    $paginator = Zend_Paginator::factory($select);
        $paginator -> setCurrentPageNumber($page);
        $paginator -> setItemCountPerPage(10);
        $this->view->users = $paginator;
        $this->view->totalUsers = $paginator->getTotalItemCount();
        $this->view->userCount = $paginator->getCurrentItemCount();
        
        //count result
        $this->view->total_content = Zend_Paginator::factory($table->select()->where("{$userTableName}.search = ?", 1)->where("{$userTableName}.enabled = ?", 1))->getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('user');
        if ($contentType) $this->view->label_content = $contentType->title;
        
        // Load fields view helpers
        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    }

}
