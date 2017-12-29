<?php

class Advgroup_Form_Inviter extends Engine_Form
{
	public $invalid_emails = array();

	public $already_members = array();

	public $emails_sent = 0;

	public function init()
	{
		// Init settings object
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$translate = Zend_Registry::get('Zend_Translate');

		// Init form
		$this -> setTitle('Invite Your Friends') -> setDescription('Invite your friends to join! Enter email addresses separated by commas in the recipients box below. If your friends decide to sign up, they will be automatically added to your group.') -> setLegend('');

		// Init recipients
		$this -> addElement('Textarea', 'recipients', array(
			'label' => 'Recipients',
			'description' => '(Comma-separated list, or one-email-per-line)',
			'style' => 'width:450px',
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
		if ($settings -> getSetting('invite.allowCustomMessage', 1) > 0)
		{
			$this -> addElement('Textarea', 'message', array(
				'label' => 'Custom Message',
				'style' => 'width:450px',
				'required' => false,
				'allowEmpty' => true,
				'description' => '(Use %invite_url% to add a link to our sign up page)',
				'value' => $settings -> getSetting('invite.message', '%invite_url%'),
				'filters' => array(new Engine_Filter_Censor(), )
			));
			$this -> message -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));
		}

		// Init captcha
		if ($settings -> core_spam_invite)
		{
			$this -> addElement('captcha', 'captcha', array(
				'description' => '_CAPTCHA_DESCRIPTION',
				'captcha' => 'image',
				'required' => true,
				'captchaOptions' => array(
					'wordLen' => 6,
					'fontSize' => '30',
					'timeout' => 300,
					'imgDir' => APPLICATION_PATH . '/public/temporary/',
					'imgUrl' => $this -> getView() -> baseUrl() . '/public/temporary',
					'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
				),
			));
		}

		// Init submit
		/*$this->addElement('button', 'submit', array(
		 'type' => 'submit',
		 'label' => 'Send Invites',
		 ));*/
		$this -> addElement('Button', 'submit', array(
			'label' => 'Send Invites',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$buttons[] = 'submit';
		$onclick = 'parent.Smoothbox.close();';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$onclick = '';
		}
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

	public function validateEmails($value)
	{
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
