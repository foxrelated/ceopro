<?php 
class Ynmember_AdminReviewFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'ynmember_review';

  protected $_requireProfileType = true;

  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynmember_admin_main', array(), 'ynmember_admin_main_review_fields');
	$tableRatingType = Engine_Api::_() -> getItemTable('ynmember_ratingtype');
	$this -> view -> listRatingType = $tableRatingType -> getAllRatingTypes();  
	 
    parent::indexAction();
  }
  
  public function headingCreateAction()
  {
  	 parent::headingCreateAction();
	  $form = $this->view->form;
	 if($form){
	 	$form -> removeElement('show');
	 }
  }
  
  public function headingEditAction()
  {
  	parent::headingEditAction();
	 $form = $this->view->form;
	 if($form){
	 	$form -> removeElement('show');
	 }
  }	
  	
  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;
    if($form){
      $form -> removeElement('show');
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;
    if($form){
      $form -> removeElement('show');
    }
  }
}
?>
