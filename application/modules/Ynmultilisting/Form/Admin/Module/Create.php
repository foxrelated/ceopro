<?php
class Ynmultilisting_Form_Admin_Module_Create extends Engine_Form {

    public function init() {
        $this->setTitle('Add New Module');
        $this->setDescription('Add new modules to allow your users to import listing from these modules.');
        
        $this->addElement('Text', 'title', array(
            'label' => 'Module Name',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'table_item', array(
            'label' => 'Database table item',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'owner_id_column', array(
            'label' => 'Content owner field in table',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'title_column', array(
            'label' => 'Listing title',
            'description' => 'Enter the column name which data will be listing title',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
		$this->addElement('Text', 'short_description_column', array(
            'label' => 'Listing short description',
            'description' => 'Enter the column name which data will be listing short description',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
		
        $this->addElement('Text', 'description_column', array(
            'label' => 'Listing description',
            'description' => 'Enter the column name which data will be listing description',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'photo_id_column', array(
            'label' => 'Listing main image',
            'description' => 'Enter the column name which data will be listing main image',
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'about_us_column', array(
            'label' => 'About Us',
            'description' => 'Enter the column name which data will be about us',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'price_column', array(
            'label' => 'Price',
            'description' => 'Enter the column name which data will be price',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'currency_column', array(
            'label' => 'Currency',
            'description' => 'Enter the column name which data will be currency',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'location_column', array(
            'label' => 'Location',
            'description' => 'Enter the column name which data will be location',
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'long_column', array(
            'label' => 'Location Longitude',
            'description' => 'Enter the column name which data will be location longitude',
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'lat_column', array(
            'label' => 'Location Latitude',
            'description' => 'Enter the column name which data will be location latitude',
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Button', 'submit_btn', array(
            'type' => 'submit',
            'label' => 'Submit',
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
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
    }
}