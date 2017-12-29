<?php
class Ynmultilisting_Widget_ListingProfileAlbumsController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function indexAction()
	{
		if ($this -> _getParam('itemCountPerPage') != '' && $this -> _getParam('itemCountPerPage') >= 0)
		{
			$itemCountPerPage = $this -> _getParam('itemCountPerPage');
		}
		else
		{
			$itemCountPerPage = 6;
		}
		// Don't render this if not authorized
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> setNoRender();
		}
		// Get subject and check auth
		$this -> view -> listing = $subject = Engine_Api::_() -> core() -> getSubject('ynmultilisting_listing');
		
		//check auth with package
		if (in_array($subject -> status, array('draft', 'expired'))){
			return $this -> setNoRender();
		}
		
		$package = $subject -> getPackage();
		if(!$package -> getIdentity()){
			return $this -> setNoRender();
		}
		
		if(!$package -> allow_photo_tab)
		{
			return $this -> setNoRender();
		}
		
		// Get paginator
		$params['listing_id'] = $subject -> listing_id;
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynmultilisting_album') -> getAlbumsPaginator($params);

		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', $itemCountPerPage));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		// Add count to title if configured
		if ($this -> _getParam('titleCount', false) && $paginator -> getTotalItemCount() > 0)
		{
			$this -> _childCount = $paginator -> getTotalItemCount();
		}
		$this -> view -> canUpload = $subject->isAllowed('photo');
        
	}

	public function getChildCount()
	{
		return $this -> _childCount;
	}

}
?>
