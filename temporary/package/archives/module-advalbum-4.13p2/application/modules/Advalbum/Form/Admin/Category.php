<?php
class Advalbum_Form_Admin_Category extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box')
      ;

    /*
    $type = new Zend_Form_Element_Hidden('type');
    $type->setValue('heading');
    */
    $this->addElement('Text','label',array(
        'label'     => 'Category Name',
        'required'  => true,
        'allowEmpty'=> false,
    ));

    $id = new Zend_Form_Element_Hidden('id');


    $this->addElements(array(
      //$type,
      $id
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
    $this->label->setValue($category->category_name);
    $this->id->setValue($category->category_id);
    $this->submit->setLabel('Edit Category');

    // @todo add the rest of the parameters
  }
}