<?php
class Ynforum_Form_Admin_Report_DeletePost extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Post?')
      ->setDescription('Are you sure you want to delete this post?')
      ->setAttrib('class', '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;

    // Init submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Post',
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