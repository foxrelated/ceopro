<?php
class Ynmultilisting_Form_Admin_Package_Create extends Engine_Form
{
  protected $_package;
	
  public function getPackage()
  {
     return $this -> _package;
  }
  public function setPackage($package)
  {
     $this -> _package = $package;
  } 
  
  public function filterRound($value)
  {
    if( empty($value) ) {
		return '0';
    }
    return round($value, 2);
  }
  
  public function init()
  {
	 
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Add New Package');
	$this->setAttrib('class', 'global_form_popup');
	$this->setAttrib('onsubmit', 'removeSubmit()');
	
	$this->addElement('Text', 'title', array(
      'label' => 'Package Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
	$this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'cols' => '50',
      'rows' => '4',
      'maxlength' => '100',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
	// Element: levels
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    $multiOptions = array();
    foreach ($levels as $level) {
        $multiOptions[$level->getIdentity()] = $level->getTitle();
    }
    reset($multiOptions);
    $this->addElement('Multiselect', 'levels', array(
    	'description' => 'YNMULTILISTING_ADMIN_PACKAGE_LEVEL',
        'label' => 'Member Levels',
        'multiOptions' => $multiOptions,
        'value' => array_keys($multiOptions),
        'required' => true,
        'allowEmpty' => false,
    ));
	
	$this->addElement('Float', 'price', array(
      'label' => 'Price',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'YNMULTILISTING_ADMIN_PACKAGE_PRICE'
    ));
	
	$this->price -> addFilter('Callback', array(array($this, 'filterRound')));
	
	$this->addElement('Float', 'valid_amount', array(
      'label' => 'Valid Period',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'by days',
      'validators' => array(
                new Engine_Validate_AtLeast(1),
       ),
    ));
	
	if(!empty($this -> _package))
	{
		$this -> addElement('dummy', 'themes', array(
				'label'     => 'Select Themes',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_themes.tpl',
						'package' =>  $this -> _package,
						'class' => 'form element',
					)
				)), 
		));  
	}
	else
	{
		$this -> addElement('dummy', 'themes', array(
				'label'     => 'Select Themes',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_themes.tpl',
						'class' => 'form element',
					)
				)), 
		));  
	}
	
	$this->addElement('Integer', 'max_photos', array(
		'required'  => true,
        'allowEmpty'=> false,
        'label' => 'Maximum Photos On Photo Slideshow',
        'value' => 1,
        'validators' => array(
            new Engine_Validate_AtLeast(1),
      	 ),
    ));
	
	$this->addElement('Integer', 'max_videos', array(
		'required'  => true,
        'allowEmpty'=> false,
        'label' => 'Maximum Videos On Video Slideshow',
        'value' => 1,
        'validators' => array(
            new Engine_Validate_AtLeast(1),
      	 ),
    ));
	
     $this->addElement('Radio', 'allow_photo_tab', array(
      'label' => 'Allow adding photos to Photos tab',
      'multiOptions' => array(
        1 => 'Yes, allow users to add photos.',
        0 => 'No, do not allow users to add photos.'
      ),
      'value' => 1,
    ));
	
	$this->addElement('Radio', 'allow_video_tab', array(
      'label' => 'Allow adding videos to Videos tab',
      'multiOptions' => array(
        1 => 'Yes, allow users to add videos.',
        0 => 'No, do not allow users to add videos.'
      ),
      'value' => 1,
    ));
	
	$this->addElement('Radio', 'allow_discussion_tab', array(
      'label' => 'Allow adding discussions to Discussions tab',
      'multiOptions' => array(
        1 => 'Yes, allow users to add threads.',
        0 => 'No, do not allow users to add threads.'
      ),
      'value' => 1,
    ));
	
	$this->addElement('Checkbox', 'show', array(
      'label' => 'Show?',
      'description' => 'Show/Hide',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
   $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
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
