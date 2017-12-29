<?php
class Ynmultilisting_Widget_SearchReviewController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
        // Make form
        $this->view->form = $form = new Ynmultilisting_Form_Review_Search();

        // Process form
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if ($form->isValid($p)) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = $values;
	}
}