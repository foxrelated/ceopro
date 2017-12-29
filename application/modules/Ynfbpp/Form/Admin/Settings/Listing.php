<?php

class Ynfbpp_Form_Admin_Settings_Listing extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Listing Settings for Profile Popup') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');

        $this -> addElement('Radio', 'ynfbpp_listing_enabled', array(
            'label' => "Show profile popup when hover over a listing's link",
            'description' => "Show profile popup when hover over a listing's link on any page",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.listing.enabled', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_listing_description', array(
            'label' => 'Show Description',
            'description' => 'Show description of listing in 2 lines',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.listing.description', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_listing_price', array(
            'label' => 'Price',
            'description' => 'Show price of listing on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.listing.price', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_listing_enddate', array(
            'label' => 'Show End Date',
            'description' => 'Show end date of listing on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.listing.enddate', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_listing_location', array(
            'label' => 'Show Location',
            'description' => 'Show location of listing on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.listing.location', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_listing_category', array(
            'label' => 'Show Category',
            'description' => 'Show category of listing on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.listing.category', 1),
        ));


        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
