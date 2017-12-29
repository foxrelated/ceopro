<?php

class Ynadvsearch_Widget_PollResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $poll_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('poll');
        if(!$poll_enable) {
            return $this -> setNoRender();
        }
        if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('poll', null, 'view')->checkRequire()) $this->setNoRender();
        $this->view->canCreate = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('poll', null, 'create')->checkRequire();
		$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);
        
        $params['browse'] = 1;
        $this -> view -> formValues = $params;
        
        $viewer = Engine_Api::_()->user()->getViewer();
        if( @$params['show'] == 2 && $viewer->getIdentity() ) {
            // Get an array of friend ids
            $params['users'] = $viewer->membership()->getMembershipsOfIds();
        }
        unset($params['show']);
    
        // Make paginator
        $currentPageNumber = (isset($params['page'])) ? $params['page'] : 1;
        $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perPage', 10);
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($params);
        $paginator
            ->setItemCountPerPage($itemCountPerPage)
            ->setCurrentPageNumber($currentPageNumber);
            
        //count result
        $this->view->total_content = $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator(array('browse' => 1))->getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('poll');
        if ($contentType) $this->view->label_content = $contentType->title;
    }

}
