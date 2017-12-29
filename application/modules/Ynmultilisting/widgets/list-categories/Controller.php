<?php
class Ynmultilisting_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 
                'application/modules/Ynmultilisting/externals/scripts/collapsible.js');
        
        $listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting') -> getListingTypeCategories($listingtype_id);
        unset($categories[0]);
        $this->view->categories = $categories;
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
    }
}