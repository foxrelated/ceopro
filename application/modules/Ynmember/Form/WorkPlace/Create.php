<?php
class Ynmember_Form_WorkPlace_Create extends Engine_Form
{
  public function init()
  {
	$this -> setAttrib('id', 'add_place');
	$this -> setAttrib('onsubmit', 'return submitForm();')
        ->setAttrib('class', 'global_form_popup');
	
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Add a workplace');
	
	$this->addElement('Text', 'company', array(
      'label' => 'Company',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
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
		));

		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));
	
	$this->addElement('Checkbox', 'current', array(
      'label' => 'I currently work here',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
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
