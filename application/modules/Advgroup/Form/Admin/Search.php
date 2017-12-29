<?php
class Advgroup_Form_Admin_Search extends Engine_Form {
  public function init()
  {
    $this->clearDecorators()
         ->addDecorator('FormElements')
         ->addDecorator('Form')
         ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
         ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));

    //Search Title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
    ));

    //Search Owner
    $this->addElement('Text','owner',array(
      'label' => 'Owner',
    ));

    //Feature Filter
    $this->addElement('Select', 'filter', array(
      'label' => 'Filter By',
      'multiOptions' => array(
        ''  => '',
        '0' => 'Only Featured Groups',
        '1' => 'Only Not Featured Groups',
        '2' => 'Only Sub Groups',
        '3' => 'Only Parent Groups',
    ),
      'value' => '',
    ));

     // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 101,
      'value' => 'group_id'
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 102,
      'value' => 'DESC',
    ));

     // Element: direction
    $this->addElement('Hidden', 'page', array(
      'order' => 103,
    ));

     // Buttons
    $this->addElement('Button', 'button', array(
      'label' => 'Search',
      'type' => 'submit',
    ));

    $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
  }
}