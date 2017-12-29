<?php
class Ynwiki_Form_Report extends Engine_Form
{
   public function init()
  {
    // Init form
    $this
      ->setTitle('Report')
      ->setAttribs(array(
      'class' => 'global_form_popup',
      'id' => 'ynwiki_report'
      ))
      ;
    // Init Type
    $this->addElement('Select', 'type', array(
      'label' => 'Report type',
      'multiOptions' => array(
        'Spam content' => 'Spam content',
        'Wrong activity' => 'Wrong activity',
        'Bad content' => 'Bad content',
      ),
    ));
  	// Init message
    $this->addElement('Textarea', 'content', array(
      'label' => 'Content',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
   	$this->addElement('Button', 'submit', array(
      'label' => 'Send Report',
      'type' => 'submit',
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close()',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
	 $this->addDisplayGroup(array(
      'submit',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}