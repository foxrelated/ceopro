<?php
class Advgroup_Form_Poll_Close extends Engine_Form
{
    public function init()
  {
    $this->setTitle('Close Poll')
         ->setAttrib('class', 'global_form_popup')
      ->setDescription('Are you sure that you want to close this poll?');

     // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Close Poll',
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
