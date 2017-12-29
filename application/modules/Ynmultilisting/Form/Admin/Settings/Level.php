<?php
class Ynmultilisting_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNMULTILISTING_SETTINGS_LEVEL_DESCRIPTION');
        
        $levels = array();
        $table  = Engine_Api::_()->getDbtable('levels', 'authorization');
        foreach ($table->fetchAll($table->select()) as $row) {
            $levels[$row['level_id']] = $row['title'];
        }
        
        $this->addElement('Select', 'level_id', array(
            'label' => 'Member Level',
            'multiOptions' => $levels,
            'ignore' => true
        ));
        if( !$this->isPublic() ) {
            if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                
                $this->addElement('Integer', 'first_amount', array(
                    'label' => 'Credit for creating listings',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'period', array(
                    'description' => 'Period (days)',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
                                
                $this->addElement('Radio', 'use_credit', array(
                'label' => 'Allow users to use Credit to purchase Listing',
                'multiOptions' => array(
                    1 => 'Yes, allow users to purchase Listing by Credit.',
                    0 => 'No, do not allow users to purchase Listing by Credit.'
                ),
                'value' => 1,
            ));
            }
    
            $this->addElement('Radio', 'create', array(
                'label' => 'Allow Creation of Listing',
                'multiOptions' => array(
                    1 => 'Yes, allow users to create new listing.',
                    0 => 'No, do not allow users to create new listing.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'edit', array(
                'label' => 'Allow Editing of Listing',
                'multiOptions' => array(
                    2 => 'Yes, allow users to edit all listings.',
                    1 => 'Yes, allow users to edit their own listings.',
                    0 => 'No, do not allow users to edit their own listings.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Listing',
                'multiOptions' => array(
                    2 => 'Yes, allow users to delete all listings.',
                    1 => 'Yes, allow users to delete their own listings.',
                    0 => 'No, do not allow users to delete their own listings.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow Viewing Details of Listing',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view listings.',
                    0 => 'No, do not allow users to view listings.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'comment', array(
                'label' => 'Allow Commenting on Listing',
                'multiOptions' => array(
                    1 => 'Yes, allow users to comment on listings.',
                    0 => 'No, do not allow users to comment on listings.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'close', array(
                'label' => 'Allow Closing of Listing',
                'multiOptions' => array(
                    2 => 'Yes, allow users to close all listings.',
                    1 => 'Yes, allow users to close their own listings.',
                    0 => 'No, do not allow users to close their own listings.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            
            $this->addElement('Radio', 'review', array(
                'label' => 'Allow Review & Rating on Listing',
                'multiOptions' => array(
                    1 => 'Yes, allow users to review & rate on listings.',
                    0 => 'No, do not allow users to review & rate on listings.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'auto_approve', array(
                'label' => 'Allow auto approve listings created by these users?',
                'multiOptions' => array(
                    1 => 'Yes, allow auto approve.',
                    0 => 'No, do not allow auto approve.'
                ),
                'value' => 1,
            ));
                        
            $this->addElement('Integer', 'max_listing', array(
                'label' => 'Maximum Listings the user can create',
                'description' => 'Set 0 is unlimited',
                'required' => true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 20,
            ));
            
            $roles = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'owner' => 'Just Me'
            );
            
            $roles_values = array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner');
            $auths = array('view', 'comment', 'share', 'photo', 'video', 'discussion');
            foreach ($auths as $auth) {
                $this->addElement('MultiCheckbox', 'auth_'.$auth, array(
                    'label' => ucfirst($auth).' Privacy',
                    'description' => 'YNMULTILISTING_AUTH_'.strtoupper($auth).'_DESCRIPTION',
                    'multiOptions' => $roles,
                    'value' => $roles_values        
                ));
            }
        }
        else {
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow Viewing Details of Listing',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view listings.',
                    0 => 'No, do not allow users to view listings.'
                ),
                'value' => 1,
            ));
        } 
        
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));        
    }
}