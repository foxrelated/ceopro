<?php
class Ynmultilisting_Form_Admin_Category extends Engine_Form
{
  protected $_field;
  
  protected $_category;
	
  public function getCategory()
 {
     return $this -> _category;
 }
 public function setCategory($category)
 {
     $this -> _category = $category;
 } 

  public function init()
  {
    $this->setMethod('post');
  
   $this->addElement('Hidden','id');
   
     //Location Name - Required
   $this->addElement('Text','label',array(
      'label'     => 'Category Name',
      'required'  => true,
      'allowEmpty'=> false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
	   ),
    ));
	
    //Rating Criteria
	$this->addElement('Text', 'rating_criteria', array(
        'label' => 'Rating Criteria',
        'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
	    'class' => 'btn_form_inline',
	    'description' => '<a name="add_more_rating_criteria" id="add_more_rating_criteria" type="button" class="fa fa-plus-circle" href="javascript:void(0);" onclick="javascript:void(0)"></a>',
    ));
	$this -> rating_criteria -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
	
    //Review Criteria
	$this->addElement('Text', 'review_criteria', array(
        'label' => 'Review Criteria',
        'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
	    'class' => 'btn_form_inline',
	    'description' => '<a name="add_more_review_criteria" id="add_more_review_criteria" type="button" class="fa fa-plus-circle" href="javascript:void(0);" onclick="javascript:void(0)"></a>',
    ));
	$this -> review_criteria -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
	
	
	$this -> addElement('File', 'photo', array('label' => 'Icon'));
	$this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	$this -> addElement('File', 'image', array('label' => 'Image'));
	$this -> image -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
   $this->addElement('Textarea','description',array(
      'label'     => 'Description',
      'required'  => false,
      'allowEmpty'=> true,
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Category',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function setField($category)
  {
    $this->_field = $category;

    // Set up elements
    //$this->removeElement('type');
    $this->label->setValue($category->getTitle());
    $this->id->setValue($category->category_id);
	if($category -> level == 1)
	{
		$this->description->setValue($category->description);
	}
    $this->submit->setLabel('Save');

    // @todo add the rest of the parameters
  }
}