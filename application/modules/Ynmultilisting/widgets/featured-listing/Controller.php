<?php

class Ynmultilisting_Widget_FeaturedListingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        $view_mode = $listingtype->feature_widget;
        $session = new Zend_Session_Namespace('mobile');
        if ($session->mobile) {
            $view_mode = '1';
        }
        $currentListingTypeId = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
        $this->view->view_mode = $view_mode;
        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $tableName = $table->info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
        } else {
            $select = $table->select()->from("$tableName", array("$tableName.*"));
        }
        $num_of_listings = $this->_getParam('num_of_listings', 6);
        $select
            ->where('featured = ?', 1)
            ->where('status = ?', 'open')
            ->where('approved_status = ?', 'approved')
            ->where('listingtype_id = ?', $currentListingTypeId)
            ->where('deleted = ?', '0')
            ->order("RAND()")
            ->limit($num_of_listings);
        $this->view->listings = $listings = $table->fetchAll($select);
        if (count($listings) == 0) {
            $this->setNoRender(true);
        }
    }
}