<?php
class Ynmultilisting_AdminCategoryFieldsController extends Fields_Controller_AdminAbstract
{
	protected $_fieldType = 'ynmultilisting_listing';

	protected $_requireProfileType = true;

	public function indexAction()
	{
		// Make navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmultilisting_admin_main', array(), null);
		$this -> view -> option_id = $option_id =  $this->_getParam('option_id');
		$tableCategory = Engine_Api::_()->getItemTable('ynmultilisting_category');
		$category = $tableCategory -> getCategoryByOptionId($option_id);
		$this -> view -> category = $category;
		parent::indexAction();
	}

	public function headingCreateAction()
	{
		 parent::headingCreateAction();
		 $form = $this->view->form;
		 if($form){
		 	$form -> removeElement('show');
			 $display = $form->getElement('display');
			 $display->setLabel('Show on listing page?');
			 $display->setOptions(array('multiOptions' => array(
				 1 => 'Show on listing page',
				 0 => 'Hide on listing page'
			 )));
		 }
	}

	public function headingEditAction()
	{
	 parent::headingEditAction();
	 $form = $this->view->form;
	 if($form){
	 	$form -> removeElement('show');
		 $display = $form->getElement('display');
		 $display->setLabel('Show on listing page?');
		 $display->setOptions(array('multiOptions' => array(
			 1 => 'Show on listing page',
			 0 => 'Hide on listing page'
		 )));
	 }
	}
	public function fieldCreateAction(){
		parent::fieldCreateAction();
		// remove stuff only relavent to profile questions
		$form = $this->view->form;

		if($form){
			  $form -> removeElement('show');
			  $form -> removeElement('search');
			  
			  $form -> addElement('Hidden', 'search', array(
				'order' => 999,
				'value' => 0,
			  ));
			  
			  $display = $form->getElement('display');
			  $display->setLabel('Show on listing page?');
			  $display->setOptions(array('multiOptions' => array(
				  1 => 'Show on listing page',
				  0 => 'Hide on listing page'
			  )));
		}
	}

	public function fieldEditAction()
	{
		parent::fieldEditAction();
		// remove stuff only relavent to profile questions
		$form = $this->view->form;

		if($form)
		{
			  $form -> removeElement('show');
			  $form -> removeElement('search');
			  
			  $form -> addElement('Hidden', 'search', array(
				'order' => 999,
				'value' => 0,
			  ));
			  
			  $display = $form->getElement('display');
			  $display->setLabel('Show on bussiness page?');
			  $display->setOptions(array('multiOptions' => array(
				  1 => 'Show on listing page',
				  0 => 'Hide on listing page'
			  )));
		}
	}
}
?>
