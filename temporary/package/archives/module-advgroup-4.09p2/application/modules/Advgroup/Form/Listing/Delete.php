<?php
class Advgroup_Form_Listing_Delete extends Engine_Form {
    public function init() {
        $this->setTitle('Remove Listing')
            ->setDescription('Are you sure you want to remove this listing?')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
    
        //$this->addElement('Hash', 'token');
        
        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Remove Listing',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));
    
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
  }
}