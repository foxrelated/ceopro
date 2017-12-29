<?php
class Ynmember_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmember_admin_main', array(), 'ynmember_admin_settings_global');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->form = $form = new Ynmember_Form_Admin_Settings_Global();
    
        if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
			$values = $form->getValues();
            $settings->setSetting('ynmember_allow_add_workplace', $values['ynmember_allow_add_workplace']);
            $settings->setSetting('ynmember_allow_search_location', $values['ynmember_allow_search_location']);
			$settings->setSetting('ynmember_allow_update_relationship', $values['ynmember_allow_update_relationship']);
            $form->addNotice('Your changes have been saved.'); 
        }
    }
    
    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmember_admin_main', array(), 'ynmember_admin_settings_level');

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
        $this->view->form = $form = new Ynmember_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->level_id->setValue($id);
 
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        
		$form->populate($permissionsTable->getAllowed('ynmember_user', $id, array_keys($form->getValues())));
        $form->populate($permissionsTable->getAllowed('ynmember_review', $id, array_keys($form->getValues())));
        if ($level->type != 'public') {
            $numberFieldArr = Array('feature_fee');
            foreach ($numberFieldArr as $numberField) {
                if ($permissionsTable->getAllowed('ynmember_user', $id, $numberField) == null) {
                    $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $id)
                    ->where('type = ?', 'ynmember_user')
                    ->where('name = ?', $numberField));
                    if ($row) {
                        $form->$numberField->setValue($row->value);
                    }
                }
            } 
            $credit = array();
            
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
                $select = $typeTbl->select()->where('module = ?', 'ynmember')->where('action_type = ?', 'ynmember_rate')->limit(1);
                $type = $typeTbl -> fetchRow($select);
    			
    			$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'feature_member'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
    			
    			if(!$type_spend) 
                {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'feature_member';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to feature %s member';
                    $type_spend->credit_default = 0;
                    $type_spend->link_params = '';
                    $type_spend->save();
                }
    			
                if(!$type) {
                    $type = $typeTbl->createRow();
                    $type->module = 'ynmember';
                    $type->action_type = 'ynmember_rate';
                    $type->group = 'earn';
                    $type->content = 'Rating %s member';
                    $type->credit_default = 5;
                    $type->save();
                }
                $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
    			
    			$select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type_spend -> type_id)
                    ->limit(1);
                $spend_credit = $creditTbl->fetchRow($select);
    			if(!$spend_credit)
    			{
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
    			
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type->type_id)
                    ->limit(1);
                $credit = $creditTbl->fetchRow($select);
                if(!$credit) 
                {
                    $credit = $creditTbl->createRow();
                }
                else 
                {
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
				$permissionsTable->setAllowed('ynmember_review', $id, 'comment',$permissionValues['comment']);
                $permissionsTable->setAllowed('ynmember_review', $id, 'can_edit_own_review', $permissionValues['can_edit_own_review']);
				$permissionsTable->setAllowed('ynmember_review', $id, 'can_share_reviews',$permissionValues['can_share_reviews']);
				$permissionsTable->setAllowed('ynmember_review', $id, 'can_report_reviews', $permissionValues['can_report_reviews']);
				$permissionsTable->setAllowed('ynmember_review', $id, 'can_delete_own_reviews', $permissionValues['can_delete_own_reviews']);
			    unset($permissionValues['comment']);
				unset($permissionValues['can_edit_own_review']);
				unset($permissionValues['can_share_reviews']);
				unset($permissionValues['can_report_reviews']);
				unset($permissionValues['can_delete_own_reviews']);
                $permissionsTable->setAllowed('ynmember_user', $id, $permissionValues);
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
            	$permissionsTable->setAllowed('ynmember_review', $id, $values['comment']);
				$permissionsTable->setAllowed('ynmember_review', $id, $values['can_edit_own_review']);
				$permissionsTable->setAllowed('ynmember_review', $id, $values['can_share_reviews']);
				$permissionsTable->setAllowed('ynmember_review', $id, $values['can_report_reviews']);
				$permissionsTable->setAllowed('ynmember_review', $id, $values['can_delete_own_reviews']);
				unset($values['comment']);
				unset($values['can_edit_own_review']);
				unset($values['can_share_reviews']);
				unset($values['can_report_reviews']);
				unset($values['can_delete_own_reviews']);
                $permissionsTable->setAllowed('ynmember_user', $id, $values);
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