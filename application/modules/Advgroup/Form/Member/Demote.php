<?php
class Advgroup_Form_Member_Demote extends Engine_Form
{


	protected $_group_id;
	public function setGroup($group_id) {
		$this->_group_id = $group_id;
	}
	public function init()
	{
		$this
		->setTitle('Demote Member')
		->setDescription('Are you sure you want to demote member(s) from officer?')
		;

		$this->addElement('Button', 'demote', array(
				'label' => 'Demote Member',
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
				'Demote',
				'cancel'
		), 'buttons');
	}
}