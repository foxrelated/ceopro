<?php
class Advgroup_Form_Member_Reject extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Reject Group Invitation') 
		-> setDescription('Would you like to reject the invitation to this group?') 
		-> setAttrib('class', 'global_form_popup') 
		-> setMethod('POST') 
		-> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));

		$this -> addElement('Button', 'submit', array(
			'label' => 'Reject Invitation',
			'ignore' => true,
			'decorators' => array('ViewHelper'),
			'type' => 'submit'
		));
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => 'parent.Smoothbox.close();',
			'decorators' => array('ViewHelper')
		));
		$this -> addDisplayGroup(array(
			'submit',
			'cancel'
		), 'buttons');
	}

}
