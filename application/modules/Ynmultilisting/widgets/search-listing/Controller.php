<?php

class Ynmultilisting_Widget_SearchListingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $location = $request -> getParam('location', '');
        $this->view->form = $form = new Ynmultilisting_Form_Search(array(
            'type' => 'ynmultilisting_listing',
            'location' => $location
        ));
        $listingType = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if ($listingType) {
            $categories = $listingType->getCategories();
            if (count($categories) > 0) {
                foreach ($categories as $category) {
                    $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1) . $category->getTitle());
                }
            }
        }

        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynmultilisting') {
            if ($controller == 'index' && ($action == 'manage' || $action == 'browse')) {
                $forwardListing = false;
            }
            if ($action != 'manage') {
                $form->removeElement('status');
            }
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'browse'), 'ynmultilisting_general', true));
        }

        if (!$listingType) {
            $form->removeElement('category');
        }

        // Process form
        $p = $request->getParams();
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