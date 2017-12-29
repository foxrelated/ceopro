<?php
class Advalbum_Form_Album_DeletePhotos extends Engine_Form {
	public function init() {
		$this->setTitle ( 'Delete Photos' )
		->setDescription ( 'Are you sure that you want to delete selected photo(s)?' )
		->setAttrib ( 'class', 'global_form_popup' )
		->setAction ( Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( array () ) )
		->setMethod ( 'POST' );

		// Buttons
		$this->addElement ( 'Button', 'submit', array (
				'label' => 'Delete',
				'type' => 'submit',
				'ignore' => true,
				'decorators' => array (
						'ViewHelper'
				)
		) );

		$this->addElement ( 'Cancel', 'cancel', array (
				'label' => 'cancel',
				'link' => true,
				'prependText' => ' or ',
				'href' => '',
				'onclick' => 'parent.Smoothbox.close();',
				'decorators' => array (
						'ViewHelper'
				)
		) );
		$this->addDisplayGroup ( array (
				'submit',
				'cancel'
		), 'buttons' );
		$button_group = $this->getDisplayGroup ( 'buttons' );
	}
}