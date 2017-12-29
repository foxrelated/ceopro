<?php
class Ynmember_Form_ReviewFields extends Fields_Form_Standard
{
  protected $_fieldType = 'ynmember_review';

  public $_error = array();
  
  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';
	
  public function init()
  {
    // custom classified fields
    if( !$this->_item ) {
      $review = new Ynmember_Model_Review(array());
      $this->setItem($review);
    }
	
    parent::init();
    $this->removeElement('submit');
  }

  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this
        ->addDecorator('FormElements')
        ;
    }
  }
}