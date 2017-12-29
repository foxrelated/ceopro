<?php
class Ynmember_Form_Relationship_Addstatus extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Relationship');
    $this->setAttribs(array('class' => 'ynmember-form-edit-relationship global_form'));
	
    //Init Relationship status
    $tableRelationship = Engine_Api::_() -> getItemTable('ynmember_relationship');
    $relationship = $tableRelationship -> getAllRelationships();
    $relationshipOptions = array('0' => '');
    foreach ($relationship as $item )
    {
    	$relationshipOptions[$item -> getIdentity()] = $item -> status;
    }
    $this->addElement('Select', 'relationship', array(
      'label' => 'Relationship Status',
      'multiOptions' => $relationshipOptions,
      'onchange' => 'changeStatus();',
    ));
    
	$this->addElement('Text', 'with', array(
            'label' => 'With',
			'autocomplete' => 'off',
    ));
    Engine_Form::addDefaultDecorators($this -> with);
   
    // Init to Values
	$this -> addElement('Hidden', 'toValues', array(
		// 'label' => 'With', => It show double label 'With' in front end
		'required' => false,
		'allowEmpty' => true,
		'style' => 'margin-top:-5px',
		'order' => 1,
		'filters' => array('HtmlEntities'),
	));
	Engine_Form::addDefaultDecorators($this -> toValues);
    
	$this->addElement('Text', 'anniversary', array(
            'label' => 'Anniversary',
            'class' => 'date_picker input_small',
			'required' => false,
			'allowEmpty' => true,
    ));

    $this -> addElement('hidden', 'auth_view', array(
			'value' => 'self',
			'order' => '99'
	));
		
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
  }
}

