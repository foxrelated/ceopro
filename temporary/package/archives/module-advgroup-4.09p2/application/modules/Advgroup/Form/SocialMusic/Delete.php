<?php

class Advgroup_Form_SocialMusic_Delete extends Engine_Form {
	protected $_type = 'ynmusic_album';
	public function getType() {
		return $this -> _type;
	}
	
	public function setType($type) {
		$this -> _type = $type;
	}
	
  	public function init() {
  		
		$view = Zend_Registry::get('Zend_View');
		$description = ($this->getType() == 'ynmusic_album') ? $view->translate('album') : $view->translate('song');	
    	$this->setTitle('Delete Social Music')
      		->setDescription($view->translate('Are you sure you want to delete this %s?', $description))
      		->setAttrib('class', 'global_form_popup')
      		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      		->setMethod('POST');

    
    	// Buttons
    	$this->addElement('Button', 'submit', array(
  			'label' => 'Delete',
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