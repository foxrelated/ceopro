<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/21/2016
 * Time: 9:23 AM
 */
class Advgroup_YnvideochannelController extends Core_Controller_Action_Standard
{
    public function init()
    {
        if (!Engine_Api::_()->core()->hasSubject()) {
            if (0 !== ($group_id = (int)$this->_getParam('group_id')) && null !== ($group = Engine_Api::_()->getItem('group', $group_id))) {
                Engine_Api::_()->core()->setSubject($group);
            } else {
                return $this->_helper->requireSubject()->forward();
            }
        }

    }

    public function listAction()
    {
        //Checking Ynultimatevideo Plugin - View privacy
        $video_enable = Engine_Api::_()->hasModuleBootstrap('ynvideochannel');
        if (!$video_enable) {
            return $this->_helper->requireSubject->forward();
        }

        $this->view->group = $group = Engine_Api::_()->core()->getSubject();

        //Check auth and create permission
        $viewer = Engine_Api::_()->user()->getViewer();
        $canCreate = $group->authorization()->isAllowed($viewer, 'video');
        $levelCreate = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $viewer, 'auth_video');

        if ($canCreate && $levelCreate) {
            $this->view->canCreate = true;
        } else {
            $this->view->canCreate = false;
        }

        //Get Search Form
        $this->view->form = $form = new Advgroup_Form_Ynvideochannel_Search();

        //Get GUID
        $guid = Engine_Api::_()->getItemByGuid($group->getGUID());

        //Get search condition
        $params = array();
        $params = $this->_getAllParams();
        $params['parent_type'] = $guid->getType();
        $params['parent_id'] = $group->getIdentity();
        $params['search'] = 1;
        $params['limit'] = 10;
        $params['orderby'] = $this->_getParam('orderby', 'creation_date');
        $form->populate($params);
        $this->view->formValues = $form->getValues();

        //Get table mapping
        $table = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');

        $this->view->paginator = $paginator = $table->getVideosPaginator($params);

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

    public function manageAction()
    {
        //Checking Ynultimatevideo Plugin - View privacy
        $video_enable = Engine_Api::_()->hasModuleBootstrap('ynvideochannel');
        if (!$video_enable) {
            return $this->_helper->requireSubject->forward();
        }
        //Get viewer, group, search form
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->group = $group = Engine_Api::_()->core()->getSubject();
        $this->view->form = $form = new Advgroup_Form_Ynvideochannel_Search();

        //Check permission to view this page
        if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'view')->isValid()) {
            return;
        }

        //Check create video authorization
        $canCreate = $group->authorization()->isAllowed($viewer, 'video');
        $levelCreate = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $viewer, 'auth_video');

        if ($canCreate && $levelCreate) {
            $this->view->canCreate = true;
        } else {
            $this->view->canCreate = false;
        }

        //Get GUID
        $guid = Engine_Api::_()->getItemByGuid($group->getGUID());

        //Get search condition
        $params = array();
        $params = $this->_getAllParams();
        $params['parent_type'] = $guid->getType();
        $params['parent_id'] = $group->getIdentity();
        $params['limit'] = 10;
        $params['orderby'] = $this->_getParam('orderby', 'creation_date');
        $form->populate($params);
        $this->view->formValues = $form->getValues();

        //Get table mapping
        $table = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');

        $this->view->paginator = $paginator = $table->getVideosPaginator($params);

        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }
}