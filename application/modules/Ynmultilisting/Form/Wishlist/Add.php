<?php
class Ynmultilisting_Form_Wishlist_Add extends Engine_Form {

    public function init() {
        $this->setTitle('Add Listing to Wish List')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
        ;
		$view = Zend_Registry::get('Zend_View');
		
		$this->addElement('Select', 'wishlist_id', array(
			'label' => 'Select Wish List',
			'required' => true,
			'allowEmpty' => false,
			'multiOptions' => array(
				'0' => $view->translate('Create New Wish List')
			)
		));
		
		$this->addElement('Text', 'title', array(
			'label' => 'Wish List name',
			'filters' => array(
                'StripTags',
            )
		));
		
		$this->addElement('Textarea', 'description', array(
			'label' => 'Description',
			'filters' => array(
                'StripTags'
            )
		));
		
		// Privacy
	    $availableOptions = array(
	      'everyone'            => 'Everyone',
	      'registered'          => 'All Registered Members',
	      'owner_network'       => 'Friends and Networks',
	      'owner_member_member' => 'Friends of Friends',
	      'owner_member'        => 'Friends Only',
	      'owner'               => 'Just Me'
	    );
	
        $this->addElement('Select', 'view', array(
            'label' => 'View Privacy',
            'description' => 'Who may see this Wish List?',
            'multiOptions' => $availableOptions,
            'value' => key($availableOptions),
        ));
    	$this->view->getDecorator('Description')->setOption('placement', 'append');
		
        // Buttons
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Add to Wish List',
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
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }
}