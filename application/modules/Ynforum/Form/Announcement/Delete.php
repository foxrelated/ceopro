<?php
class Ynforum_Form_Announcement_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Announcement')
      ->setDescription('Are you sure you want to delete this announcement?');

     // Init submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Announcement',
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