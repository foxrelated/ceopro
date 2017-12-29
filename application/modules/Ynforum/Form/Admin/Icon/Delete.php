<?php
class Ynforum_Form_Admin_Icon_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Icon?')
      ->setDescription('Are you sure you want to delete this icon?')
      ->setAttrib('class', '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;

    // Init submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Icon',
      'type' => 'submit',
            'decorators' => array(
          array('ViewScript', array(
                'viewScript' => '_formButtonCancel.tpl',
                'class'      => 'form element'
          ))
      ),
    ));
  }
}