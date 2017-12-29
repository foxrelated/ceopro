<?php
class Ynmultilisting_Widget_ProfileListingsController extends Engine_Content_Widget_Abstract {
    protected $_childCount;
  
    public function indexAction() {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
        return $this->setNoRender();
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
            return $this->setNoRender();
        }

        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');

        // Get paginator
        $listingTbl = Engine_Api::_() -> getItemTable('ynmultilisting_listing');
    	$listingTblName = $listingTbl -> info('name');
		$listingtypeTbl = Engine_Api::_() -> getItemTable('ynmultilisting_listingtype');
    	$listingtypeTblName = $listingtypeTbl -> info('name');

        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
        }
        else
        {
            $select = $listingTbl->select()->from("$listingTblName", array("$listingTblName.*"));
        }
    	$select -> setIntegrityCheck(false);
		$select -> joinLeft("$listingtypeTblName","$listingtypeTblName.listingtype_id = $listingTblName.listingtype_id", "");
		$select -> where("$listingTblName.user_id = ?", $subject->getIdentity());
		$select
    			-> where("$listingTblName.search = ?", 1)
    			-> where("$listingTblName.status = ?", 'open')
    			-> where("$listingTblName.approved_status = ?", 'approved');
		$select -> order ("$listingtypeTblName.order ASC");
		$select -> order ("$listingTblName.approved_date DESC");
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if( $paginator->getTotalItemCount() <= 0 ) {
            return $this->setNoRender();
        }

        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }
}