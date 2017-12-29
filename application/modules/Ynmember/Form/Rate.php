<?php
class Ynmember_Form_Rate extends Engine_Form
{
	protected $_formArgs;
	protected $_user;
	protected $_ratingTypes;
	
	public function getFormArgs()
	{
		return $this -> _formArgs;
	}
	
	public function setFormArgs($formArgs)
	{
		$this -> _formArgs = $formArgs;
	} 
	
	public function getRatingTypes()
	{
		return $this -> _ratingTypes;
	}
	
	public function setRatingTypes($ratingTypes)
	{
		$this -> _ratingTypes = $ratingTypes;
	} 
	
	public function getUser()
	{
		return $this -> _user;
	}
	
	public function setUser($user)
	{
		$this -> _user = $user;
	} 
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	
    $this->setTitle($view -> translate('Add review for %s',$this -> _user->getTitle()));
	$this->setAttrib('class', 'global_form_popup');
	
	$this -> addElement('dummy', 'rate', array(
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_rate_member.tpl',
					'viewModule' => 'ynmember',
					'ratingTypes' =>  $this -> _ratingTypes,
					'class' => 'form element',
				)
			)), 
	));  
	
	$this->addElement('Text', 'title', array(
      'label' => 'Review Title',
      'placeholder' => $view -> translate('Title of the review...'),
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
	
	// Add subforms
    if( !$this->_item ) {
      $customFields = new Ynmember_Form_ReviewFields($this -> _formArgs);
    } else {
      $customFields = new Ynmember_Form_ReviewFields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Ynmember_Form_Create' ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));
	
	$this->addElement('Textarea', 'summary', array(
        'label' => 'Summary',
        'allowEmpty' => false,
      	'required' => true,
        'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
		
    // Buttons
    $this->addElement('Button', 'submit', array(
      'value' => 'submit',
      'label' => 'Review',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
