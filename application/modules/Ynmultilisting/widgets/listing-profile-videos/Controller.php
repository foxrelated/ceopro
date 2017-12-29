<?php
class Ynmultilisting_Widget_ListingProfileVideosController extends Engine_Content_Widget_Abstract{
	protected $_childCount;
    public function indexAction(){
     // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() || !in_array(Engine_Api::_()->ynmultilisting()->getMultilistingVideoType(), array('video', 'ynvideo'))) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $this->view->listing = $subject = Engine_Api::_()->core()->getSubject('ynmultilisting_listing');

	//check auth with package
	if (in_array($subject -> status, array('draft', 'expired'))){
		return $this -> setNoRender();
	}
	
	$package = $subject -> getPackage();
	if(!$package -> getIdentity()){
		return $this -> setNoRender();
	}
	
	if(!$package -> allow_video_tab)
	{
		return $this -> setNoRender();
	}
		
	// Get paginator
    $mappingTable = Engine_Api::_()->getDbTable('mappings', 'ynmultilisting');
	$params['listing_id'] = $subject -> getIdentity();
    $this->view->paginator = $paginator = $mappingTable->getWidgetVideosPaginator($params);
    
    // Set item count per page and current page number
    $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 6));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    
    $this->view->canUpload = $canUpload = $subject->isAllowed('video');
  }
	 
  public function getChildCount()
  {
    return $this->_childCount;
  }
}
?>
