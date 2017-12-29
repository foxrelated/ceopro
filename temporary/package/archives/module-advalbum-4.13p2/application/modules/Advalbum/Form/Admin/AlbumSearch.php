<?php
class Advalbum_Form_Admin_AlbumSearch extends Engine_Form {
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
      'order' => 1
    ));
    //Search Owner
    $this->addElement('Text', 'owner_name', array(
      'label' => 'Owner',
      'order' => 2
    ));

    //Featured Filter
    $this->addElement('Select', 'featured', array(
      'label' => 'Filter By',
      'multiOptions' => array(
        ''  => 'All',
        '1' => 'Only Featured Album',
        '0' => 'Only Non Featured Album',
    ),
      'value' => 'all',
      'order' => 4
    ));

     // Element: order
    $this->addElement('Hidden', 'orderby', array(
      'order' => 101,
      'value' => 'album_id'
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
      'order' => 5
    ));

    $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
  }
}