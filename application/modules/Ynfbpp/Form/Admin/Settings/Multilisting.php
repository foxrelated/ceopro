<?php

class Ynfbpp_Form_Admin_Settings_Multilisting extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Multiple Listing Settings for Profile Popup') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');

        $this -> addElement('Radio', 'ynfbpp_multilisting_enabled', array(
            'label' => "Show profile popup when hover over a multilisting's link",
            'description' => "Show profile popup when hover over a multilisting's link on any page",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.multilisting.enabled', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_multilisting_description', array(
            'label' => 'Show Description',
            'description' => 'Show description of multilisting in 2 lines',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.multilisting.description', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_multilisting_price', array(
            'label' => 'Price',
            'description' => 'Show price of multilisting on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.multilisting.price', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_multilisting_enddate', array(
            'label' => 'Show End Date',
            'description' => 'Show end date of multilisting on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.multilisting.enddate', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_multilisting_location', array(
            'label' => 'Show Location',
            'description' => 'Show location of multilisting on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.multilisting.location', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_multilisting_category', array(
            'label' => 'Show Category',
            'description' => 'Show category of multilisting on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.multilisting.category', 1),
        ));


        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
