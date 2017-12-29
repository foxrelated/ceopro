<?php
class Ynmultilisting_Form_Admin_Quicklink_Create extends Engine_Form {
    
    protected $_currencies = array();
    protected $_params = array();
    protected $_quicklink = null;
    
    public function getCurrencies() {
        return $this -> _currencies;
    }
    
    public function setCurrencies($currencies) {
        $this -> _currencies = $currencies;
    }
    
    public function getParams() {
        return $this -> _params;
    }
    
    public function setParams($params) {
        $this -> _params = $params;
    }
    
    public function getQuicklink() {
        return $this -> _quicklink;
    }
    
    public function setQuicklink($quicklink) {
        $this -> _quicklink = $quicklink;
    }
    
    public function init() {
        $params = $this->getParams();
        $quicklink = $this->getQuicklink();
        $this->setTitle('Add New Quick Link');
        $this->setDescription('Quick link is a reference to a list of items which satisfy your searching criteria. Please select your desired criteria and save the corresponding items on the quick link.');
        
        $this-> setAttrib('class', 'global_form_popup');
        $this->addElement('Text', 'title', array(
            'label' => 'Quick link name',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Heading', 'find_listing', array(
            'label' => 'Find listings',
            'description' => 'You may select the range of listings to be the quick link. Please choose the listing category and fill-in the information (optional)'
        ));
        
        $this->addElement('Multiselect', 'category_ids', array(
            'label' => 'Listings in categories*',
            'description' => 'Click Crl + if you want to search in multiple categories. In case you do not select any category, the others fill-in information below is not valid.',
        ));
        
        
       $this -> addElement('Dummy', 'prices', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_quicklink_price.tpl',
                    'currencies' => $this->getCurrencies(),
                    'params' => $this->getParams(),
                    'quicklink' => $this->getQuicklink()
                )
            )), 
        ));
        
        //location
        $location = '';
        if ($quicklink) $location = $quicklink->location;
        if (isset($params['location'])) $location = $params['location'];
        $this -> addElement('Dummy', 'location_map', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_location_search.tpl',
                    'location' => $location
                )
            )), 
        ));
        
        $this -> addElement('hidden', 'latitude', array(
            'value' => '0',
            'order' => '98'
        ));
        
        $this -> addElement('hidden', 'longitude', array(
            'value' => '0',
            'order' => '99'
        ));
        
        $this -> addElement('Text', 'radius', array(
            'label' => 'Radius (miles)',
            'required' => false,
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
		$this->addElement('Text', 'expire_from', array(
            'label' => 'Expire from',
            'class' => 'date_picker'
        ));
        
        $this->addElement('Text', 'expire_to', array(
            'label' => 'Expire to',
            'class' => 'date_picker'
        ));
		
        $this -> addElement('Text', 'owners', array(
            'label' => 'Listing owners',
            'autocomplete' => 'off',
            'order' => '995'
        ));
        
        $this -> addElement('Hidden', 'owner_ids', array(
            'filters' => array('HtmlEntities'),
            'order' => '996'
        ));
        Engine_Form::addDefaultDecorators($this -> owner_ids);
        
		$this->addElement('Heading', 'listing_heading', array(
			'description' => 'Or choosing the selected listings to be the quick link by enter the listing name by its first character',
			'order' => '997'
		));
		
        $this -> addElement('Text', 'listings', array(
            'label' => 'Listing name',
            'autocomplete' => 'off',
            'order' => '998'
        ));
        
        $this -> addElement('Hidden', 'listing_ids', array(
            'filters' => array('HtmlEntities'),
            'order' => '999'
        ));
        Engine_Form::addDefaultDecorators($this -> listing_ids);        
        
        
        $this->addElement('Button', 'submit_btn', array(
            'type' => 'submit',
            'label' => 'Save',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons', array(
            'order' => '1000',
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
    }
}