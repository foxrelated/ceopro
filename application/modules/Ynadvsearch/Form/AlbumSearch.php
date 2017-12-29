<?php
class Ynadvsearch_Form_AlbumSearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));

    parent::init();
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Albums:'
    ));

    $this->addElement('Select', 'sort', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
      ),
    ));
    
    // prepare categories
    $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();
    if( count($categories) > 0 ) {
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories,
      ));
    }
    
    $this->addElement('Button', 'submit_btn', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}