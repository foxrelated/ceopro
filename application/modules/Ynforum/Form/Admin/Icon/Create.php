<?php
class Ynforum_Form_Admin_Icon_Create extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Add Icon')
      ->setAttrib('id',      'form-add-icon')
      ->setAttrib('class',   '');
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Icon name*',
      'maxlength' => '63',
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
	$this -> title -> setAttrib("required", true);
    // Init icon art
     $this->addElement('File', 'icon', array(
      'label' => 'Icon image*',
      'required' => true,
    ));
	$this -> icon -> setAttrib("required", true);
    $this->icon->addValidator('Extension', false, 'jpeg, jpg, png, gif');
    // Init submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Icon',
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
