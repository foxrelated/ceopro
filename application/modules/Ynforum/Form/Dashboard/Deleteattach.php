<?php

class Ynforum_Form_Dashboard_Deleteattach extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Delete Attachment')
      ->setDescription('Are you sure you want to delete this attachment?')
      ;

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Delete Attachment',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'order' => 20,
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
      'order' => 21,
    ));

    $this->addDisplayGroup(array(
      'execute',
      'cancel'
    ), 'buttons', array(

    ));
  }
}