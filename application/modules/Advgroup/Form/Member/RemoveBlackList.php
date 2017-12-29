<?php
class Advgroup_Form_Member_RemoveBlackList extends Engine_Form
{


	protected $_group_id;
	public function setGroup($group_id) {
		$this->_group_id = $group_id;
	}
	public function init()
	{
		$this
		->setTitle('Promote Member')
		->setDescription('Are you sure you want to remove member(s) from black list?')
		;

		$this->addElement('Button', 'add', array(
				'label' => 'Remove',
				'link' => true,
				'type' => 'submit',
				'href' => '',
				'decorators' => array(
						'ViewHelper'
				),
		));


		$this->addElement('Cancel', 'cancel', array(
				'prependText' => ' or ',
				'label' => 'cancel',
				'link' => true,
				'href' => '',
				'onclick' => 'parent.Smoothbox.close();',
				'decorators' => array(
						'ViewHelper'
				),
		));

		$this->addDisplayGroup(array(
				'add',
				'cancel'
		), 'buttons');
	}
}