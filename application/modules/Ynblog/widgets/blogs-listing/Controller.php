<?php

class Ynblog_Widget_BlogsListingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        // setup view mode
        $view_mode = $this->_getParam('view_mode', 'list');
        $mode_enabled = array();
        if ($this->_getParam('mode_grid', 0)) {
            $mode_enabled[] = 'grid';
        }
        if ($this->_getParam('mode_list', 0)) {
            $mode_enabled[] = 'list';
        }
        if (!in_array($view_mode, $mode_enabled) && $mode_enabled) {
            $view_mode = $mode_enabled[0];
        }
        $this->view->mode_enabled = $mode_enabled;
        $this->view->view_mode = $view_mode;
        //Search Params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $form = new Ynblog_Form_Search();
        // Process form
        if( $form->isValid($params) ) {
            $params = $form->getValues();
        } else {
            $params = array();
        }
        $this->view->formValues = $params;
        if (!empty($params['user_id']))
            $this->view->owner = $owner = Engine_Api::_()->getItem('user', $params['user_id']);
        $params['draft'] = 0;
        $params['is_approved'] = 1;
        $params['visible'] = 1;

        // Do the show thingy
        if (isset($params['show']) && $params['show'] == 2) {
            // Get an array of friend ids
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        //Get blog paginator
        $paginator = Engine_Api::_()->ynblog()->getBlogsPaginator($params);
        $items_per_page = $this->_getParam('max', 10);
        $paginator->setItemCountPerPage($items_per_page);

        if (isset($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        $this->view->paginator = $paginator;
    }
}
