<?php

class Ynadvsearch_Widget_ClassifiedResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $classified_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('classified');
        if(!$classified_enable) {
            return $this -> setNoRender();
        }
		$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);
//        if (!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('classified', null, 'create')->checkRequire()) $this->setNoRender();
        $this->view->can_create = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('classified', null, 'create')->checkRequire();
        $this -> view -> formValues = $params;
        $customFieldValues = $params;
    
        // Process options
        $tmp = array();
        foreach( $customFieldValues as $k => $v ) {
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
        $customFieldValues = $tmp;
        $viewer = Engine_Api::_()->user()->getViewer();
        // Do the show thing
        if( @$params['show'] == 2 ) {
          // Get an array of friend ids to pass to getClassifiedsPaginator
          $table = Engine_Api::_()->getItemTable('user');
          $select = $viewer->membership()->getMembersSelect('user_id');
          $friends = $table->fetchAll($select);
          // Get stuff
          $ids = array();
          foreach( $friends as $friend )
          {
            $ids[] = $friend->user_id;
          }
          //unset($values['show']);
          $params['users'] = $ids;
        }
    
        // check to see if request is for specific user's listings
        if( ($user_id = $this->_getParam('user_id')) ) {
          $params['user_id'] = $user_id;
        }
    
        $this->view->assign($params);
    
        // items needed to show what is being filtered in browse page
        if( !empty($params['tag']) ) {
          $this->view->tag_text = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
        }
        
        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    
        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($params, $customFieldValues);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $page = (isset($params['page'])) ? $params['page'] : 1;
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
    
        if( !empty($values['category']) ) {
          $this->view->categoryObject = Engine_Api::_()->getDbtable('categories', 'classified')
              ->find($values['category'])->current();
        }
        
        //count result
        $this->view->total_content = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator(array(), array())->getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('classified');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

}
