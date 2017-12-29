<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/21/2016
 * Time: 10:06 AM
 */
class Advgroup_Widget_ProfileYnvideochannelVideosController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;
    public function indexAction()
    {
        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        // Don't render this if not has any group
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Don't render this if not has module ynvideochannel
        if (!Engine_Api::_()->hasItemType('ynvideochannel_video')) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->group = $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->is_subgroup && !$subject->isParentGroupOwner($viewer)) {
            $parent_group = $subject->getParentGroup();
            if (!$parent_group->authoziration()->isAllowed($viewer, 'view')) {
                return $this->setNoRender();
            }
            else if (!$subject->authorization()->isAllowed($viewer, 'view')) {
                return $this->setNoRender();
            }
        }
        else if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }


        //Get number of videos display
        $max = $this->_getParam('itemCountPerPage');
        if (!is_numeric($max) || $max <= 0) $max = 5;

        $marginLeft = $this->_getParam('marginLeft', '');
        if (!empty($marginLeft)) {
            $this->view->marginLeft = $marginLeft;
        }

        // Get GUID
        $guid = Engine_Api::_()->getItemByGuid($subject->getGUID());

        // Prepare params to get paginator
        $params = array();
        $params['parent_type'] = $guid->getType();
        $params['parent_id'] = $subject->getIdentity();
        $params['orderby'] = 'creation_date';
        $params['page'] = $this->_getParam('page',1);
        $params['limit'] = $max;

        // Get paginator
        $table = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $this->view->paginator = $paginator = $table->getVideosPaginator($params);

        $paginator->setItemCountPerPage($max);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $canCreate = $subject -> authorization() -> isAllowed($viewer, 'video');
        $levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'video');

        if ($canCreate && $levelCreate) {
            $this -> view -> canCreate = true;
        } else {
            $this -> view -> canCreate = false;
        }

        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }
    public function getChildCount()
    {
        return $this->_childCount;
    }
}