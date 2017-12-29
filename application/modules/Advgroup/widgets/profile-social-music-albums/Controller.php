<?php
class Advgroup_Widget_ProfileSocialMusicAlbumsController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function init() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id))) {
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
	}
	
	public function indexAction() {
        // Don't render if blog item not available
        if( !Engine_Api::_()->hasModuleBootstrap('ynmusic')) {
            return $this->setNoRender();
        }
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }
        
        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');
        
        // Get subject and check auth
        $this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
    
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> canCreate = $canCreate = $group -> authorization() -> isAllowed(null, 'music');
		
		if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer)) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				$this -> setNoRender();
			}
			else if (!$group -> authorization() -> isAllowed($viewer, "view")) {
				$this -> setNoRender();
			}
		}
		else if (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			$this -> setNoRender();
		}
		
        //Get search condition
        $params = array();
        $params['group_id'] = $group -> getIdentity();
        $params['ItemTable'] = 'ynmusic_album';
        $params['browse_by'] = 'recently_created';
		$params['type'] = 'album';
        //Get paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_mapping') -> getSocialMusicPaginator($params);
        $itemCountPerPage = $this -> _getParam('itemCountPerPage', 8);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 8;
        }
        $paginator -> setItemCountPerPage($itemCountPerPage);
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
        
        // Do not render if nothing to show and cannot create
        if ($paginator -> getTotalItemCount() <= 0 && !$canCreate) {
            return $this -> setNoRender();
        }
        
        // Add count to title if configured
        if( $this->_getParam('titleCount', false)) {
           $this->_childCount = $paginator -> getTotalItemCount();   
        }
        
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
		$this->view->formValues = array();
  	}

	public function getChildCount() {
        return $this->_childCount;
    }
}