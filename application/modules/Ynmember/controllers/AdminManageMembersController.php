<?php

class Ynmember_AdminManageMembersController extends Core_Controller_Action_Admin
{
    public function init()
    {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynmember_admin_main', array(), 'ynmember_admin_manage_member');
    }

    public function indexAction()
    {
        $this->view->formFilter = $formFilter = new Ynmember_Form_Admin_Manage_Filter();
        $page = $this->_getParam('page', 1);

        $userTbl = Engine_Api::_()->getItemTable('user');
        $userTblName = $userTbl->info('name');
        $featureTbl = Engine_Api::_()->getItemTable('ynmember_feature');
        $featureTblName = $featureTbl->info('name');
        $select = $userTbl->select()->setIntegrityCheck(false)
            ->from($userTblName)
            ->joinLeft($featureTblName, "{$userTblName}.`user_id` = {$featureTblName}.`user_id`", array("{$featureTblName}.active"));

        // Process form
        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array(
            'order' => 'user_id',
            'order_direction' => 'DESC',
        ), $values);

        $this->view->assign($values);

        // Set up select info
        $select->order((!empty($values['order']) ? $values['order'] : 'user_id') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

        if (!empty($values['displayname'])) {
            $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
        }
        if (!empty($values['username'])) {
            $select->where('username LIKE ?', '%' . $values['username'] . '%');
        }
        if (!empty($values['email'])) {
            $select->where('email LIKE ?', '%' . $values['email'] . '%');
        }
        if (!empty($values['level_id'])) {
            $select->where('level_id = ?', $values['level_id']);
        }
        if (isset($values['enabled']) && $values['enabled'] != -1) {
            $select->where('enabled = ?', $values['enabled']);
        }
        if (!empty($values['user_id'])) {
            $select->where('user_id = ?', (int)$values['user_id']);
        }
        if (isset($values['featured']) && $values['featured'] != -1) {
            if ($values['featured'] == '1') {
                $select->where("{$featureTblName}.active = 1");
            } else {
                $select->where("{$featureTblName}.active IS NULL || {$featureTblName}.active = 0");
            }
        }

        // Filter out junk
        $valuesCopy = array_filter($values);
        // Reset enabled bit
        if (isset($values['enabled']) && $values['enabled'] == 0) {
            $valuesCopy['enabled'] = 0;
        }
        // Make paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
        $this->view->formValues = $valuesCopy;
        $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
        $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
        $this->view->openUser = (bool)($this->_getParam('open') && $paginator->getTotalItemCount() == 1);
    }

    public function featureAction()
    {
        $featuresTbl = Engine_Api::_()->getItemTable("ynmember_feature");
        $userId = $this->_getParam('user_id');
        $value = $this->_getParam('value');
        if ($userId) {
            $featureRow = $featuresTbl->getFeatureRowByUserId($userId);
            if (is_null($featureRow)) {
                $featureRow = $featuresTbl->createRow();
                $featureRow->setFromArray(array(
                    'user_id' => $userId,
                    'creation_date' => new Zend_Db_Expr("NOW()"),
                ));
            }
            $featureRow->active = $value;
            $featureRow->modified_date = new Zend_Db_Expr("NOW()");
            $featureRow->expiration_date = NULL;
            $featureRow->save();
            echo Zend_Json::encode(array(
                'error_code' => 0,
                'error_message' => '',
                'message' => ($value)
                    ? Zend_Registry::get("Zend_Translate")->_("Set featured successfully!")
                    : Zend_Registry::get("Zend_Translate")->_("Unset featured successfully!")
            ));
            exit;
        } else {
            echo Zend_Json::encode(array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Can not set featured this user")
            ));
            exit;
        }
    }

    public function dayAction()
    {
        $userId = $this->_getParam('user_id');
        if ($userId) {
            $user = Engine_Api::_()->user()->getUser($userId);
            $userTbl = Engine_Api::_()->getItemTable('user');
            if ($user) {
                $user->member_of_day = 1;
                $user->save();
                $userTbl->update(array(
                    'member_of_day' => '0'
                ), array(
                    'user_id <> ? ' => $userId
                ));

                echo Zend_Json::encode(array(
                    'error_code' => 0,
                    'error_message' => '',
                    'message' => Zend_Registry::get("Zend_Translate")->_("Set member of day successfully!")
                ));
                exit;
            }
        } else {
            echo Zend_Json::encode(array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get("Zend_Translate")->_("Can not set member of day for this user")
            ));
            exit;
        }

    }
}
