<?php
class Advgroup_Form_Transfer extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Group Owner Transfer') -> setDescription('Please choose a member you want to transfer the owner role to:') -> setAttrib('class', 'global_form_popup') -> setMethod('post') -> setAttrib('id', 'advgroup_transfer');

		$this -> addElement('Text', 'to', array('autocomplete' => 'off'));
		Engine_Form::addDefaultDecorators($this -> to);

		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'label' => 'Member',
			'required' => true,
			'allowEmpty' => false,
			'style' => 'margin-top:-5px',
			'order' => 1,
			'validators' => array('NotEmpty'),
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);

		$this -> addElement('Button', 'submit', array(
			'label' => 'Submit',
			'type' => 'submit',
			'order' => 3,
			'ignore' => true,
			'decorators' => array(
		        'ViewHelper',
		      ),
		));
		$onclick = 'parent.Smoothbox.close();';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$onclick = '';
		}
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'order' => 4,
			'link' => true,
			'prependText' => ' or ',
			'onclick' => $onclick,
			'decorators' => array(
		        'ViewHelper',
		      ),
		));

		$this -> addDisplayGroup(array(
			'submit',
			'cancel'
		), 'buttons');
	}

}
