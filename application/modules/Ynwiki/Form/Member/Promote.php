<?php
class Ynwiki_Form_Member_Promote extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Promote Member')
      ->setDescription('Are you sure you want to promote this member to officer?')
      ;

    //$this->addElement('Hash', 'token');

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'label' => 'Promote Member',
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
      'submit',
      'cancel'
    ), 'buttons');
  }
}