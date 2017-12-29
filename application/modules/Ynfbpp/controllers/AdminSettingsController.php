<?php

class Ynfbpp_AdminSettingsController extends Core_Controller_Action_Admin
{

    public function indexAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core')
            -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_settings');

        $params = array();

        // repopulate value in case of error
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
        }
        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Global(array('params' => $params));

        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        $values = $form -> getValues();

        // save color
        $colorSettings = array(
            'popup_backgroundcolor',
            'popup_footercolor',
            'popup_bordercolor',
        );
        foreach ($colorSettings as $setting) {
            $params['ynfbpp_' . $setting] = $values['ynfbpp_' . $setting];
        }
        
        $params['guide_picture'] = '<img src="application/modules/Ynfbpp/externals/images/guide_picture.jpg" />';

        if($form->isValid($params)) {
            $values = $form->getValues();
            foreach ($colorSettings as $setting) {
                $values['ynfbpp_'.$setting] = $params[$setting];
            }
            unset($values['looks-n-feels']);
            unset($values['preview']);
            unset($values['preview_popup']);
            unset($values['guide_picture']);

            foreach ($values as $key => $value) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }

            $form->addNotice('Your changes have been saved.');
        }
    }

    public function userAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core')
            -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_user');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_User();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

    public function groupAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_group');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Group();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

    public function eventAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_event');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Event();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

    public function businessAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_business');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Business();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

    public function companyAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_company');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Company();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

    public function storeAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_store');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Store();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

    public function listingAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_listing');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Listing();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

    public function multilistingAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_multilisting');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Multilisting();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }

}
