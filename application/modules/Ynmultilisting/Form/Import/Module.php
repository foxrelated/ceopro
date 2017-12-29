<?php
class Ynmultilisting_Form_Import_Module extends Engine_Form {
    public function init() {
        // Init form
        $this
          ->setAttrib('name', 'ynmultilisting-import-module')
          ->setMethod('GET');
    	
		$this->addElement('Select', 'listingtype', array(
            'label' => 'Listing Type',
        ));
		
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
        ));
        
    	$this->addElement('Select', 'module_id', array(
            'label' => 'Module',
        ));
    	
		$this->addElement('Radio', 'all_owner', array(
			'label' => 'Search Owner',
			'multiOptions' => array(
				1 => 'All owners',
				0 => 'Some specific owners'
			),
			'value' => 1
		));
		
		$this -> addElement('Text', 'owners', array(
            'autocomplete' => 'off',
            'order' => '996'
        ));
		
		$this -> addElement('Hidden', 'owner_ids', array(
            'filters' => array('HtmlEntities'),
            'order' => '997'
        ));
		Engine_Form::addDefaultDecorators($this -> owner_ids);
		
        // Init submit
        $this->addElement('Button', 'submit_btn', array(
            'ignore' => true,
            'label' => 'Import Listings',
            'type'  => 'submit',
            'order' => '998'
        ));
  }

}

