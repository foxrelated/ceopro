<?php
class Ynadvsearch_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Global Settings')
        
        ->setDescription('YNADVSEARCH_GLOBAL_SETTINGS_DESCRIPTION');
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Integer', 'num_autosuggest', array(
            'label' => 'Number of autosuggest items shown on search bar',
            'value' => $settings->getSetting('ynadvsearch_num_searchitem', 10),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}