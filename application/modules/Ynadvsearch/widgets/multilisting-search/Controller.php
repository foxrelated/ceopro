<?php
class Ynadvsearch_Widget_MultilistingSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        if($multilisting_enable) {
            return $this -> setNoRender();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->form = $form = new Ynadvsearch_Form_MultilistingSearch(array(
            'type' => 'ynmultilisting_listing'
        ));

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
		
		$listingTypeId = $request->getParam('listingtype_id', 0);		
		$listingType = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingTypeId);
        if ($listingTypeId && $listingType) {
            $categories = $listingType -> getCategories();
            //unset($categories[0]);
            if (count($categories) > 0) {
                foreach ($categories as $category) {
                    $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1).$category -> getTitle());
                }
            }
        }
		
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