<?php

class Advalbum_Form_Photo_SendToFriends extends Engine_Form
{
	public $invalid_emails = array();

	public $emails_sent = 0;

	public function init() {
		// Init settings object
		$translate = Zend_Registry::get('Zend_Translate');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Init form
		$this -> setTitle('Send To Friends')
			->setAttrib('class', 'global_form_popup')
//			-> setDescription('')
			-> setLegend('');

		if (!$viewer->getIdentity()) {
			$this->addElement('Text', 'send_name', array(
				'label' => 'Your name',
				'allowEmpty' => false,
				'required' => true,
				'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 64)),
				),
				'filters' => array(
					'StripTags',
					new Engine_Filter_Censor(),
				),
			));
		}


		// Init recipients
		$this -> addElement('Textarea', 'recipients', array(
			'label' => 'Recipients',
			'description' => 'Separate multiple email addresses (up to 5) with commas.',
			'style' => 'width: 100%',
			'required' => true,
			'allowEmpty' => false,
			'validators' => array(new Engine_Validate_Callback( array(
					$this,
					'validateEmails'
				)), ),
		));
		$this -> recipients -> getValidator('Engine_Validate_Callback') -> setMessage('Please enter only valid email addresses.');
		$this -> recipients -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));

		// Init custom message
		$this -> addElement('Textarea', 'message', array(
			'label' => 'Message',
			'style' => 'width: 100%',
			'required' => false,
			'allowEmpty' => true,
			'filters' => array(new Engine_Filter_Censor(), )
		));
		$this -> message -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));

		$this -> addElement('Button', 'submit', array(
			'label' => 'Send Emails',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$buttons[] = 'submit';

		$onclick = 'parent.Smoothbox.close();';
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper')
		));
		$buttons[] = 'cancel';

		$this -> addDisplayGroup($buttons, 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}

	public function validateEmails($value) {
		// Not string?
		if (!is_string($value) || empty($value))
		{
			return false;
		}

		// Validate emails
		$validate = new Zend_Validate_EmailAddress();

		$emails = array_unique(array_filter(array_map('trim', preg_split("/[\s,]+/", $value))));

		if (empty($emails))
		{
			return false;
		}

		$emailsCount = sizeof(explode(",", $emails));
		if ($emailsCount > 5) {
			$error_message = "You are allowed to send email to up to 5 persons at a time.";
			return false;
		}

		foreach ($emails as $email)
		{
			if (!$validate -> isValid($email))
			{
				return false;
			}
		}

		return true;
	}

}
