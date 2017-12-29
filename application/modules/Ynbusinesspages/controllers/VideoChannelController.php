<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/20/2016
 * Time: 9:53 AM
 */
class Ynbusinesspages_VideoChannelController extends Core_Controller_Action_Standard
{
    public function init()
    {
        $this -> view -> tab = $this->_getParam('tab', null);

        if (!Engine_Api::_()->core()->hasSubject()) {
            if (0 != ($business_id = (int) $this->_getParam('business_id')) && null != ($business = Engine_Api::_()->getItem('ynbusinesspages_business', $business_id))) {
                Engine_Api::_()->core()->setSubject($business);
            }
        }

        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->_helper->requireSubject->forward();
        }

        $business = Engine_Api::_()->core()->getSubject();

        if(!$business->isViewable() || !$business->getPackage()->checkAvailableModule('ynvideochannel_video')) {
            $this->_helper->requireAuth->forward();
        }

        $ynvideochannel_enable = Engine_Api::_()->hasModuleBootstrap('ynvideochannel');
        if (!$ynvideochannel_enable) {
            $this->_helper->requireSubject->forward();
        }
    }

    public function listAction()
    {
        $this->view->business = $business = Engine_Api::_()->core()->getSubject();

        //Check auth and create permission
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->canCreate = $canCreate = $business->isAllowed('video_create');

        //Get Search Form
        $this->view->form = $form = new Ynbusinesspages_Form_Ynvideochannel_Search();

        //Get GUID
        $guid = Engine_Api::_()->getItemByGuid($business->getGUID());

        //Get search condition
        $params = array();
        $params['parent_type'] = $guid->getType();
        $params['parent_id'] = $business->getIdentity();
//        $params['orderby'] = $this->_getParam('browse_by', 'creation_date');
        $form->populate($params);
        $this->view->formValues = $form->getValues();

        //Get table mapping
        $table = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');

        $this->view->paginator = $paginator = $table->getVideosPaginator($params);

        $paginator -> setItemCountPerPage(10);
        $paginator -> setCurrentPageNumber($this -> _getParam('page'));
    }

    public function manageAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        //Get viewer, business,
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->business = $business = Engine_Api::_()->core()->getSubject();
        $this->view->form = $form = new Ynbusinesspages_Form_Ynvideochannel_Search();

        //Check permission to create video
        $this->view->canCreate = $business->isAllowed('video_create');

        //Get GUID
        $guid = Engine_Api::_()->getItemByGuid($business->getGUID());

        //Prepare param to filter
        $params = array();
        $params = $this->_getAllParams();
        $params['user_id'] = $viewer->getIdentity();
        $params['parent_type'] = $guid->getType();
        $params['parent_id'] = $business->getIdentity();
        $form->populate($params);
        $this->view->formValues = $form->getValues();

        //Get table mapping
        $table = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');

        $this->view->paginator = $paginator = $table->getVideosPaginator($params);

        $paginator -> setItemCountPerPage(10);
        $paginator -> setCurrentPageNumber($this -> _getParam('page'));
    }
}