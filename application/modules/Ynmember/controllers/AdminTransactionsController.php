<?php
class Ynmember_AdminTransactionsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmember_admin_main', array(), 'ynmember_admin_main_transactions');
        
        $table = Engine_Api::_()->getDbTable('transactions', 'ynmember');
		$select = $table -> select();
        $tableName = $table -> info('name');
        
        $methods = array();
        
        $this->view->form = $form = new Ynmember_Form_Admin_Transactions_Search();
        
        if (Engine_Api::_()->hasModuleBootstrap("yncredit")) {
            $form->gateway_id->addMultiOption(-3, 'Pay with Credit');
            $methods['-3'] = 'Pay with Credit';
        }
        
        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable->select()->where('enabled = ?', 1);
        $gateways = $gatewayTable->fetchAll($gatewaySelect);
        foreach ($gateways as $gateway) {
            $form->gateway_id->addMultiOption($gateway->gateway_id, 'Pay with '.$gateway->title);
            $methods[''.$gateway->gateway_id] = 'Pay with '.$gateway->title;        
        }
        
        $this->view->methods = $methods;
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $this->view->formValues = $values;
        
        if ($values['gateway_id'] != 'all') {
            $select->where('gateway_id = ?', $values['gateway_id']);
        }
        
        $transactions = $table->fetchAll($select);
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($transactions);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
}