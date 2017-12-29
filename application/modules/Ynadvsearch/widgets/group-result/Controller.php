<?php

class Ynadvsearch_Widget_GroupResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $group_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('group');
        if(!$group_enable) {
            return $this -> setNoRender();
        }
        if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('group', null, 'view')->checkRequire()) $this->setNoRender();
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('group', null, 'create');
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);
        
        $params['view'] = $params['view_group'];
        $this -> view -> formValues = $params;
        
        if( $viewer->getIdentity() && @$params['view'] == 1 ) {
            $params['users'] = array();
            foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
                $params['users'][] = $memberinfo->user_id;
            }
        }
        
        $params['search'] = 1;

        // check to see if request is for specific user's listings
        $user_id = $params['user'];
        if( $user_id ) {
            $params['user_id'] = $user_id;
        }

    
        // Make paginator
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('group')->getGroupPaginator($params);
        $page = (isset($params['page'])) ? $params['page'] : 1;
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        
        //count result
        $this->view->total_content = Engine_Api::_()->getItemTable('group')->getGroupPaginator(array('search' => 1))->getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('group');
        if ($contentType) $this->view->label_content = $contentType->title;
    }
}
