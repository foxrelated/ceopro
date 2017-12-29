<?php

class Ynfbpp_Form_Admin_Settings_Store extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Store Settings for Profile Popup') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');

        $this -> addElement('Radio', 'ynfbpp_store_enabled', array(
            'label' => "Show profile popup when hover over a store's link",
            'description' => "Show profile popup when hover over a store's link on any page",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.store.enabled', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_store_description', array(
            'label' => 'Show Description',
            'description' => 'Show description of store in 2 lines',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.store.description', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_store_address', array(
            'label' => 'Show Address',
            'description' => 'Show address of store on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.store.address', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_store_website', array(
            'label' => 'Show Website',
            'description' => 'Show website of store on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.store.website', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_store_products', array(
            'label' => 'Show Number Of Products',
            'description' => 'Show number of products on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.store.products', 1),
        ));


        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
