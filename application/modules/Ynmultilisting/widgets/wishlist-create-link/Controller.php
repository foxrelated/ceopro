<?php
class Ynmultilisting_Widget_WishlistCreateLinkController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		
       $viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity()) {
            return $this->setNoRender();
        }
    }
}
	