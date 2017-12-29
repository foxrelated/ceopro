<?php
class Ynmultilisting_Widget_WishlistListingController extends Engine_Content_Widget_Abstract {
 	public function indexAction() {
       	$form = new Ynmultilisting_Form_Wishlist_Search();
		$request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request ->getParams();
        if ($form->isValid($params)) {
			$params = $form->getValues();
		}
		else
			$params = array();
		$this->view->formValues = array_filter($params);

		$params['listingtype_id'] = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		$table = Engine_Api::_()->getItemTable('ynmultilisting_wishlist');
       	$select = $table->getWishlistSelect($params);
		$wishlists = $table->fetchAll($select);
		$availableWishlists = array();
		foreach ($wishlists as $wishlist) {
			if ($wishlist->isViewable() && $wishlist->hasListings()) {
				$availableWishlists[] = $wishlist;
			}
		}
        $limit = $this->_getParam('itemCountPerPage', 10);

		$this->view->paginator = $paginator = Zend_Paginator::factory($availableWishlists);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($request -> getParam('page', 1) );
	}
}