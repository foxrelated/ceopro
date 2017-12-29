<?php

class Ultimatenews_Form_Edit extends Ultimatenews_Form_Create
{
  public function init()
  {
    parent::init();
    $this	->setTitle('Edit News')
    		->setAttrib('class', '')
			->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format' => 'smoothbox'), 'ultimatenews_edit_ultimatenews'))
	;
    $this->submit->setLabel('Save Changes');
  }
}