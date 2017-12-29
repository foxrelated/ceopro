<?php
class Ynmember_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNMEMBER_SETTINGS_LEVEL_DESCRIPTION');
        
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
                    'label' => 'Add credit for reviewing member',
                    'description' => 'No of first actions',
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
            }
    
			$this->addElement('Radio', 'can_review_members', array(
                'label' => 'Can Write Review for Members?',
                'desc' => 'Do you want to let members write review for members?',
                'multiOptions' => array(
                    1 => 'Yes, allow writing review for members.',
                    0 => 'No, do not allow writing review for members.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'can_review_oneself', array(
                'label' => 'Can Write Review for Oneself?',
                'desc' => 'Do you want to let members write review for oneself?',
                'multiOptions' => array(
                    1 => 'Yes, allow writing review for oneself.',
                    0 => 'No, do not allow writing review for oneself.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'can_edit_own_review', array(
                'label' => 'Can Edit Own Review?',
                'desc' => 'Do you want to let members edit own review?',
                'multiOptions' => array(
                    1 => 'Yes, allow editing own review.',
                    0 => 'No, do not allow editing review.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'can_like_members', array(
                'label' => 'Can Like a Member?',
                'desc' => 'Do you want to let members like a member?',
                'multiOptions' => array(
                    1 => 'Yes, allow to like a member.',
                    0 => 'No, do not allow to like a member.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'comment', array(
                'label' => 'Can Like and Comment a Review?',
                'desc' => 'Do you want to let members like and comment a review?',
                'multiOptions' => array(
                    1 => 'Yes, allow to like and comment a review.',
                    0 => 'No, do not allow to like and comment a review.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'can_share_reviews', array(
                'label' => 'Can Share a Review?',
                'desc' => 'Do you want to let members share a review?',
                'multiOptions' => array(
                    1 => 'Yes, allow sharing a review.',
                    0 => 'No, do not allow sharing a review.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'can_report_reviews', array(
                'label' => 'Can Report a Review?',
                'desc' => 'Do you want to let members report a review?',
                'multiOptions' => array(
                    1 => 'Yes, allow reporting a review.',
                    0 => 'No, do not allow reporting a review.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'can_delete_own_reviews', array(
                'label' => 'Can Delete Own Reviews?',
                'desc' => 'Do you want to let members delete their reviews?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to delete their reviews.',
                    0 => 'No, do not allow this member level to delete their reviews.'
                ),
                'value' => 1,
            ));
    		
    		
    		$roles = array(
    			'registered' => 'All Registered',
    			'network' => 'My Network',
    			'member' => 'My Friends',
    			'owner' => 'Only Me'
    		);
    		
    		$roles_values = array('registered', 'network', 'member', 'owner');
    		
            $this->addElement('MultiCheckbox', 'auth_get_notification', array(
    			'label' => 'Getting Notification Privacy',
    			'description' => 'YNMEMBER_GET_NOTIFICATION',
    			'multiOptions' => $roles,
    			'value' => $roles_values		
    		));
			
			$this->addElement('Integer', 'feature_fee', array(
                'label' => 'Fee To Feature Member One Day ($)',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 10,
            ));
        }
        
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));        
    }
}