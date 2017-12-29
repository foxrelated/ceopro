<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/12/2016
 * Time: 3:19 PM
 */
class Ynmultilisting_UltimateVideosController extends Core_Controller_Action_Standard
{
    public function init()
    {
        if (!Engine_Api::_()->core()->hasSubject()) {
            if (0 !== ($listing_id = (int)$this->_getParam('listing_id'))
                && null !== ($listing = Engine_Api::_()->getItem('ynmultilisting_listing', $listing_id))
            ) {
                Engine_Api::_()->core()->setSubject($listing);
            }
        }
    }

    public function listAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject())
        {
            return $this -> _helper -> requireSubject -> forward();
        }

        //Check viewer and subject requirement
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity())
        {
            return $this -> _helper -> requireAuth -> forward();
        }

        //Checking Ynvideo Plugin - View privacy
        $ynultimatevideo_enable = Engine_Api::_() -> hasModuleBootstrap('ynultimatevideo');
        $this->view->form = $form = new Ynmultilisting_Form_Video_Manage();
        if (!$ynultimatevideo_enable)
        {
            return $this -> _helper -> requireSubject -> forward();
        }

        //Get viewer, listing, search form
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();

        // Check create video authorization
        if ($viewer -> getIdentity() == $listing->user_id)
        {
            $this -> view -> canCreate = $listing -> isAllowed('video');
        }
        else
        {
            return $this -> _helper -> requireSubject -> forward();
        }

        //Get data

//        $table_ynultimatevideo = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
//        $params['parent_id'] = $this->_getParam('listing_id');
//        $params['parent_type'] = 'ynmultilisting_listing';
//        $this -> view -> paginator = $paginator = $table_ynultimatevideo->getVideosPaginator($params);

        $tableMappings = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
        $params['listing_id'] = $this->_getParam('listing_id');
        $this -> view -> paginator = $paginator = $tableMappings -> getUltimatevideoPaginator($params);

        foreach( $paginator as $video )
        {
            $subform = new Ynmultilisting_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));
            if($video->video_id == $listing->video_id)
                $subform->removeElement('delete');
            $subform->populate($video->toArray());
            $form->addSubForm($subform, $video->getGuid());
        }

        // Check post/form
        if( !$this->getRequest()->isPost() ) {
            return;
        }

        $post = $this->getRequest()->getPost();
        if(!$form->isValid($post))
            return;
        $cover = $this->_getParam('cover');
        // Process
        foreach( $paginator as $video )
        {
            $subform = $form->getSubForm($video->getGuid());
            $subValues = $subform->getValues();
            $subValues = $subValues[$video->getGuid()];
            unset($subValues['video_id']);

            $guid = Engine_Api::_()->getItemByGuid($video->getGuid());

            if( isset($cover) && $cover == $video->video_id) {
                $listing->video_type = $guid->getType();
                $listing->video_id = $video->video_id;
                $listing->save();
            }

            if( isset($subValues['delete']) && $subValues['delete'] == '1' )
            {
                if( $listing->video_id == $video->video_id){
                    $listing->video_type = null;
                    $listing->video_id = 0;
                    $listing->save();
                }
                $select =  $table_ynultimatevideo -> select() -> where('parent_id = ?', $listing->getIdentity()) -> where('parent_type = ?', 'ynmultilisting_listing') -> where('video_id=?',$video->video_id) -> limit(1);
                $video_row = $table_ynultimatevideo->fetchRow($select);
                $video_row->delete();
                $video->delete();
            }
            else
            {
                $video->setFromArray($subValues);
                $video->save();
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'ynmultilisting_general', true);
    }

    public function manageAction()
    {
        // Checking Ultimate Video Plugin - Viewer required -View privacy
        if (!Engine_Api::_()->hasItemType('ynultimatevideo_video')) {
            return;
        }

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        $this->view->listing = $listing = Engine_Api::_()->core()->getSubject();

        if (!$listing->isAllowed('view')) {
            return $this->_helper->requireAuth()->forward();
        }
        
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->form = $form = new Ynmultilisting_Form_Video_Search();

        // Check create video
        $this->view->canCreate = $canCreate = $listing->isAllowed('video');

        // Prepare params
        $mappingTable = Engine_Api::_()->getDbTable('mappings', 'ynmultilisting');
        $params['listing_id'] = $listing -> getIdentity();
        $this->view->paginator = $paginator = $mappingTable->getWidgetUltimateVideosPaginator($params);

        $paginator -> setItemCountPerPage(6);
        $paginator -> setCurrentPageNumber($this->_getParam('page'), 1);
    }

}