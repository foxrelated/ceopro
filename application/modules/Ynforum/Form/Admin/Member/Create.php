<?php
class Ynforum_Form_Admin_Member_Create extends Engine_Form {
    private $_type = 'forum';
    
    public function __construct($options = null) {
        if ($options && is_array($options) && array_key_exists('form_type', $options)) {
            $this->_type = $options['form_type'];
            unset ($options['form_type']);
        }
        parent::__construct($options);
    }
    
    public function init() {
        $this->setTitle('Add Member')
                ->setDescription("Search for a member to allow this member can view this $this->_type.")
                ->setAttrib('id', 'forum_form_admin_member_create')
                ->setAttrib('class', 'global_form_popup')
        ;

        $this->addElement('Text', 'username', array(
            'label' => 'Member Name'
        ));
        
        $this->addElement('Hidden', 'user_id', array(
            'label' => 'User Identity',
            'required' => true,
            'allowEmpty' => false,
        ));

        // Buttons
        $this->addElement('Button', 'execute', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
        //$button_group->addDecorator('DivDivDivWrapper');
    }
}