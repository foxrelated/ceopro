<?php
class Ynmultilisting_Form_Admin_Review_Search extends Engine_Form {
    public function init() {
    	
		$view = Zend_Registry::get('Zend_View');
		
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form',
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
            'label' => 'Listing Type',
            'multiOptions' => $multiOptions,
        ));
		
		
		$arrTypes = array(
			'all' => $view -> translate('All'),
			'member' => $view -> translate('Member Only'),
			'editor' => $view -> translate('Editor Only'),
		);
		
		$this->addElement('Select', 'type', array(
            'label' => 'Review by',
            'multiOptions' => $arrTypes,
        ));
		
        $this->addElement('Text', 'reviewer_name', array(
            'label' => 'Reviewer',
        ));
		
		$this->addElement('Text', 'title', array(
            'label' => 'Listing Title',
        ));
        
        $this->addElement('Text', 'from_date', array(
            'label' => 'From Date',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Text', 'to_date', array(
            'label' => 'To Date',
            'class' => 'date_picker input_small',
        ));
        
	    // Element: order
	    $this->addElement('Hidden', 'order', array(
	      'order' => 101,
	      'value' => 'review_id'
	    ));
	
	    // Element: direction
	    $this->addElement('Hidden', 'direction', array(
	      'order' => 102,
	      'value' => 'DESC',
	    ));
		
        $this->addElement('Button', 'btn_submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->btn_submit->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}