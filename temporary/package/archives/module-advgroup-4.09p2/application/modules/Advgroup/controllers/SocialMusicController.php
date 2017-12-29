<?php
class Advgroup_SocialMusicController extends Core_Controller_Action_Standard {
    public function init() {
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id))) {
                Engine_Api::_() -> core() -> setSubject($group);
            }
        }
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return $this -> _helper -> requireSubject -> forward();
        }
    }
    
    public function deleteAction() {        // In smoothbox
    	$this->_helper->layout->setLayout('default-simple');  
		$type = $this->_getParam('type');
    	$this->view->form = $form = new Advgroup_Form_SocialMusic_Delete(array('type'=>$type));
        
    	if( !$this->getRequest()->isPost()) {
      		return;
    	}
		
        $params = $this -> _getAllParams();
        $result = Engine_Api::_() -> getItemTable('advgroup_mapping') -> deleteItem($params);
        if($result != "true") {
            die($result);
        }
    
		$label = ($type == 'ynmusic_album') ? $this->view->translate('Album') : $this->view->translate('Song');
		$musicType = ($type == 'ynmusic_album') ? 'album' : 'song';
        $group = Engine_Api::_() -> getItem('group', $params['group_id']);
        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array($this->view->translate('%s has been deleted', $label)),
            'layout' => 'default-simple',
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                'controller' => 'social-music',
                'action' => 'list',
                'subject' => $group -> getGuid(),
                'type' => $musicType,
            ), 'group_extended', true),
            'closeSmoothbox' => true,
        ));
        
        
    }

    public function listAction() {
        $music_enable = Engine_Api::_() -> hasModuleBootstrap('ynmusic');
        
        if (!$music_enable) {
            return $this -> _helper -> requireSubject -> forward();
        }
		
        $this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $canCreate = $group -> authorization() -> isAllowed(null, 'music');
        
        $levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'music');
        if ($canCreate && $levelCreate) {
            $this -> view -> canCreate = true;
        } else {
            $this -> view -> canCreate = false;
        }
        
        //Get Viewer, Group and Search Form
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
        $this -> view -> form = $form = new Advgroup_Form_SocialMusic_Search();


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
            
        //Get search condition
        $params = array();
        $params['group_id'] = $group -> getIdentity();
        $params['search'] = $this -> _getParam('search', '');
        $params['browse_by'] = $this -> _getParam('browse_by', 'recently_created');
		$params['type'] = $this -> _getParam('type', 'album');
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'browse_by' => $params['browse_by'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        $params['ItemTable'] = 'ynmusic_'.$params['type'];
        
		$this -> view -> type = $params['type'];
        $this -> view -> ItemTable = $params['ItemTable'];
        //Get Album paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_mapping') -> getSocialMusicPaginator($params);
    
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		$timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
    }

    

}
?>
