<?php
class Ynmultilisting_AdminEditorsController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_editors');
    }
        
    public function indexAction() {
    	$this -> view -> form = $form = new Ynmultilisting_Form_Admin_Editors_Search();
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('editors', 'ynmultilisting');
		$params = $this ->_getAllParams();
		$form -> populate($params);
		$this->view->formValues = $params;
        $this->view->paginator = $table -> getEditorsPaginator($params);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
    
    public function createAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Editors_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
        
        if (!isset($values['toValues']) || empty($values['toValues'])) {
            $form->addError('Can not find the user.');
            return;
        } 
		
		$editorTable = Engine_Api::_()->getDbtable('editors', 'ynmultilisting');
        $db = $editorTable -> getAdapter();
        $db -> beginTransaction();
        $listingTypes = $values['listingtypes'];
        try {
            $table = Engine_Api::_()->getDbtable('editors', 'ynmultilisting');
			foreach($listingTypes as $listingTypeID)
			{
				$user = Engine_Api::_() -> getItem('user', $values['toValues']);
				if($user -> getIdentity())
				{
					$isEditor = $editorTable -> checkIsEditor($listingTypeID, $user);
					if(!$isEditor)
					{
						$editor = $table->createRow();
			            $editor -> user_id = $values['toValues'];
						$editor -> listingtype_id = $listingTypeID;
			            $editor -> save();
					}
				}
	            $db -> commit();
			}
        }
        catch( Exception $e ) {
            $db -> rollBack();
            throw $e;
        }       

        return $this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true, 
            'parentRefresh' => true, 
            'messages' => 'Add Editor sucessful.')
		);
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->editor_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $editor = Engine_Api::_()->getItem('ynmultilisting_editor', $id);
                $editor->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('This editor has been removed.')
            ));
        }
    }
    
    public function multideleteAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE)
        {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id)
            {
                $editor = Engine_Api::_()->getItem('ynmultilisting_editor', $id);
                if ($editor) {
                    $editor->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'editors', 'action'=>'index'), 'admin_default' , TRUE);
        }
    }

    public function suggestAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('user');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = Engine_Api::_()->getItemTable('user')->select()->where('search = ?', 1);
        
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $friend ){
            $data[] = array(
                'id' => $friend->getIdentity(),
                'label' => $friend->getTitle(), // We should recode this to use title instead of label
                'title' => $friend->getTitle(),
                'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                'url' => $friend->getHref(),
                'type' => 'user',
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
}
