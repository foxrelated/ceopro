<?php
class Ynmultilisting_Widget_ListingProfileRelatedListingsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if( !Engine_Api::_()->core()->hasSubject('ynmultilisting_listing')) {
            return $this->setNoRender();
        }
        $num_of_listings = $this->_getParam('num_of_listings', 6);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject('ynmultilisting_listing');
        
        if(!$subject->isAllowed('view')){
            return $this->setNoRender();
        } 
        $listing = $subject;
        $category = Engine_Api::_()->getItem('ynmultilisting_category', $listing->category_id);
        if ($category) {
            $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
            $tableName = $table->info('name');
            if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
                $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
            } else {
                $select = $table->select()->from("$tableName", array("$tableName.*"));
            }
            $select
                ->where('listing_id <> ?', $listing->getIdentity())
                ->where('category_id = ?', $category->getIdentity())
                ->where('approved_status = ?', 'approved')
                ->where('status = ?', 'open')
				->where('deleted = ?', '0')
                ->order(new Zend_Db_Expr(('rand()')))
                ->limit($num_of_listings);
            $listingsSameCategory = $table->fetchAll($select);
        }
        else {
            $listingsSameCategory = array();
        }
        if (count($listingsSameCategory) == 0) {
            return $this->setNoRender(true);
        }
        $this->view->listings = $listingsSameCategory;
    }
}
