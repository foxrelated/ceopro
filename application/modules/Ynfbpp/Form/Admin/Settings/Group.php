<?php

class Ynfbpp_Form_Admin_Settings_Group extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Group Settings for Profile Popup') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');

        $this -> addElement('Radio', 'ynfbpp_group_enabled', array(
            'label' => "Show profile popup when hover over a group's link",
            'description' => "Show profile popup when hover over a group's link on any page",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.group.enabled', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_group_description', array(
            'label' => 'Show Description',
            'description' => 'Show description of group in 2 lines',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.group.description', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_group_owner', array(
            'label' => 'Show Group Owner',
            'description' => 'Show group owner on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.group.owner', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_group_mutual', array(
            'label' => 'Show Friends',
            'description' => 'Show friends who joined to group',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.group.mutual', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_group_location', array(
            'label' => 'Show Location',
            'description' => 'Show location of group on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.group.location', 1),
        ));

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
