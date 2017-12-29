<?php

class Ynfbpp_Form_Admin_Settings_Business extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Business Settings for Profile Popup') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');

        $this -> addElement('Radio', 'ynfbpp_business_enabled', array(
            'label' => "Show profile popup when hover over a business's link",
            'description' => "Show profile popup when hover over a business's link on any page",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.business.enabled', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_business_description', array(
            'label' => 'Show Description',
            'description' => 'Show description of business in 2 lines',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.business.description', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_business_location', array(
            'label' => 'Show Location',
            'description' => 'Show location of business on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.business.location', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_business_website', array(
            'label' => 'Show Website',
            'description' => 'Show website of business on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.business.website', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_business_facebook', array(
            'label' => 'Show Facebook',
            'description' => 'Show Facebook of business on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.business.facebook', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_business_twitter', array(
            'label' => 'Show Twitter',
            'description' => 'Show Twitter of business on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.business.twitter', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_business_email', array(
            'label' => 'Show Email',
            'description' => 'Show email of business on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.business.email', 1),
        ));


        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
