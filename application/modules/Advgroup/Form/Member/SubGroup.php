<?php
class Advgroup_Form_Member_Subgroup extends Engine_Form
{


	protected $_group_id;
	public function setGroup($group_id) {
		$this->_group_id = $group_id;
	}
	public function init()
	{
		$this
		->setTitle('Add Member To SubGroup')
		->setDescription('Are you sure you want to add member(s) to sub group?')
		;

		$this->addElement('Button', 'promote', array(
				'label' => 'Add Member',
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