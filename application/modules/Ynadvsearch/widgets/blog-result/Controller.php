<?php

class Ynadvsearch_Widget_BlogResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $blog_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('blog');
        if(!$blog_enable) {
            return $this -> setNoRender();
        }
        if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('blog', null, 'view')->checkRequire()) $this->setNoRender();
        $this->view->canCreate = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('blog', null, 'create')->checkRequire();
		$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);

        $this -> view -> formValues = $params;
		$params['draft'] = "0";
        $params['visible'] = "1";
        
        $viewer = Engine_Api::_()->user()->getViewer();
        // Do the show thing
        if( @$params['show'] == 2 ) {
          // Get an array of friend ids
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
        $this->view->assign($params);
        // Get blogs
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator($params);
    
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $paginator->setItemCountPerPage($items_per_page);
        $page = (isset($params['page'])) ? $params['page'] : 1;
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
    
        if( !empty($params['category']) ) {
          $this->view->categoryObject = Engine_Api::_()->getDbtable('categories', 'blog')
              ->find($params['category'])->current();
        }
        
        //count result
        $this->view->total_content = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator(array('draft' => 0, 'visible' => 1))->getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('blog');
        if ($contentType) $this->view->label_content = $contentType->title;
        
    }

}
