<?php
class Advgroup_AdminGlobalController extends Core_Controller_Action_Admin
{
   public function indexAction()
  {
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_admin_main', array(), 'advgroup_admin_main_global');
       $this->view->form = $form = new Advgroup_Form_Admin_Global();
     if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
       $form->saveValues();
     }
   }
}
?>
