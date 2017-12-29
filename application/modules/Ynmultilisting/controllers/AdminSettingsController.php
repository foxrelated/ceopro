<?php
class Ynmultilisting_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_settings_global');
        $settings = Engine_Api::_()->getApi('settings', 'core');
         
        $params = array();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
        }
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Settings_Global(array('params' => $params));
         
        if (!$this->getRequest()->isPost()){
            return;
        }
        $p_valid = $this->getRequest()->getPost();
        $colorSettings = array(
            'menu_backgroundcolor', 
            'menu_hovercolor', 
            'menu_textcolor',
            'menu_backgroundbar'
        );
        $formValues  = $form->getValues();
        foreach ($colorSettings as $setting) {
            $p_valid['ynmultilisting_'.$setting] = $formValues['ynmultilisting_'.$setting];
        }
        if($form->isValid($p_valid)) {
            $p_post = $this->getRequest()->getPost();
            $values = $form->getValues();
            foreach ($colorSettings as $setting) {
                $values['ynmultilisting_'.$setting] = $p_post[$setting];
            }
			unset($values['image']);
            foreach ($values as $key => $value) {
                $settings->setSetting($key, $value);
            }
            $form->addNotice('Your changes have been saved.'); 
        }
    }
    
    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_settings_level');

        if (null !== ($id = $this->_getParam('level_id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } 
        else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if(!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;
    
        // Make form
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->level_id->setValue($id);
 
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        //print_r($permissionsTable->getAllowed('ynmultilisting_listing', $id, 'auth_view')); exit;
        $form->populate($permissionsTable->getAllowed('ynmultilisting_listing', $id, array_keys($form->getValues())));
        
        if ($level->type != 'public') {
            $numberFieldArr = Array('max_listing');
            foreach ($numberFieldArr as $numberField) {
                if ($permissionsTable->getAllowed('ynmultilisting_listing', $id, $numberField) == null) {
                    $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $id)
                    ->where('type = ?', 'ynmultilisting_listing')
                    ->where('name = ?', $numberField));
                    if ($row) {
                        $form->$numberField->setValue($row->value);
                    }
                }
            } 
            $credit = array();
            
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
                $select = $typeTbl->select()->where('module = ?', 'ynmultilisting')->where('action_type = ?', 'ynmultilisting_new')->limit(1);
                $type = $typeTbl -> fetchRow($select);
                $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
                $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'publish_multilisting'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
                
                if(!$type_spend) {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'publish_multilisting';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to publish %s listings';
                    $type_spend->credit_default = 0;
                    $type_spend->link_params = '';
                    $type_spend->save();
                }
                
				 $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type_spend -> type_id)
                    ->limit(1);
                $spend_credit = $creditTbl->fetchRow($select);
                if(!$spend_credit) {
                    $spend_credit = $creditTbl->createRow();
                    $spend_credit -> level_id = $id;
                    $spend_credit -> type_id = $type_spend -> type_id;
                    $spend_credit -> first_amount = 0;
                    $spend_credit -> first_credit = 0;
                    $spend_credit -> credit = 0;
                    $spend_credit -> max_credit = 0;
                    $spend_credit -> period = 1;
                    $spend_credit->save();
                }
				
				$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'feature_multilisting'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
                
                if(!$type_spend) {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'feature_multilisting';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to feature %s listing';
                    $type_spend->credit_default = 0;
                    $type_spend->link_params = '';
                    $type_spend->save();
                }
				
				 $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type_spend -> type_id)
                    ->limit(1);
                $spend_credit = $creditTbl->fetchRow($select);
                if(!$spend_credit) {
                    $spend_credit = $creditTbl->createRow();
                    $spend_credit -> level_id = $id;
                    $spend_credit -> type_id = $type_spend -> type_id;
                    $spend_credit -> first_amount = 0;
                    $spend_credit -> first_credit = 0;
                    $spend_credit -> credit = 0;
                    $spend_credit -> max_credit = 0;
                    $spend_credit -> period = 1;
                    $spend_credit->save();
                }
				
                if(!$type) {
                    $type = $typeTbl->createRow();
                    $type->module = 'ynmultilisting';
                    $type->action_type = 'ynmultilisting_new';
                    $type->group = 'earn';
                    $type->content = 'Creating %s listings';
                    $type->credit_default = 5;
                    $type->link_params = '{"route":"ynmultilisting_general","action":"create"}';
                    $type->save();
                }
				
                
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type->type_id)
                    ->limit(1);
                $credit = $creditTbl->fetchRow($select);
                if(!$credit) {
                    $credit = $creditTbl->createRow();
                }
                else {
                    $form->first_amount->setValue($credit->first_amount);
                    $form->first_credit->setValue($credit->first_credit);
                    $form->credit->setValue($credit->credit);
                    $form->max_credit->setValue($credit->max_credit);
                    $form->period->setValue($credit->period);
                } 
            }
        }

        // Check post
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        // Check validitiy
        if(!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        
        $values = $form->getValues();
        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        // Process
        if ($level->type != 'public') {
            
            $checkArr = array('auth_view', 'auth_comment', 'auth_share', 'auth_photo', 'auth_video', 'auth_discussion');
            foreach ($checkArr as $check) {
                if(empty($values[$check])) {
                    unset($values[$check]);
                    $form->$check->setValue($permissionsTable->getAllowed('ynmultilisting_listing', $id, $check));
                }
            }
            try {
                //credit
                if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                    $creditValues = array_slice($values, 0, 5);
                    $permissionValues = array_slice($values, 5);
                    $credit->level_id = $id;
                    $credit->type_id = $type->type_id;
                    $credit->setFromArray($creditValues);
                    $credit->save();
                }
                else {
                    $permissionValues = $values;
                }
                $permissionsTable->setAllowed('ynmultilisting_listing', $id, $permissionValues);
                 // Commit
                $db->commit();
            }
        
            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        else {
            try {
                $permissionsTable->setAllowed('ynmultilisting_listing', $id, $values);
                $db->commit();
            }
            
            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        
        $form->addNotice('Your changes have been saved.'); 
    }
}