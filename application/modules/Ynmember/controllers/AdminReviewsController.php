<?php

class Ynmember_AdminReviewsController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynmember_admin_main', array(), 'ynmember_admin_main_reviews');

        $tableReview = Engine_Api::_()->getItemTable('ynmember_review');
        $this->view->form = $form = new Ynmember_Form_Admin_Review_Search();
        $form->populate($this->_getAllParams());
        $params = $form->getValues();
        $this->view->formValues = $params;
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        if (!empty($values['from_date']))
            $from_date = strtotime($values['from_date']);
        if (!empty($values['to_date']))
            $to_date = strtotime($values['to_date']);
        if (!empty($values['from_date']))
            $params['from_date'] = date('Y-m-d H:i:s', $from_date);
        if (!empty($values['to_date']))
            $params['to_date'] = date('Y-m-d H:i:s', $to_date);
        date_default_timezone_set($oldTz);
        if (!empty($params['review_for'])) {
            $resource_id = array();
            $list_reviewer = Engine_Api::_()->ynmember()->getUsersByName($params['review_for']);
            foreach ($list_reviewer as $item) {
                $resource_id[] = $item->getIdentity();
            }
            $params['resource_id'] = $resource_id;
        }
        if (!empty($params['reviewer_name'])) {
            $user_id = array();
            $list_review_for = Engine_Api::_()->ynmember()->getUsersByName($params['reviewer_name']);
            foreach ($list_review_for as $item) {
                $user_id[] = $item->getIdentity();
            }
            $params['user_id'] = $user_id;
        }
        $page = $this->_getParam('page', 1);
        $params['page'] = $page;
        $params['isAdmin'] = true;
        $this->view->paginator = $paginator = $tableReview->getReviewPaginator($params);
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $this->view->form = $form = new Ynmember_Form_Admin_Review_Delete();
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $review = Engine_Api::_()->getItem('ynmember_review', $id);
        if ($review) {

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

            $review->delete();
        }
        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Review deleted.')),
            'format' => 'smoothbox',
            'smoothboxClose' => true,
            'parentRefresh' => true,
        ));
    }

    public function multiselectedAction()
    {
        $action = $this->_getParam('select_action', 'Delete');
        $this->view->action = $action;
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);

        // Check post
        if ($this->getRequest()->isPost() && $confirm == true) {
            $ids_array = explode(",", $ids);
            switch ($action) {
                case 'Delete':
                    foreach ($ids_array as $id) {
                        $review = Engine_Api::_()->getItem('ynmember_review', $id);
                        if ($review) {

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

                            $review->delete();
                        }
                    }
                    break;
            }
            $this->_helper->redirector->gotoRoute(array('action' => ''));
        }
    }

    public function viewDetailAction()
    {
        $id = $this->_getParam('id');
        $review = Engine_Api::_()->getItem('ynmember_review', $id);
        $this->view->review = $review;

        //get rating
        $tableRating = Engine_Api::_()->getItemTable('ynmember_rating');
        $this->view->ratings = $ratings = $tableRating->fetchAll($tableRating->select()->where('review_id = ?', $id));

        // Load fields view helpers
        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    }
}