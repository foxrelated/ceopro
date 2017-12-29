<?php
class Advgroup_Form_Member_Promote extends Engine_Form
{


	protected $_group_id;
	public function setGroup($group_id) {
		$this->_group_id = $group_id;
	}
	public function init()
	{
		$this
		->setTitle('Promote Member')
		->setDescription('Are you sure you want to promote member(s) to officer?')
		;

		$this->addElement('Button', 'promote', array(
				'label' => 'Promote Member',
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
				'promote',
				'cancel'
		), 'buttons');
	}
}