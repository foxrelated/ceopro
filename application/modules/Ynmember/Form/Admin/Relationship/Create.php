<?php
class Ynmember_Form_Admin_Relationship_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Add New Status')
      ->setAttrib('class', 'global_form_popup')
      ;
	
	$this->addElement('Text', 'status', array(
      'label' => 'Status',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
    $this->addElement('Checkbox', 'with_member', array(
      'label' => 'With other member?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));
	
	 $this->addElement('Checkbox', 'appear_feed', array(
      'label' => 'Appear in News Feed?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));
	
	 $this->addElement('Checkbox', 'user_approved', array(
      'label' => 'Need approval from partner?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));
		
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create',
      'type' => 'submit',
      'onclick' => 'removeSubmit()',
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
  }
}

