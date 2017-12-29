<?php

class Ynadvsearch_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function indexAction ()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        	->getNavigation('ynadvsearch_admin_main', array(), 'ynadvsearch_admin_main_settings');
        $params = $this->_getAllParams();
        $this->view->saveSuccess = 0;
        $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
        $noneSearchModule = array(
                'announcement',
                'authorization',
                'core',
                'fields',
                'invite',
                'messages',
                'network',
                'payment',
                'ynadvsearch',
                'younet-core',
                'minify',
                'advcommentbox',
                'advmenusystem',
                'advcommentbox',
                'advmenusystem',
                'chat',
                'contactimporter',
                'mobi',
                'ynaffiliate',
                'webcamavatar',
                'wall',
                'profile-completeness',
                'social-connect',
                'questionanswer',
                'blogimporter',
                'yntheme',
                'storegroupbuyconnection',
                'ynprofilesearch',
                'ynprofilestyler',
                'yntour',
                'ynfbpp',
                'ynmediaimporter',
                'ynwelcomemessage',
                'ynrewardpoints',
                'advlayouteditor',
                'storage',
                'ynresponsive1',
                'activity',
        );

        $searchModulesTable = new Ynadvsearch_Model_DbTable_Modules();
        $select = $modulesTable->select();
        $select->where('name NOT IN (?)', $noneSearchModule);
        $results = $modulesTable->fetchAll($select);

        foreach ($results as $result) {
            $searchModulesTable->addModule($result);
        }

        // fix for removing already added modules
        $remove_select = $searchModulesTable->select();
        $remove_select->where('name IN (?)', $noneSearchModule);
        $remove_results = $searchModulesTable->fetchAll($remove_select);
        if (count($remove_results) > 0) {
            foreach ($remove_results as $remove_result) {
                $remove_result->delete();
            }
        }
        if (isset($params['submit'])) {
            $mods = $params['moduleynsearch'];
            foreach ($mods as $value => $key) {
                $searchModulesTable->updateEnabledModule($value, $key);
            }
            $this->view->saveSuccess = 1;
        }

        $modules = $searchModulesTable->getModules();
        $this->view->modules = $modules;
    }

    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynadvsearch_admin_main', array(), 'ynadvsearch_admin_main_global');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->form = $form = new Ynadvsearch_Form_Admin_Settings_Global();
        
        if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $settings->setSetting('ynadvsearch_num_searchitem', $values['num_autosuggest']);

            $form->addNotice('Your changes have been saved.'); 
        }
    }
}
