<?php
class Ynmember_Widget_SearchReviewController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        // Make form
        $this->view->form = $form = new Ynmember_Form_Search_Review();
        
        // Process form
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if (!$form->isValid($p)) {
            return false;
        }
    }
}