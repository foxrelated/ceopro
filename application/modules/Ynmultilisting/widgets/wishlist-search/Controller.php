<?php
class Ynmultilisting_Widget_WishlistSearchController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->form = $form = new Ynmultilisting_Form_Wishlist_Search();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynmultilisting') {
            if ($controller == 'wishlist' && ($action == 'index')) {
                $forwardListing = false;
            }
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'ynmultilisting_wishlist', true));
        }

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