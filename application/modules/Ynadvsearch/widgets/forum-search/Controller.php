<?php

class Ynadvsearch_Widget_ForumSearchController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        if (!Engine_Api::_() -> hasItemType('forum_topic')) {
            $this->setNoRender();
        }
        if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('forum', null, 'view')->checkRequire()) $this->setNoRender();
        $this->view->values = $values = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        
    }

}
