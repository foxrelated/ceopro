<?php
class Advgroup_Form_Member_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Member Title')
      ->setDescription('Enter a special title for this person.')
      ->setAttrib('id', 'group_form_title')
      ;

    //$this->addElement('Hash', 'token');

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Save Changes',
      'decorators' => array(
        'ViewHelper'
      ),
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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}