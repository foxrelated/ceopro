<?php
class Ynmultilisting_Form_Review_Edit extends Engine_Form
{
	protected $_listing;
	protected $_ratingTypes;
	protected $_reviewTypes;
	protected $_item;
	
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
	
	public function getItem()
	{
		return $this -> _item;
	}
	
	public function setItem($item)
	{
		$this -> _item = $item;
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
    $this->setTitle('Edit review for '.$this -> _listing->getTitle());
	$this->setAttrib('class', 'global_form_popup');
	
	$this -> addElement('dummy', 'rate', array(
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_review_listing.tpl',
					'listing_id' => $this -> _listing -> getIdentity(),
					'edit' => 1,
					'ratingTypes' =>  $this -> _ratingTypes,
					'review' => $this->_item,
					'class' => 'form element',
				)
			)), 
	));  
	
	$tableReviewvalues = Engine_Api::_() -> getDbTable('reviewvalues','ynmultilisting');
	foreach($this -> _reviewTypes as $reviewType)
	{
		$reviewRow = $tableReviewvalues -> getRowReviewThisType($reviewType -> getIdentity(), $this ->_item -> getIdentity());
		$value = ($reviewRow)? $reviewRow -> content : "";
		$value = htmlspecialchars_decode($value);
		$value = strip_tags($value);
		$this -> addElement('Text', 'review_'.$reviewType->getIdentity(), array(
			  'label' => $view->translate($reviewType -> title),
		      'allowEmpty' => false,
		      'required' => true,
		      'validators' => array(
		        array('NotEmpty', true),
		      ),
	  		  'value' => $value,
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
    ));
	
	$this->addElement('Textarea', 'cons', array(
      'label' => 'Cons',
    ));
	
	$this->addElement('Textarea', 'overal_review', array(
        'label' => 'Your Review',
        'allowEmpty' => false,
      	'required' => true,
    ));
		
    // Buttons
    $this->addElement('Button', 'submit', array(
      'value' => 'submit',
      'label' => 'Save changes',
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
