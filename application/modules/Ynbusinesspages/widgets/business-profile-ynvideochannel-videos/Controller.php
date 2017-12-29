<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/20/2016
 * Time: 10:48 AM
 */
class Ynbusinesspages_Widget_BusinessProfileYnvideochannelVideosController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;
    public function indexAction()
    {
        // Don't render if module not available
        if (!Engine_Api::_()->hasModuleBootstrap('ynvideochannel')) {
            return $this->setNoRender();
        }

        // Don't render this if not authorized
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');

        // Get subject and check auth
        $this->view->business = $business = Engine_Api::_()->core()->getSubject('ynbusinesspages_business');
        if (!$business->isViewable() || !$business->getPackage()->checkAvailableModule('ynvideochannel_video')) {
            return $this->setNoRender();
        }

        //check auth create
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->canCreate = $canCreate = $business->isAllowed('video_create');

        //Get GUID
        $guid = Engine_Api::_()->getItemByGuid($business->getGUID());
        //Get search condition
        $params = array();
        $params['parent_id'] = $business -> getIdentity();
        $params['parent_type'] = $guid->getType();
        $params['browse_by'] = 'creation_date';

        //Get paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('videos', 'ynvideochannel') -> getVideosPaginator($params);
        $itemCountPerPage = $this -> _getParam('itemCountPerPage', 8);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 8;
        }
        $paginator -> setItemCountPerPage($itemCountPerPage);
        $paginator -> setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest()->getParam('page', 1));
    }
    public function getChildCount()
    {
        return $this->_childCount;
    }
}