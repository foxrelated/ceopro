<?php
class Advgroup_Form_Admin_Subcategory extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this->setMethod('post');

    /*
    $type = new Zend_Form_Element_Hidden('type');
    $type->setValue('heading');
    */
    $parent = new Zend_Form_Element_Select('parent_id',array());
    $parent->setLabel('Parent Category');
    
    $label = new Zend_Form_Element_Text('label');
    $label->setLabel('Category Name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');


    $id = new Zend_Form_Element_Hidden('id');


    $this->addElements(array(
      $parent,
      $label,
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

   // $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

  public function setField($category)
  {
    $this->_field = $category;

    // Set up elements
    //$this->removeElement('type');
    $this->parent_id->setValue($category->parent_id);
    $this->label->setValue($category->title);
    $this->id->setValue($category->category_id);
    $this->submit->setLabel('Edit Category');

    // @todo add the rest of the parameters
  }
}