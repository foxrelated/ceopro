<?php

class Ynmember_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Check form
        $form = new Ynmember_Form_Search(array(
            'type' => 'user'
        ));

        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if (empty($params['within']))
            $params['within'] = 50;
        if (empty($params['long']))
            $params['long'] = 0;
        if (empty($params['lat']))
            $params['lat'] = 0;

        if (!$form->isValid($params)) {
            $this->view->error = true;
            $this->view->totalUsers = 0;
            $this->view->userCount = 0;
            $this->view->page = 1;
            return false;
        }
        $this->view->form = $form;
    }
}