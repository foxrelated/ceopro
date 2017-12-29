<?php
class Advgroup_ListingsController extends Core_Controller_Action_Standard {
	public function init() {
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
		}
        
        $listing_enable = Engine_Api::_()->hasModuleBootstrap('ynlistings');
        
        if (!$listing_enable) {
            return $this -> _helper -> requireSubject -> forward();
        }
	}
	
    public function listAction() {
        $this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
        //check auth create
        $this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->canCreate = $canCreate = $group -> authorization() -> isAllowed($viewer, 'listing');
        
        if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer)) {
            $parent_group = $group -> getParentGroup();
            if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
                return $this -> _helper -> requireAuth -> forward();
            }
            else if (!$group -> authorization() -> isAllowed($viewer, "view")) {
                return $this -> _helper -> requireAuth -> forward();
            }
        }
        else if (!$group -> authorization() -> isAllowed($viewer, 'view')) {
            return $this -> _helper -> requireAuth -> forward();
        }
        //Get Search Form
        $this -> view -> form = $form = new Advgroup_Form_Listing_Search();

        //Get search condition
        $params = array();
        $params['group_id'] = $group -> getIdentity();
        $params['search'] = $this -> _getParam('search', '');
        $params['order'] = $this -> _getParam('order', 'recent');
        $params['manage'] = 1;
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'order' => $params['order'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        $params['ItemTable'] = 'ynlistings_listing';
        
        $this -> view -> ItemTable = $params['ItemTable'];
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'advgroup') -> getListingsPaginator($params);
        
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 10));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
    }

    public function deleteAction() {       // In smoothbox
        $this->_helper->layout->setLayout('default-simple');  
        $this->view->form = $form = new Advgroup_Form_Listing_Delete();
        
        if( !$this->getRequest()->isPost() ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $params = $this -> _getAllParams();
        $result = Engine_Api::_() -> getItemTable('advgroup_mapping') -> deleteItem($params);
        if($result != "true") {
            die($result);
        }

        $group = Engine_Api::_() -> getItem('group', $params['group_id']);
        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Listing has been removed')),
            'closeSmoothbox' => true,
            'parentRefresh' => true
        ));
        
        
    }
}
?>
