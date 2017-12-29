<?php
class Ynmultilisting_Form_Review_Create extends Engine_Form
{
	protected $_listing;
	protected $_ratingTypes;
	protected $_reviewTypes;
	
	public function getReviewTypes()
	{
		return $this -> _reviewTypes;
	}
	
	public function setReviewTypes($reviewTypes)
	{
		$this -> _reviewTypes = $reviewTypes;
	} 
	
	public function getRatingTypes()
	{
		return $this -> _ratingTypes;
	}
	
	public function setRatingTypes($ratingTypes)
	{
		$this -> _ratingTypes = $ratingTypes;
	} 
	
	public function getListing()
	{
		return $this -> _listing;
	}
	
	public function setListing($listing)
	{
		$this -> _listing = $listing;
	} 
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
    $this->setTitle('Add Review');
	$this->setAttrib('class', 'global_form_popup');
	
	$this -> addElement('dummy', 'rate', array(
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_review_listing.tpl',
					'ratingTypes' =>  $this -> _ratingTypes,
					'class' => 'form element',
				)
			)), 
	));  
	
	foreach($this -> _reviewTypes as $reviewType)
	{
		$this -> addElement('Text', 'review_'.$reviewType->getIdentity(), array(
			  'label' => $view->translate($reviewType -> title),
		      'allowEmpty' => false,
		      'required' => true,
		      'validators' => array(
		        array('NotEmpty', true),
		      ),
		      'filters' => array(
		        new Engine_Filter_Censor(),
	  		  ),
		));  
	}
	
	$this->addElement('Text', 'title', array(
      'label' => 'Review Title',
      'placeholder' => 'Title of the review...',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 128)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
	$this->addElement('Textarea', 'pros', array(
      'label' => 'Pros',
      'filters' => array(
        	new Engine_Filter_HtmlSpecialChars(),
        	new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
      ),
    ));
	
	$this->addElement('Textarea', 'cons', array(
      'label' => 'Cons',
      'filters' => array(
        	new Engine_Filter_HtmlSpecialChars(),
        	new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
      ),
    ));
	
	$this->addElement('Textarea', 'overal_review', array(
        'label' => 'Your Review',
        'allowEmpty' => false,
      	'required' => true,
        'filters' => array(
        	new Engine_Filter_HtmlSpecialChars(),
        	new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
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
