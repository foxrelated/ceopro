<?php

class Ynmember_Form_GetNotification extends Engine_Form
{
	protected $_active;

	public function setActive($active)
	{
		$this->_active = $active;
	}

	public function getItem()
	{
		return $this->_active;
	}
	
	public function init()
	{
		$title = ($this->_active) ? 'Getting Notification' : 'Stop Getting Notification';
		$des = ($this->_active) ? 'Do you want to get notification from this member?' : 'Do you want to stop getting notification from this member?';
		$this
		->setTitle($title)
		->setDescription($des)
		->setMethod('POST')
		->setAction($_SERVER['REQUEST_URI'])
		->setAttrib('class', 'global_form_popup')
		;

		$this->addElement('Hash', 'token');

		// Buttons
		$this->addElement('Button', 'submit', array(
	      'label' => ($this->_active) ? 'Get' : 'Stop',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array('ViewHelper')
		));

		$this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => ' or ',
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close();',
	      'decorators' => array(
	        'ViewHelper'
	      )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');

	}
}