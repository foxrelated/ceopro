<?php
class Ynmultilisting_Widget_QuickLinkLinkController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$oriId = $listingtype_id = $this->_getParam('listingtype', 0);
		if (!$listingtype_id || $listingtype_id == 'all')
			$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		$listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
		if (!$listingtype) {
			$this->setNoRender();
			return;
		}
		$quicklink_ids = $this->_getParam('quicklinks');
		$params = array('show' => 1);
		if ($listingtype_id != 0) {
			$params['ids'] = $quicklink_ids;
		}
		if ($oriId == 'all') {
			$params['all'] = true;
		}
		$this->view->quicklinks = $quicklinks = $listingtype->getQuicklinks($params);
		
		if (!count($quicklinks)) {
			$this->setNoRender();
		}
	}
}