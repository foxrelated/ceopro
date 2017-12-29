<?php

class Ynadvsearch_Widget_YnBlogResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $ynblog_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynblog');
        if(!$ynblog_enable) {
            return $this -> setNoRender();
        }
        if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('blog', null, 'view')->checkRequire()) $this->setNoRender();
		$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);

        $this -> view -> formValues = $params;
		
        // Get ynblogs
        $paginator = Engine_Api::_ ()->ynblog ()->getBlogsPaginator ( $params );
        $query = Engine_Api::_ ()->ynblog ()->getBlogsSelect ( $params );
        $table = Engine_Api::_() -> getItemTable('blog');
        $this->view->blogs = $blogs = $table->fetchAll($query);
        
        $items_per_page = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.page', 10 );
        $paginator->setItemCountPerPage ( $items_per_page );
    
        $this->view->paginator = $paginator->setCurrentPageNumber( $params['page'], 1 );
        
        //count result
        $this->view->total_content = Engine_Api::_ ()->ynblog ()->getBlogsPaginator ( array() )->getTotalItemCount();
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('blog');
        if ($contentType) $this->view->label_content = $contentType->title;
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
    }

}
