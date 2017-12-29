<?php
class Ynadvsearch_Form_VideoSearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET')
      ;
    // prepare categories
    $categories = Engine_Api::_()->video()->getCategories();
    $categories_prepared[0] = "All Categories";
    foreach ($categories as $category){
      $categories_prepared[$category->category_id] = $category->category_name;
    }

    $this->addElement('Text', 'text', array(
      'label' => 'Search',
    ));

    $this->addElement('Hidden', 'tag');

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
        'rating' => 'Highest Rated',
      ),
    ));

    // category field
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => $categories_prepared,
    ));
    
    $this->addElement('Button', 'submit_btn', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}