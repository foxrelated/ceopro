<?php
class Ynmultilisting_ProfileController extends Core_Controller_Action_Standard {
	
	public function init() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = null;
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			$id = $this -> _getParam('id');
			if (null !== $id) {
				$subject = Engine_Api::_() -> getItem('ynmultilisting_listing', $id);
				if ($subject && $subject -> getIdentity()) {
					Engine_Api::_() -> core() -> setSubject($subject);
				} else {
					return $this -> _helper -> requireSubject() -> forward();
				}
				// Check authorization to view listing.
				if (!$subject->isAllowed('view')) {
				    return $this -> _helper -> requireAuth() -> forward();
				}
			}
		}
		$this -> _helper -> requireSubject('ynmultilisting_listing');
		$listingTypeId = $subject -> getListingType() -> getIdentity();
		$currentListingTypeId = Engine_Api::_() -> ynmultilisting() -> getCurrentListingTypeId();
		if($listingTypeId != $currentListingTypeId){
			Engine_Api::_() -> ynmultilisting() -> setCurrentListingType($listingTypeId);
		}		
	}

	public function indexAction() {
		
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject() -> forward();
		}
		$this -> view -> listing = $subject = Engine_Api::_() -> core() -> getSubject();
        
		if($subject -> deleted)
		{
			return $this -> _helper -> requireSubject() -> forward();
		}
		
		// Check authorization to view listing.
		if (!$subject->isAllowed('view')) {
		    return $this -> _helper -> requireAuth() -> forward();
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($subject -> status != 'open' && $subject -> approved_status != 'approved') {
			if (!$viewer -> isAdmin() && !$viewer -> isSelf($subject -> getOwner())) {
				return $this -> _helper -> requireAuth() -> forward();
			}
		}

        $view = Zend_Registry::get('Zend_View');
        $og = '<meta property="og:title" content="' . $subject->getTitle() . '" />';
        $og .= '<meta property="og:description" content="' . $view->string()->striptags($subject->getDescription()) . '" />';
        $og .= '<meta property="og:url" content="' . $subject->getHref() . '" />';
        $og .= '<meta property="og:updated_time" content="' . strtotime($subject->modified_date) . '" />';
        $og .= '<meta property="og:type" content="website" />';
        $view->layout()->headIncludes .= $og;

		$this -> _helper -> content -> setEnabled();
	}
	
	public function mobileAction() {
		
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject() -> forward();
		}
		$this -> view -> listing = $subject = Engine_Api::_() -> core() -> getSubject();
        
		if($subject -> deleted)
		{
			return $this -> _helper -> requireSubject() -> forward();
		}
		
		// Check authorization to view listing.
		if (!$subject->isAllowed('view')) {
		    return $this -> _helper -> requireAuth() -> forward();
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($subject -> status != 'open' && $subject -> approved_status != 'approved') {
			if (!$viewer -> isAdmin() && !$viewer -> isSelf($subject -> getOwner())) {
				return $this -> _helper -> requireAuth() -> forward();
			}
		}
		
		$this -> _helper -> content -> setEnabled();
	}
	
}
