<?php

class Ynmember_ReviewController extends Core_Controller_Action_Standard
{
    public function init()
    {
        $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
        if (!$require_check) {
            if (!$this->_helper->requireUser()->isValid())
                return;
        }
    }

    public function indexAction()
    {
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $reviewTbl = Engine_Api::_()->getItemTable("ynmember_review");
        $params = $this->_getAllParams();
        $form = new Ynmember_Form_Search_Review();
        if ($form->isValid($params)) {
            $params = $form -> getValues();
        }
        else
            $params = array();
        $this->view->formValues = $params;
        if (!empty($params['review_for'])) {
            $resource_id = array();
            $list_reviewer = Engine_Api::_()->ynmember()->getUsersByName($params['review_for']);
            foreach ($list_reviewer as $item) {
                $resource_id[] = $item->getIdentity();
            }
            $params['resource_id'] = $resource_id;
        }
        if (!empty($params['review_by'])) {
            $user_id = array();
            $list_review_for = Engine_Api::_()->ynmember()->getUsersByName($params['review_by']);
            foreach ($list_review_for as $item) {
                $user_id[] = $item->getIdentity();
            }
            $params['user_id'] = $user_id;
        }

        $this->view->can_edit_own_review = $can_edit_own_review = ($this->_helper->requireAuth()->setAuthParams('ynmember_review', null, 'can_edit_own_review')->checkRequire());
        $this->view->reviews = $reviews = $reviewTbl->getReviewPaginator($params);
        $reviews->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
        $reviews->setCurrentPageNumber($this->_getParam('page', 1));
        $this->_helper->content->setEnabled();
    }

    public function usefulAction()
    {
        $review_id = $this->_getParam('review_id');
        $value = $this->_getParam('value');
        $inline = $this->_getParam('inline', false);
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }
        $this->view->review = $review = Engine_Api::_()->getItem('ynmember_review', $review_id);
        if (!$review) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('This review is not existed.');
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Can not set useful');
            return;
        }
        $usefulTbl = Engine_Api::_()->getDbTable('usefuls', 'ynmember');
        $row = $usefulTbl->getUseFul($viewer->getIdentity(), $review_id);
        if (!$row) {
            $row = $usefulTbl->createRow();
        }
        $row->setFromArray(array(
            'review_id' => $review_id,
            'user_id' => $viewer->getIdentity(),
            'value' => $value,
        ));
        $row->save();
        $params = $review->getReviewUseful();
        if (isset($params['yes_count'])) {
            $review->helpful_count = (int)$params['yes_count'];
        } else {
            $review->helpful_count = 0;
        }
        $review->save();
        $params['inline'] = ($inline) ? true : false;
        echo $this->view->partial(
            '_useful.tpl',
            'ynmember',
            $params
        );
        exit;
    }

    public function userAction()
    {
        $identity = $this->_getParam('id');
        if ($identity) {
            $this->user = $user = Engine_Api::_()->getItem('user', $identity);
            if ($user instanceof Core_Model_Item_Abstract) {
                if (!Engine_Api::_()->core()->hasSubject()) {
                    Engine_Api::_()->core()->setSubject($user);
                }
            }
        }

        /**
         * Get study places as string
         * */
        $studyPlacesTbl = Engine_Api::_()->getDbTable('studyplaces', 'ynmember');
        $studyplaces = $studyPlacesTbl->getStudyPlacesByUserId($user->getIdentity());
        $studys = array();
        foreach ($studyplaces as $study) {
            if ($study->isViewable()) {
                $studys[] = "<a target='_blank' href='https://www.google.com/maps?q={$study->latitude},{$study->longitude}'>{$study->name}</a>";
            }
        }
        $this->view->studyplaces = implode(", ", $studys);

        /**
         * Get work places as string
         * */
        $workPlacesTbl = Engine_Api::_()->getDbTable('workplaces', 'ynmember');
        $workplaces = $workPlacesTbl->getWorkPlacesByUserId($user->getIdentity());
        $works = array();
        foreach ($workplaces as $work) {
            if ($work->isViewable()) {
                $works[] = "<a target='_blank' href='https://www.google.com/maps?q={$work->latitude},{$work->longitude}'>{$work->company}</a>";
            }
        }
        $this->view->workplaces = implode(", ", $works);

        /**
         * Get living places as string
         * */
        $livePlacesTbl = Engine_Api::_()->getDbTable('liveplaces', 'ynmember');
        $liveplaces = $livePlacesTbl->getLiveCurrentPlacesByUserId($user->getIdentity());
        $lives = array();
        foreach ($liveplaces as $live) {
            if ($live->isViewable()) {
                $lives[] = "<a target='_blank' href='https://www.google.com/maps?q={$live->latitude},{$live->longitude}'>{$live->location}</a>";
            }
        }
        $this->view->liveplaces = implode(", ", $lives);

        /**
         *
         */
        $this->view->groups = array();
        if (Engine_Api::_()->hasModuleBootstrap('group') || Engine_Api::_()->hasModuleBootstrap('advgroup')) {
            $groupTbl = Engine_Api::_()->getItemTable('group');
            $membership = (Engine_Api::_()->hasModuleBootstrap('advgroup'))
                ? Engine_Api::_()->getDbtable('membership', 'advgroup')
                : Engine_Api::_()->getDbtable('membership', 'group');

            $select = $membership->getMembershipsOfSelect($user);
            $this->view->groups = $groups = $groupTbl->fetchAll($select);
        }
        $reviewTbl = Engine_Api::_()->getItemTable('ynmember_review');
        $reviews = $reviewTbl->getAllReviewsByResourceId($user->getIdentity());
        $this->view->reviews = $reviews;
        $this->view->navigation = Engine_Api::_()
            ->getApi('menus', 'core')
            ->getNavigation('user_profile');

        $this->_helper->content->setEnabled();
    }

    public function userGroupAction()
    {
        $identity = $this->_getParam('id');
        if ($identity) {
            $this->user = $user = Engine_Api::_()->getItem('user', $identity);
        }
        $this->_helper->layout->setLayout('default-simple');
        $this->view->groups = array();
        if (Engine_Api::_()->hasModuleBootstrap('group') || Engine_Api::_()->hasModuleBootstrap('advgroup')) {
            $groupTbl = Engine_Api::_()->getItemTable('group');
            $membership = (Engine_Api::_()->hasModuleBootstrap('advgroup'))
                ? Engine_Api::_()->getDbtable('membership', 'advgroup')
                : Engine_Api::_()->getDbtable('membership', 'group');

            $select = $membership->getMembershipsOfSelect($user);
            $this->view->groups = $groups = $groupTbl->fetchAll($select);
        }
    }

    public function detailAction()
    {
        $reviewId = $this->_getParam('id');
        if ($reviewId) {
            $review = Engine_Api::_()->getItem('ynmember_review', $reviewId);
        }
        if (!$review) {
            return $this->_helper->redirector->gotoRoute(array(
                'controller' => 'review',
                'action' => 'index'
            ), 'ynmember_extended', true);
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($review->user_id != $viewer->getIdentity()) {
            $review->view_count++;
            $review->save();
        }
        if ($review instanceof Ynmember_Model_Review) {
            if (!Engine_Api::_()->core()->hasSubject()) {
                Engine_Api::_()->core()->setSubject($review);
            }
        }
        $this->view->can_report_reviews = $can_report_reviews = ($this->_helper->requireAuth()->setAuthParams('ynmember_review', null, 'can_report_reviews')->checkRequire());
        $this->view->can_share_reviews = $can_share_reviews = ($this->_helper->requireAuth()->setAuthParams('ynmember_review', null, 'can_share_reviews')->checkRequire());
        $this->view->can_delete_own_reviews = $can_delete_own_reviews = ($this->_helper->requireAuth()->setAuthParams('ynmember_review', null, 'can_delete_own_reviews')->checkRequire());
        $this->view->review = $review;
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->_helper->content->setEnabled();
    }

    public function deleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $review = Engine_Api::_()->getItem('ynmember_review', $this->getRequest()->getParam('id'));
        /*
        if (!$this -> _helper -> requireAuth() -> setAuthParams($review, null, 'delete') -> isValid())
            return;
        */
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Ynmember_Form_DeleteReview();

        if (!$review) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Review doesn't exists or not authorized to delete.");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $db = $review->getTable()->getAdapter();
        $db->beginTransaction();

        try {

            //delete rating belong to this review
            $ratingTable = Engine_Api::_()->getItemTable('ynmember_rating');
            $select = $ratingTable->select()->where('review_id = ?', $review->getIdentity());
            $ratings = $ratingTable->fetchAll($select);
            foreach ($ratings as $rating) {
                $rating->delete();
            }

            // General Rating
            $user = Engine_Api::_()->getItem('user', $review->resource_id);
            if ($user->getIdentity()) {
                $tableRating = Engine_Api::_()->getItemTable('ynmember_rating');
                $user->rating = $tableRating->getRateResouce($user->getIdentity());
                $user->review_count = $user->review_count--;
                $user->save();
            }

            //delete review
            $review->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Review has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'review'), 'ynmember_extended', true),
            'messages' => Array($this->view->message)
        ));
    }

}
	
	