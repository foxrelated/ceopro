<?php
class Ynadvsearch_Form_YnGroupSearch extends Engine_Form
{
  public function init()
  {
  	$translate = Zend_Registry::get("Zend_Translate");
     //Set Form Layout And Attributes.
    $this
      ->setAttribs(array( 'id' => 'filter_form',
                          'class' => 'global_form_box',
                           'method' => 'GET'
                    ));

      //Page Id Field.
    $this->addElement('Hidden','page',array(
        'order' => 100,
    ));

    $this->addElement('Hidden','tag',array(
        'order' => 101,
    ));
      //Search Text Field.
    $this->addElement('Text', 'text', array(
      'label' => 'Search Groups:',
    ));


    $this->text->setAttrib('placeholder','Search groups');
    
      //Category Field.
    $categories = Engine_Api::_()->getDbtable('categories', 'advgroup')->getAllCategoriesAssoc();
    if(count($categories) >= 1 ) {
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category:',
        'multiOptions' => $categories,
       
      ));
    }
	
	 //View Field.
    $this->addElement('Select', 'view_group', array(
      'label' => 'View:',
      'multiOptions' => array(
        '0' => 'Everyone\'s Groups',
        '1' => 'Only My Friends\' Groups',
      ),
      
    ));

      //Order Field
    $this->addElement('Select', 'order', array(
      'label' => 'List By:',
      'multiOptions' => array(
        'creation_date' => 'Recently Created',
        'member_count' => 'Most Popular',
        'most_active' => 'Most Active',
      ),
      'value' => 'creation_date',
     
    ));
	
	$this -> addElement('Text', 'location', array(
			'label' => 'Location',
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_location_search.tpl',
					'class' => 'form element',
				)
			)), 
		));
		
		$this -> addElement('Text', 'within', array(
			'label' => 'Radius (mile)',
			'placeholder' => $translate->translate('Radius (mile)..'),
			'maxlength' => '60',
			'required' => false,
			'style' => "display: block",
			'validators' => array(
				array(
					'Int',
					true
				),
				new Engine_Validate_AtLeast(0),
			),
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
		$this -> addElement('Button', 'Search', array(
			'label' => 'Search',
			'type' => 'submit',
		));
  }
}