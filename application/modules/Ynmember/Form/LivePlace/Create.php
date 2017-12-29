<?php
class Ynmember_Form_LivePlace_Create extends Engine_Form
{
  public function init()
  {
	$this -> setAttrib('id', 'add_place');
	$this -> setAttrib('onsubmit', 'return submitForm();')
    ->setAttrib('class', 'global_form_popup');
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Add a living place');
	
	$this -> addElement('Dummy', 'location_map', array(
			'label' => 'Location',
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_location_search.tpl',
					'viewModule' => 'ynmember',
					'class' => 'form element',
					'location' => $this -> _location,
				)
			)), 
		));
		
		$this -> addElement('hidden', 'location_address', array(
			'value' => '0',
			'order' => '97',
			'required' => true
		));

		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));
	
    // Buttons
    $this->addElement('Button', 'addPlace', array(
      'label' => 'Save changes',
      'onclick' => 'removeSubmit()',
      'type' => 'button',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('addPlace', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
