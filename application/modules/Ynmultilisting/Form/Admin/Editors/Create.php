<?php
class Ynmultilisting_Form_Admin_Editors_Create extends Engine_Form {
	public function init() {
		$this 
		  -> setTitle('Add New Editor')
          -> setDescription('Search member who will be added as a review editor.')
          -> setAttrib('class', 'global_form_popup');
          
		$this -> addElement('Text', 'to', array('label' => '*Editor', 'autocomplete' => 'off'));
		Engine_Form::addDefaultDecorators($this -> to);

		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'style' => 'margin-top:-5px',
			'order' => 1,
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);
		
		$multiOptions = array();
		$tableListingType = Engine_Api::_() -> getItemTable('ynmultilisting_listingtype');
		$listingTypes = $tableListingType -> getAvailableListingTypes();
		foreach($listingTypes as $listingType)
		{
			$multiOptions[$listingType -> getIdentity()] = $listingType -> title;
		}
		$this->addElement('Multiselect', 'listingtypes', array(
	    	'description' => 'Press Ctrl and click to select multiple types',
	        'label' => '*Listing types',
	        'multiOptions' => $multiOptions,
	        'value' => array_keys($multiOptions),
	        'required' => true,
	        'allowEmpty' => false,
    	));
		$this->listingtypes->getDecorator("Description")->setOption("placement", "append");
		
		$this -> addElement('Button', 'submit_btn', array(
			'label' => 'Submit',
			'type' => 'submit',
			'order' => 3,
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$onclick = 'parent.Smoothbox.close();';
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'order' => 4,
			'link' => true,
			'prependText' => ' or ',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper')
		));

		$this -> addDisplayGroup(array(
			'submit_btn',
			'cancel'
		), 'buttons');
	}

}
