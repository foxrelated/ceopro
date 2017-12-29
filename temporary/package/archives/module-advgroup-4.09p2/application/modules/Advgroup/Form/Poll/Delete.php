<?php
class Advgroup_Form_Poll_Delete extends Engine_Form
{
      public function init()
  {
    $this->setTitle('Delete Poll')
         ->setAttrib('class', 'global_form_popup')
      ->setDescription('Are you sure that you want to delete this Poll? This action cannot be undone.');
  
     $this->addElement('Button', 'submit', array(
      'label' => 'Delete Poll',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}
?>
