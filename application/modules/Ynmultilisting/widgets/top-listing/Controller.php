<?php

class Ynmultilisting_Widget_TopListingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $num_of_listings = $this->_getParam('num_of_listings', 3);
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if ($listingtype) {
            $listingTbl = Engine_Api::_()->getItemTable('ynmultilisting_listing');
            $select = $listingTbl->select()
                ->from($listingTbl->info('name'), array(new Zend_Db_Expr('DISTINCT category_id')))
                ->where("listingtype_id = ?", $listingtype->listingtype_id)
                ->limit(1)
                ->order(" RAND() ");
            $categoyId = $listingTbl->fetchRow($select)->category_id;
            $this->view->category = $category = Engine_Api::_()->getItem('ynmultilisting_category', $categoyId);
            $tableName = $listingTbl->info('name');
            if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
                $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
            } else {
                $select = $listingTbl->select()->from("$tableName", array("$tableName.*"));
            }
            $select
                ->where('search = ?', 1)
                ->where('status = ?', 'open')
                ->where('approved_status = ?', 'approved')
                ->where('deleted = ?', 0)
                ->where('category_id = ? ', $categoyId)
                ->limit($num_of_listings);
            $this->view->listings = $listings = $listingTbl->fetchAll($select);
            if (!count($listings)) {
                $this->setNoRender(true);
            }
            $this->getElement()->removeDecorator('Title');
        } else {
            $this->setNoRender(true);
        }
    }
}