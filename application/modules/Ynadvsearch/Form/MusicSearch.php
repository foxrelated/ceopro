<?php
class Ynadvsearch_Form_MusicSearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('GET')
      ;

    parent::init();
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Music:'
    ));

    $this->addElement('Select', 'show', array(
      'label' => 'Show',
      'multiOptions' => array(
        '1' => 'Everyone\'s Playlists',
        '2' => 'Only My Friends\' Playlists',
      ),
    ));

    $this->addElement('Select', 'sort', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
      ),
    ));

    $this->addElement('Hidden', 'user');
	$this->addElement('Hidden', 'page', array(
      'order' => 100
    ));
	$this->addElement('Button', 'search_bt', array(
      'label' => 'Search',
      'type' => 'submit',
       'order' => 101
    ));
  }
}