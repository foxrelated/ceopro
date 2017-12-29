<?php
class Ynforum_Form_Admin_Icon_Edit extends Ynforum_Form_Admin_Icon_Create {
	public function init() {
		// Init form
		parent::init();
		$this -> setTitle('Edit Icon') -> setAttrib('id', 'form-edit-icon') -> setAttrib('class', '');
		$this -> addElement('Hidden', 'icon_id');
		$this -> removeElement('icon');
		// Init icon art
	     $this->addElement('File', 'icon', array(
	      'label' => 'Icon image'
	    ));
		// Override submit button
		$this -> removeElement('submit');
		$this -> addElement('Button', 'save', array('label' => 'Save Changes', 'type' => 'submit', 'decorators' => array( array('ViewScript', array('viewScript' => '_formButtonCancel.tpl', 'class' => 'form element'))), ));
	}
}
