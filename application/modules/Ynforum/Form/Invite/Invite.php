<?php
class Ynforum_Form_Invite_Invite extends Engine_Form
{
	private $_users;
	public function __construct($options = null)
	{
		if (is_array($options) && array_key_exists('users', $options))
		{
			$this -> _users = $options['users'];
			unset($options['users']);
		}
		parent::__construct($options);
	}
  public function init()
  {
    $this
      ->setTitle('Invite Forum Participators')
      ->setDescription('Choose the people you want to invite to this event.')
      ->setAttrib('id', 'event_form_invite')
      ;

    $this->addElement('Checkbox', 'all', array(
      'id' => 'selectall',
      'label' => 'Choose All Guests',
      'ignore' => true
    ));

	$this -> addElement('Dummy', 'dummy_users', array(
		'users' => $this->_users,
		'label' => 'Members',
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_forum_users.tpl',
				'class' => 'form element',
			)
		))));
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Invitation & Finish',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Button', 'cancel', array(
      'label' => 'Skip & Finish',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}