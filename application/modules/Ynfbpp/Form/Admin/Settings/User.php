<?php

class Ynfbpp_Form_Admin_Settings_User extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('User Settings for Profile Popup') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');

        $this -> addElement('Radio', 'ynfbpp_user_enabled', array(
            'label' => "Show profile popup when hover over a user's link",
            'description' => "Show profile popup when hover over a user's link on any page",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.user.enabled', 1),
        ));

//        $this -> addElement('Radio', 'ynfbpp_user_status', array(
//            'label' => 'Show User Status',
//            'description' => 'Show user status under profile link',
//            'multiOptions' => array(
//                '1' => 'Yes',
//                '0' => 'No'
//            ),
//            'value' => $settings -> getSetting('ynfbpp.user.status', 1),
//        ));

//        $this -> addElement('Radio', 'ynfbpp_user_profile', array(
//            'label' => 'Show Profile Fields',
//            'description' => 'Show profile fields on popup',
//            'multiOptions' => array(
//                '1' => 'Yes',
//                '0' => 'No'
//            ),
//            'value' => $settings -> getSetting('ynfbpp.user.profile', 1),
//        ));

//        $this -> addElement('Text', 'ynfbpp_user_fieldlimit', array(
//            'label' => 'Number of Profile Fields',
//            'description' => 'How many profile fields will be shown on popup? (Enter a number from 1 to 10).',
//            'value' => $settings -> getSetting('ynfbpp.user.fieldlimit', 3),
//            'required'=>true,
//            'validators' => array('Int',array('Between',false,array(1,10)))
//        ));

        $this -> addElement('Radio', 'ynfbpp_user_mutual', array(
            'label' => 'Show Mutual Friend',
            'description' => 'Show mutual friends',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => $settings -> getSetting('ynfbpp.user.mutual', 1),
        ));

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
