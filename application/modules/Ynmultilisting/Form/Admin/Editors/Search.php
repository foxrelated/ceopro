<?php
class Ynmultilisting_Form_Admin_Editors_Search extends Engine_Form {
  public function init()
  {
  	$view = Zend_Registry::get('Zend_View');
	
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
	$multiOptions = array();
	$tableListingType = Engine_Api::_() -> getItemTable('ynmultilisting_listingtype');
	$listingTypes = $tableListingType -> getAvailableListingTypes();
	$multiOptions['all'] = $view -> translate('All');
	foreach($listingTypes as $listingType)
	{
		$multiOptions[$listingType -> getIdentity()] = $listingType -> title;
	}
	$this->addElement('Select', 'listingtype_id', array(
        'label' => 'Listing types',
        'multiOptions' => $multiOptions,
        'value' => 'all',
	));
	
    //Editor Name
    $this->addElement('Text', 'title', array(
      'label' => 'Editor Name',
      'filters' => array(
            'StripTags'
      )
    ));
	
     // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 101,
      'value' => 'editor_id'
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