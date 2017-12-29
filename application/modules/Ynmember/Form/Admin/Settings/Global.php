<?php
class Ynmember_Form_Admin_Settings_Global extends Engine_Form {
	
    public function init() {
        $this
        ->setTitle('Global Settings')
        
        ->setDescription('YNMEMBER_GLOBAL_SETTINGS_DESCRIPTION');
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Radio', 'ynmember_allow_add_workplace', array(
            'label' => 'Add work and places members\'ve lived',
            'desc' => 'Do you want to let members add work and places member\'ve lived to their profile? If set to no, some other settings on this page may not apply.',
            'multiOptions' =>array(
	            1 => 'Yes, members can add location to their profile.',
	            0 => 'No, hide this field from their profile.',
       		 ),
            'value' => $settings->getSetting('ynmember_allow_add_workplace', 1),
        ));
		
		$this->addElement('Radio', 'ynmember_allow_search_location', array(
            'label' => 'Search by location',
            'desc' => 'Do you want to let members search by location?',
            'multiOptions' =>array(
	            1 => 'Yes, members can search by location.',
	            0 => 'No, do not allow members to search by location.',
       		 ),
            'value' => $settings->getSetting('ynmember_allow_search_location', 1),
        ));
		
		$this->addElement('Radio', 'ynmember_allow_update_relationship', array(
            'label' => 'Update relationship status',
            'desc' => 'Do you want to let members update their relationship status on their profiles?',
            'multiOptions' =>array(
	            1 => 'Yes, members can update relationship status.',
	            0 => 'No, do not allow members to update relationship status.',
       		 ),
            'value' => $settings->getSetting('ynmember_allow_update_relationship', 1),
        ));
		
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}