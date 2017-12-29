<?php
class Ynadvsearch_Form_EventSearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setMethod('get')
      ->setAttrib('class', 'filters')
      ->setAttrib('id', 'filter_form')
      ;
    
    $this->addElement('Text', 'search_text', array(	
      'label' => 'Search Events:',	
    ));

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category:',
      'multiOptions' => array(
        '' => 'All Categories',
      )
    ));

    $this->addElement('Select', 'view', array(
      'label' => 'View:',
      'multiOptions' => array(
        '' => 'Everyone\'s Events',
        '1' => 'Only My Friends\' Events',
      ),
    ));

    $this->addElement('Select', 'order', array(
      'label' => 'List By:',
      'multiOptions' => array(
        'starttime ASC' => 'Start Time',
        'creation_date DESC' => 'Recently Created',
        'member_count DESC' => 'Most Popular',
      ),
      'value' => 'creation_date DESC',
    ));
    
        $this->addElement('Button', 'submit_btn', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}