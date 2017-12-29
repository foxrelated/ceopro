<?php
class Ynmultilisting_AdminFaqsController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_faqs');
    }
        
    public function indexAction() {
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('faqs', 'ynmultilisting');
        $faqs = $table->fetchAll($table->select()->order('order ASC'));
        $this->view->paginator = Zend_Paginator::factory($faqs);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
    
    public function createAction() {
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Faqs_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
        $table = Engine_Api::_()->getDbtable('faqs', 'ynmultilisting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $faq = $table->createRow();
            $faq->setFromArray($values);
            $faq->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'faqs', 'action'=>'index'), 'admin_default', TRUE);
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
    }
    
    public function editAction() {
        $id = $this->_getParam('id');
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Faqs_Edit();
        $faq = Engine_Api::_()->getItem('ynmultilisting_faq', $id);
        $form->populate($faq->toArray());
            
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if(!$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $db = Engine_Api::_()->getDbtable('faqs', 'ynmultilisting')->getAdapter();
        $db->beginTransaction();
        try {
            $faq->setFromArray($form->getValues());
            $faq->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'faqs', 'action'=>'index'), 'admin_default', TRUE);
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
        $this->view->faq_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $faq = Engine_Api::_()->getItem('ynmultilisting_faq', $id);
                $faq->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This FAQ has been deleted.')
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
                $faq = Engine_Api::_()->getItem('ynmultilisting_faq', $id);
                if ($faq) {
                    $faq->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'faqs', 'action'=>'index'), 'admin_default', TRUE);
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