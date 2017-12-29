<?php
class Advgroup_Form_Poll_Unclose extends Engine_Form
{
   public function init()
  {
    $this->setTitle('Reopen Poll')
         ->setAttrib('class', 'global_form_popup')
      ->setDescription('Are you sure that you want to reopen this poll?');

     // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Reopen Poll',
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
