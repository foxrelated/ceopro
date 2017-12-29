<?php
class Ynforum_Form_Admin_Member_Delete extends Engine_Form {

    public function init() {
        $this->setTitle('Remove Member')->setDescription('Are you sure you want to remove this member?');

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Remove Member',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}