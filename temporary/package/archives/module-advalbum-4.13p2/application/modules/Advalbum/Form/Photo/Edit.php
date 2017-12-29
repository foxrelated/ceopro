<?php
class Advalbum_Form_Photo_Edit extends Engine_Form
{
  protected $_isArray = true;

  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements');

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'filters' => array(
      	new Engine_Filter_StringLength(array('max' => '64'))
      ),
      'decorators' => array(
		  'ViewHelper',
		        array('HtmlTag', array('tag' => 'div', 'class'=>'albums_editphotos_title_input')),
		        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'albums_editphotos_title')),
		      ),
    ));

    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'onKeyPress' => "return disableEnterKey(event)",
       'placeholder' => '',
      'filters' => array(
      ),
      'decorators' => array(
  		'ViewHelper',
        	array('HtmlTag', array('tag' => 'div', 'class'=>'albums_editphotos_title_input')),
        	array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'albums_editphotos_title')),
      ),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Caption',
      'rows' => 3,
      'cols' => 120,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'filters' => array(
    		new Engine_Filter_StringLength(array('max' => '255'))
       ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class'=>'album_editphotos_caption_input')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class'=>'albums_editphotos_caption_label')),
      ),
    ));

    $this->addElement('Date', 'taken_date', array(
    		'label' => 'Date Taken',
    ));
    
    $this->addElement('Multiselect', 'color', array(
    		'label' => "Main colors",
    		'multiOptions' => array(),
    		//'value' => array_keys($colors),
    		'required' => false,
    		'allowEmpty' => true,
    		'style' => "width: 255px;"
    ));
    

     $this->addElement('Dummy', 'dummy');

    $this->addElement('Checkbox', 'delete', array(
      'label' => "Delete Photo",
      'decorators' => array(
        'ViewHelper',
        array('Label', array('placement' => 'APPEND')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'photo-delete-wrapper')),
      ),
    ));

    $this->addElement('Hidden', 'photo_id', array(
      'validators' => array(
        'Int',
      )
    ));
    
    
  }
}