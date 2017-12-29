<?php
class Advgroup_Form_Allinvite extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Invite Members') -> setDescription('Choose the people you want to invite to this group.') -> setAttrib('id', 'group_form_users_invite') -> setAttrib('action', 'javascript:;');

		$this -> addElement('Text', 'users_search', array(
			'description' => '(filter the search box and press Enter to search)',
			'onkeypress' => 'updateUserList(event, "users")',
		));
		$this -> users_search -> getDecorator("Description") -> setOption("placement", "append");

		$this -> addElement('Checkbox', 'all', array(
			'id' => 'userselectall',
			'label' => 'Choose All',
			'ignore' => true
		));

		$this -> addElement('MultiCheckbox', 'users', array('label' => 'Members', ));

		$this -> addElement('Button', 'button_btn', array(
			'label' => 'Send Invites',
			'onClick' => 'submitForm("users")',
			'ignore' => true,
			'decorators' => array('ViewHelper', ),
		));
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
			'onclick' => $onclick,
			'decorators' => array('ViewHelper', ),
		));

		$this -> addDisplayGroup(array(
			'button_btn',
			'cancel'
		), 'users_buttons');
	}

}
