<?php
class Advgroup_Form_Event_Search extends Engine_Form
{
  public function init()
  {
  	$translate = Zend_Registry::get("Zend_Translate");
    $this
      ->setMethod('get')
      ->setAttrib('class', 'global_form_box')
			 ->setAttrib('id', 'filter_form')
      ;

    $this->addElement('Text', 'text', array(
      'label' => 'Search:',
      'placeholder' => $translate->translate('Search events')
    ));

    $this -> addElement('Select', 'view', array(
			'label' => 'View:',
			'multiOptions' => array(
				'' => 'Everyone\'s Events',
				'5' => 'My Friends\'s Events',
				'2' => 'Events I\'m Attending',
				'1' => 'Events I may Attending',
				'3' => 'Events I\'m Invited',
				'4' => 'Events I\'m Following',
			),
		));

		$this -> addElement('Select', 'order', array(
			'label' => 'List By:',
			'multiOptions' => array(
				'starttime ASC' => 'Start Time',
				'creation_date DESC' => 'Recently Created',
				'member_count DESC' => 'Most Popular',
			),

			'value' => 'creation_date DESC',
		));
		 // Buttons
    $this->addElement('Button', 'search', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}