<?php
class Ynadvsearch_Form_PollSearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)))
      ;

    parent::init();
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Polls:',
    ));

    $this->addElement('Select', 'show', array(
      'label' => 'Show',
      'multiOptions' => array(
        '1' => 'Everyone\'s Polls',
        '2' => 'Only My Friends\' Polls',
      ),
    ));
    
    $this->addElement('Select', 'closed', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => 'All Polls',
        '0' => 'Only Open Polls',
        '1' => 'Only Closed Polls',
      ),
    ));

    $this->addElement('Select', 'order', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
      ),
    ));
    
    $this->addElement('Button', 'submit_btn', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}