<?php class Advgroup_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'group';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_admin_main', array(), 'advgroup_admin_main_fields');
    parent::indexAction();
  }

  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $display = $form->getElement('display');
      $display->setLabel('Show on group page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on group page',
          0 => 'Hide on group page'
        )));
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
//      $form->setTitle('Edit Group Question');
      $display = $form->getElement('display');
      $display->setLabel('Show on group page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on group page',
          0 => 'Hide on group page'
        )));
    }
  }
}
?>
