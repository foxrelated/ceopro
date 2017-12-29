<?php
class Ynblog_AdminAddthisController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynblog_admin_main', array(), 'ynblog_admin_main_addthis');
    $this->view->form  = $form = new Ynblog_Form_Admin_Addthis();
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
      foreach ($values as $key => $value){
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }   
  }
}