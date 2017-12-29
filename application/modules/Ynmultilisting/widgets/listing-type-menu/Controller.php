<?php
class Ynmultilisting_Widget_ListingTypeMenuController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->listingtypes = $listingtypes = Engine_Api::_()->getDbTable('listingtypes', 'ynmultilisting')->getAvailableListingTypes();
        if (!count($listingtypes)) {
            $this->setNoRender();
        }
        $this->view->current_listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
    }
}
