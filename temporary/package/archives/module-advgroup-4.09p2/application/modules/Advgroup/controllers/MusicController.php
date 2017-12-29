<?php
class Advgroup_MusicController extends Core_Controller_Action_Standard
{
    public function init()
    {
        if (!Engine_Api::_() -> core() -> hasSubject())
        {
            if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
            {
                Engine_Api::_() -> core() -> setSubject($group);
            }
        }
        if (!Engine_Api::_() -> core() -> hasSubject())
        {
            return $this -> _helper -> requireSubject -> forward();
        }
    }
    
    public function deleteAction() {        // In smoothbox
    $this->_helper->layout->setLayout('default-simple');  
    $this->view->form = $form = new Advgroup_Form_Music_Delete();
        
         if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
        $params = $this -> _getAllParams();
        $result = Engine_Api::_() -> getItemTable('advgroup_mapping') -> deleteItem($params);
        if($result != "true")
        {
            die($result);
        }
    

        
            $group = Engine_Api::_() -> getItem('group', $params['group_id']);
                    return $this -> _forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Album Music has been deleted')),
                        'layout' => 'default-simple',
                        'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                            'controller' => 'music',
                            'action' => 'list',
                            'subject' => $group -> getGuid(),
                            'type' => $params['type'],
                        ), 'group_extended', true),
                        'closeSmoothbox' => true,
                    ));
        
        
    }

    public function listAction()
    {
        $music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('music');
        $mp3music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('mp3music');
        
        if (!$music_enable)
        {
            if (!$mp3music_enable)
            {
                return $this -> _helper -> requireSubject -> forward();
            }
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
        $this -> view -> form = $form = new Advgroup_Form_Music_Search;

        if ($viewer -> getIdentity() == 0)
            $form -> removeElement('view');

        if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer))
        {
            $parent_group = $group -> getParentGroup();
            if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
            {
                return $this -> _helper -> requireAuth -> forward();
            }
            else
            if (!$group -> authorization() -> isAllowed($viewer, "view"))
            {
                return $this -> _helper -> requireAuth -> forward();
            }
        }
        else
        if (!$group -> authorization() -> isAllowed($viewer, 'view'))
        {
            return $this -> _helper -> requireAuth -> forward();
        }
            
        //Get search condition
        $params = array();
        $params['group_id'] = $group -> getIdentity();
        $params['user_id'] = null;
        $params['search'] = $this -> _getParam('search', '');
        $params['view'] = $this -> _getParam('view', 0);
        $params['order'] = $this -> _getParam('order', 'recent');
        if ($params['view'] == 1)
        {
            $params['user_id'] = $viewer -> getIdentity();
        }
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'view' => $params['view'],
            'order' => $params['order'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        if ($this -> _getParam('type') == 'mp3music')
        {
            $params['ItemTable'] = 'mp3music_album';
        }   
        else {
            $params['ItemTable'] = 'music_playlist';
        }
        
        $this -> view -> ItemTable = $params['ItemTable'];
        //Get Album paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_mapping') -> getAlbumsPaginator($params);
    
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 20));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));


    }

    

}
?>
