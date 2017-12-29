<?php
class Ynmultilisting_Widget_ListingProfileReviewsController extends Engine_Content_Widget_Abstract {
   protected $_childCount;
  
    public function indexAction() {
        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
		
        if( !Engine_Api::_()->core()->hasSubject('ynmultilisting_listing')) {
       		 return $this->setNoRender();
        }
    	
        // Get subject
        $this->view->listing = $listing = Engine_Api::_()->core()->getSubject('ynmultilisting_listing');
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $table = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $select = $table->select();
        $select
            ->where('listing_id = ?', $listing->getIdentity())
            ->where('user_id <> '.$viewer->getIdentity())
            ->order('modified_date');
			
        $this->view->paginator = $paginator = Zend_Paginator::factory($table->fetchAll($select));
        
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this -> _getParam('itemCountPerPage', 4));
        $paginator->setCurrentPageNumber($this -> _getParam('page', 1));
        $this->view->page = $paginator->getCurrentPageNumber();
        // Add count to title if configured
        if( $this->_getParam('titleCount', true) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        
        $select->reset();
        $select
            ->where('listing_id = ?', $listing->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity());  
        $this->view->my_review = $my_review = $table->fetchRow($select);
        if ($my_review) {
            $this->_childCount++;
        }
        
        $this->view->can_review = $can_review = $listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
    }

    public function getChildCount() {
        return $this->_childCount;
    }
}