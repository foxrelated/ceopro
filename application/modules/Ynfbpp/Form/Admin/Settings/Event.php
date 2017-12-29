<?php

class Ynfbpp_Form_Admin_Settings_Event extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Event Settings for Profile Popup') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');

        $this -> addElement('Radio', 'ynfbpp_event_description', array(
            'label' => 'Show Description',
            'description' => 'Show event description',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.event.description', 1),
        ));
        $this -> addElement('Radio', 'ynfbpp_event_owner', array(
            'label' => 'Show Event Owner',
            'description' => 'Show event owner on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.event.owner', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_event_mutual', array(
            'label' => 'Show Friends',
            'description' => 'Show friends who joined to event',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.event.mutual', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_event_location', array(
            'label' => 'Show Location',
            'description' => 'Show location of event on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.event.location', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_event_time', array(
            'label' => 'Show start and end time',
            'description' => 'Show start time and end time of event on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.event.time', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_event_status', array(
            'label' => 'Show status of event',
            'description' => 'Show status of event on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.event.status', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_event_website', array(
            'label' => 'Show website',
            'description' => 'Show website of event on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.event.website', 1),
        ));

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
