<?php
class Ynwiki_Form_Location extends Engine_Form {
	public function init() {
		$this -> setTitle('Move Location') 
        -> setMethod('post') -> setAttrib('id', 'ynwiki_move');

		// Init to Values
        $this -> addElement('Hidden', 'toValues', array(
            'label' => 'New parent page',
            'required' => true,
            'allowEmpty' => false,
            'style' => 'margin-top:-5px',
            'order' => 0,
            'validators' => array('NotEmpty'),
            'filters' => array('HtmlEntities'),
        ));
        Engine_Form::addDefaultDecorators($this -> toValues);
        
        $this -> addElement('Text', 'to', array(
            'label' => 'New parent page',
			'autocomplete' => 'off',
            'order' => 1, 
            'description' => 'Start typing a page title to see a list of suggestions'
		));
		Engine_Form::addDefaultDecorators($this -> to);
        $this->to->getDecorator("Description")->setOption("placement", "append");   


		$this -> addElement('Button', 'submit', array(
			'label' => 'Submit',
			'type' => 'submit',
			'ignore' => true,
            'decorators' => array(
            'ViewHelper',
            ),
		));

		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true, 
			'prependText' => ' or ',
			'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view','pageId' => Zend_Controller_Front::getInstance()->getRequest()->getParam('pageId')), 'ynwiki_general', true),
            'decorators' => array(
            'ViewHelper',
          ),
		));
         // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
	}

}
