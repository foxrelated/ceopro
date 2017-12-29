<?php
class Ynadvsearch_Widget_MemberSearchController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $ynmember_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynmember');
        if($ynmember_enable) {
            return $this -> setNoRender();
        }
        $this->view->form = $form = new User_Form_Search(array(
            'type' => 'user'
        ));
        $form->setAttrib('id', 'filter_form');
        // Process form
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if ($form->isValid($p)) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = $values;
        $this->view->topLevelId = $form->getTopLevelId();
        $this->view->topLevelValue = $form->getTopLevelValue();
    }
}