<?php
class Ynmultilisting_WishlistController extends Core_Controller_Action_Standard {
    public function indexAction() {
        $this->_helper->content->setEnabled()->setNoRender();
    }
    
    public function viewAction() {
        $this->_helper->content->setEnabled();
		$id = $this->_getParam('id', 0);
		$this->view->wishlist = $wishlist = Engine_Api::_()->getItem('ynmultilisting_wishlist', $id);
		if (!$id || !$wishlist) {
			return $this->_helper->requireSubject()->forward();
		}
		
		$listings = $wishlist->getAllListings();
		$this->view->paginator = $paginator = Zend_Paginator::factory($listings);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
		
		$listingIds = array();
        foreach ($paginator as $listing){
            $listingIds[] = $listing -> getIdentity();
        }
        $this->view->listingIds = implode("_", $listingIds);
		
		$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);
		$mode_list = $mode_grid = $mode_pin = $mode_map = 1;
        $mode_enabled = array();
        $view_mode = 'list';

        if(isset($params['mode_list']))
        {
            $mode_list = $params['mode_list'];
        }
        if($mode_list)
        {
            $mode_enabled[] = 'list';
        }
        if(isset($params['mode_grid']))
        {
            $mode_grid = $params['mode_grid'];
        }
        if($mode_grid)
        {
            $mode_enabled[] = 'grid';
        }
        if(isset($params['mode_pin']))
        {
            $mode_pin = $params['mode_pin'];
        }
        if($mode_pin)
        {
            $mode_enabled[] = 'pin';
        }
        if(isset($params['mode_map']))
        {
            $mode_map = $params['mode_map'];
        }
        if($mode_map)
        {
            $mode_enabled[] = 'map';
        }
        if(isset($params['view_mode']))
        {
            $view_mode = $params['view_mode'];
        }

        if($mode_enabled && !in_array($view_mode, $mode_enabled))
        {
            $view_mode = $mode_enabled[0];
        }

        $this -> view -> mode_enabled = $mode_enabled;

        $class_mode = "ynmultilisting_list-view";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "ynmultilisting_grid-view";
                break;
            case 'map':
                $class_mode = "ynmultilisting_map-view";
                break;
            case 'pin':
                $class_mode = "ynmultilisting_pin-view";
                break;
            default:
                $class_mode = "ynmultilisting_list-view";
                break;
        }
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;
    }

    public function manageAction() {
        $this->_helper->content->setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid())
            return;
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_()->getItemTable('ynmultilisting_wishlist');
       	$select = $table->getWishlistSelect(array('user_id' => $viewer->getIdentity(), 'listingtype_id' => Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId()));
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));		
    }
	
	public function addAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		if (!$this -> _helper -> requireUser() -> isValid())
            return;
		$id = $this->_getParam('listing_id', 0);
		$listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
		if (!$id || !$listing) {
			return $this->_helper->requireSubject()->forward();
		}
		$this->view->form = $form = new Ynmultilisting_Form_Wishlist_Add();
		$wishlists = Engine_Api::_()->getItemTable('ynmultilisting_wishlist')->getAvailableWishlists($viewer->getIdentity(), $listingtype_id);
		foreach ($wishlists as $wishlist) {
			if (!$listing->inWishlist($wishlist->getIdentity())) {
				$form->wishlist_id->addMultiOption($wishlist->getIdentity(), $wishlist->getTitle());
			}
		}
		
		if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
		if (isset($values['wishlist_id']) && ($values['wishlist_id'] == '0') && (trim($values['title']) == "")) {
			$form->addError($this->view->translate('Wish List name must not be empty!'));
			return;
		}
		$wishlist_id = $values['wishlist_id'];
		unset($values['wishlist_id']);
		if ($wishlist_id == 0) {
			$table = Engine_Api::_()->getItemTable('ynmultilisting_wishlist');
			$values['listingtype_id'] = $listingtype_id;
			$values['user_id'] = $viewer->getIdentity();
			$wishlist = $table->createRow();
			$wishlist->setFromArray($values);
			$wishlist->save();
			
			//set auth for view
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_role = $values['view'];
            if (!$auth_role) {
                $auth_role = 'everyone';
            }
            $roleMax = array_search($auth_role, $roles);
            foreach ($roles as $i=>$role) {
               $auth->setAllowed($wishlist, $role, 'view', ($i <= $roleMax));
            }    
			
			$wishlist_id = $wishlist->getIdentity();
		}
		
		$listing->addToWishlist($wishlist_id);
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> false,
            'messages' => array($this->view->translate('Add to Wish List successfully!'))
        ));
	}
	
	public function addToMyAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		if (!$this -> _helper -> requireUser() -> isValid())
            return;
		
		$id = $this->_getParam('id', 0);
		$wishlistToAdd = Engine_Api::_()->getItem('ynmultilisting_wishlist', $id);
		if (!$id || !$wishlistToAdd) {
			return $this->_helper->requireSubject()->forward();
		}
		if (!$wishlistToAdd->isViewable()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		if ($wishlistToAdd->isOwner($viewer)) {
			$this->_forward('success', 'utility', 'core', array(
	            'smoothboxClose' => true,
	            'parentRefresh'=> false,
	            'messages' => array($this->view->translate('This wish list is already yours!'))
	        ));
			return;
		}

		$this->view->form = $form = new Ynmultilisting_Form_Wishlist_Add();
		$wishlists = Engine_Api::_()->getItemTable('ynmultilisting_wishlist')->getAvailableWishlists($viewer->getIdentity(), $listingtype_id);
		foreach ($wishlists as $wishlist) {
			$form->wishlist_id->addMultiOption($wishlist->getIdentity(), $wishlist->getTitle());
		}
		
		if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
		if (isset($values['wishlist_id']) && ($values['wishlist_id'] == '0') && empty($values['title'])) {
			$form->addError($this->view->translate('Wish List name must not be empty!'));
			return;
		}
		$wishlist_id = $values['wishlist_id'];
		unset($values['wishlist_id']);
		if ($wishlist_id == 0) {
			$table = Engine_Api::_()->getItemTable('ynmultilisting_wishlist');
			$values['listingtype_id'] = $listingtype_id;
			$values['user_id'] = $viewer->getIdentity();
			$wishlist = $table->createRow();
			$wishlist->setFromArray($values);
			$wishlist->save();
			
			//set auth for view
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_role = $values['view'];
            if (!$auth_role) {
                $auth_role = 'everyone';
            }
            $roleMax = array_search($auth_role, $roles);
            foreach ($roles as $i=>$role) {
               $auth->setAllowed($wishlist, $role, 'view', ($i <= $roleMax));
            }    
			
			$wishlist_id = $wishlist->getIdentity();
		}
		
		$wishlistToAdd->addToWishlist($wishlist_id);
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array($this->view->translate('Add to Wish List successfully!'))
        ));
	}

	public function deleteAction() {
        $this -> _helper -> layout -> setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$this -> _helper -> requireUser() -> isValid())
            return;
		$id = $this->_getParam('id', 0);
		$wishlist = Engine_Api::_()->getItem('ynmultilisting_wishlist', $id);
		if (!$id || !$wishlist) {
			return $this->_helper->requireSubject()->forward();
		}
		if (!$wishlist->isOwner($viewer)) {
			return $this->_helper->requireAuth()->forward();
		}
        $this->view->id = $id;
		
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $wishlist->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array($this->view->translate('This Wish List has been deleted.'))
            ));
        }
    }

	public function createAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		if (!$this -> _helper -> requireUser() -> isValid())
            return;
		$this->view->form = $form = new Ynmultilisting_Form_Wishlist_Create();
		
		if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
		$values['listingtype_id'] = $listingtype_id;
		$values['user_id'] = $viewer->getIdentity();
		
		$table = Engine_Api::_()->getDbtable('wishlists', 'ynmultilisting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $wishlist = $table->createRow();
            $wishlist->setFromArray($values);
            $wishlist->save();
			
			//set auth for view
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_role = $values['view'];
            if (!$auth_role) {
                $auth_role = 'everyone';
            }
            $roleMax = array_search($auth_role, $roles);
            foreach ($roles as $i=>$role) {
               $auth->setAllowed($wishlist, $role, 'view', ($i <= $roleMax));
            }
			
            $db->commit();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }     
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'ynmultilisting_wishlist', true),
            'messages' => array($this->view->translate('Create Wish List successfully!'))
        ));
	}

	public function editAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
		if (!$this -> _helper -> requireUser() -> isValid())
            return;
		
		$id = $this->_getParam('id', 0);
		$wishlist = Engine_Api::_()->getItem('ynmultilisting_wishlist', $id);
		if (!$id || !$wishlist) {
			return $this->_helper->requireSubject()->forward();
		}
		if (!$wishlist->isOwner($viewer)) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this->view->form = $form = new Ynmultilisting_Form_Wishlist_Edit();
		
		$form->populate($wishlist->toArray());
		
		$auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $role) {
            if(1 === $auth->isAllowed($wishlist, $role, 'view')) {
                if ($form->view)
                $form->view->setValue($role);
            }
        }    
		
		if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
		
		$table = Engine_Api::_()->getDbtable('wishlists', 'ynmultilisting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $wishlist->setFromArray($values);
            $wishlist->save();
			
			//set auth for view
            $auth_role = $values['view'];
            if (!$auth_role) {
                $auth_role = 'everyone';
            }
            $roleMax = array_search($auth_role, $roles);
            foreach ($roles as $i=>$role) {
               $auth->setAllowed($wishlist, $role, 'view', ($i <= $roleMax));
			}	
            $db->commit();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }     
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'ynmultilisting_wishlist', true),
            'messages' => array($this->view->translate('Edit Wish List successfully!'))
        ));
	}

	public function removeListingAction() {
        $this -> _helper -> layout -> setLayout('default-simple');
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$this -> _helper -> requireUser() -> isValid())
            return;
		$id = $this->_getParam('id', 0);
		$wishlist = Engine_Api::_()->getItem('ynmultilisting_wishlist', $id);
		if (!$id || !$wishlist) {
			return $this->_helper->requireSubject()->forward();
		}
		if (!$wishlist->isOwner($viewer)) {
			return $this->_helper->requireAuth()->forward();
		}
        $this->view->id = $id;
		
		$listing_id = $this->_getParam('listing_id', 0);
		$this->view->listing = $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $listing_id);
		if (!$listing_id || !$listing) {
			return $this->_helper->requireSubject()->forward();
		}
		$this->view->listing_id = $listing_id;
		
		if (!$listing->inWishlist($id)) {
			$this->view->error = true;
			$this->view->message = $this->view->translate('Listing %s is not in this Wish List', $listing->getTitle());
			return;
		}
        // Check post
        if( $this->getRequest()->isPost()) {
            
			$wishlist->removeListing($listing_id);

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array($this->view->translate('Listing %s has been remove from this Wish List.', $listing->getTitle()))
            ));
        }
    }
}
