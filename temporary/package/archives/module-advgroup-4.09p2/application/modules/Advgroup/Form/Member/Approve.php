<?php
class Advgroup_Form_Member_Approve extends Engine_Form
{
	
	protected $_group_id;
	public function setGroup($group_id) {
		$this->_group_id = $group_id;
	}
	
	
  public function init()
  {
    $this
      ->setTitle('Approve Group Membership Request')
      ->setDescription('Would you like to approve the request(s) for membership in this group?')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Button', 'approve', array(
				'label' => 'Approve Member',
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
      'approveRequest',
      'cancel'
    ), 'buttons');
  }
}