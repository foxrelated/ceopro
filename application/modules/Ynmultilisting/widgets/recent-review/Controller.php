<?php
/**
 * @author HoangND
 */
class Ynmultilisting_Widget_RecentReviewController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if (is_null($listingtype))
        {
            return $this -> setNoRender();
        }
		$listings = $listingtype->getAllListings(array('publish' => true));
		$listing_ids = array();
		foreach ($listings as $listing) {
			if ($listing->isAllowed('view')) {
				$listing_ids[] = $listing->getIdentity();
			}
		}
		$reviewTbl = Engine_Api::_()->getItemTable('ynmultilisting_review');
		$this -> view -> paginator = $paginator = $reviewTbl -> getReviewsPaginator(array('listingtype_id'=>$listingtype->getIdentity(), 'listing_ids'=>$listing_ids));
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		if (!$paginator -> getTotalItemCount()) {
			return $this -> setNoRender();
		}
    }
}