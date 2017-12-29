<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/12/2016
 * Time: 3:18 PM
 */
class Ynmultilisting_Widget_ListingProfileUltimateVideosController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {
        //Don't render if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject() || Engine_Api::_()->ynmultilisting()->getMultilistingVideoType() != 'ynultimatevideo_video') {
            return $this->setNoRender();
        }


        //Get subject
        $this->view->listing = $listing = Engine_Api::_()->core()->getSubject('ynmultilisting_listing');

        //Check auth with package
        if (in_array($listing->status, array('draft', 'expired'))) {
            return $this->setNoRender();
        }

        $package = $listing->getPackage();
        if (!$package->getIdentity()) {
            return $this->setNoRender();
        }
        if (!$package->allow_video_tab) {
            return $this->setNoRender();
        }

        $params = array();
        $params['parent_type'] = 'ynmultilisting_listing';
        $params['parent_id'] = $listing->getIdentity();

        // Get paginator
        $mappingTable = Engine_Api::_()->getDbTable('mappings', 'ynmultilisting');
        $params['listing_id'] = $listing -> getIdentity();
        $this->view->paginator = $paginator = $mappingTable->getWidgetUltimateVideosPaginator($params);

        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }

        $this->view->canUpload = $canUpload = $listing->isAllowed('video');
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}