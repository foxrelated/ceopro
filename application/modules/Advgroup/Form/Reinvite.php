<?php
class Advgroup_Form_Reinvite extends Engine_Form
{
	protected $_group_id;
	public function setGroup($group_id)
	{
		$this -> _group_id = $group_id;
	}

	protected $_user_id;
	public function setUser($user_id)
	{
		$this -> _user_id = $user_id;
	}

	public function init()
	{
		$this -> setTitle('Invite Member') -> setDescription('Are you sure you want to resend a invitation to this member?');

		//     $this->addElement('Button', 'submit', array(
		//       'type' => 'submit',
		//       'ignore' => true,
		//       'decorators' => array('ViewHelper'),
		//       'label' => 'Resend Invite',
		//     ));
		$this -> addElement('Cancel', 'resend', array(

			'label' => 'Resend Invite',
			'link' => true,
			'class' => 'group_viewmore',
			'href' => '',
			'onclick' => "parent.resendInvite($this->_group_id, $this->_user_id );",
			//'onclick' => '',
			'decorators' => array('ViewHelper'),
		));

		$this -> addElement('Cancel', 'cancel', array(
			'prependText' => ' or ',
			'label' => 'cancel',
			'link' => true,
			'href' => '',
			'onclick' => 'parent.Smoothbox.close();',
			'decorators' => array('ViewHelper'),
		));

		$this -> addDisplayGroup(array(
			'resend',
			'cancel'
		), 'buttons');
	}

}
