<?php
class Ynmultilisting_AdminModuleController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_modules');
    }
        
    public function indexAction() {
        $page = $this->_getParam('page',1);
        $modules = Engine_Api::_()->getDbTable('modules', 'ynmultilisting')->getAvailableModules();
        $this->view->paginator = Zend_Paginator::factory($modules);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
    
    public function createAction() {
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Module_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
        if (!Engine_Api::_()->hasItemType($values['table_item'])) {
            $form->addError($this->view->translate('Can not find the table of item "%s".', $values['table_item']));
            return;    
        }
        
        $itemTable = Engine_Api::_()->getItemTable($values['table_item']);
        $tableColumns = $itemTable->info(Zend_Db_Table_Abstract::COLS);
        $columns = array('owner_id_column', 'title_column', 'short_description_column', 'description_column', 'photo_id_column', 'about_us_column', 'price_column', 'location_column', 'long_column', 'lat_column');
        foreach ($columns as $column) {
            if (!empty($values[$column]) && !in_array($values[$column], $tableColumns)) {
                $form->addError($this->view->translate('Can not find the column name "%s" in table of item "%s".', $values[$column], $values['table_item']));
                return;
            }    
        }
        
        $table = Engine_Api::_()->getDbtable('modules', 'ynmultilisting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $module = $table->createRow();
            $module->setFromArray($values);
            $module->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'module', 'action'=>'index'), 'admin_default', TRUE);
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
    }
    
    public function editAction() {
        $id = $this->_getParam('id');
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Module_Edit();
        $module = Engine_Api::_()->getItem('ynmultilisting_module', $id);
        $form->populate($module->toArray());
            
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if(!$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $db = Engine_Api::_()->getDbtable('modules', 'ynmultilisting')->getAdapter();
        $db->beginTransaction();
        try {
            $module->setFromArray($form->getValues());
            $module->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'module', 'action'=>'index'), 'admin_default', TRUE);
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
    }
    
    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->module_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $module = Engine_Api::_()->getItem('ynmultilisting_module', $id);
                $module->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This Module has been deleted.')
            ));
        }
    }
    
    public function multideleteAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id) {
                $module = Engine_Api::_()->getItem('ynmultilisting_module', $id);
                if ($module) {
                    $module->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'module', 'action'=>'index'), 'admin_default', TRUE);
        }
    }

    public function sortAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $table = Engine_Api::_()->getDbTable('faqs', 'ynmultilisting');
        $faqs = $table->fetchAll();
        $order = explode(',', $this->getRequest()->getParam('order'));
        foreach( $order as $i => $item ) {
            $faq_id = substr($item, strrpos($item, '_') + 1);
            foreach( $faqs as $faq ) {
                if( $faq->getIdentity() == $faq_id ) {
                    $faq->order = $i;
                    $faq->save();
                }
            }
        }
    }
}