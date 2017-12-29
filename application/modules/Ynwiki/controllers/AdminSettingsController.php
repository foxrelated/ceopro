<?php
class Ynwiki_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynwiki_admin_main', array(), 'ynwiki_admin_main_settings');
  }
  public function indexAction()
  {
    $this->view->form = $form = new Ynwiki_Form_Admin_Global();
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        if($value < 0)
            $value = 0;
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
	 
     $form->addNotice('Your changes have been saved.');
	 $this->_redirect('/admin/ynwiki/settings');
    }
  }
}