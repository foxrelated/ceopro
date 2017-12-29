<?php
class Advalbum_Form_Photo_Location extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Add Location') -> setAttribs(array(
			'class' => 'global_form_popup global_content_simple',
			'id' => 'advalbum_photo_edit_location'
		));
		$this -> setDescription('Where was this photo taken?') -> setMethod('POST');

		$this -> addElement('Text', 'location', array(
			'onKeyPress' => "return disableEnterKey(event)",
			'style' => 'width:200pt',
			'placeholder' => ''
		));
		// Buttons
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));

		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => 'or ',
			'href' => '',
			'onclick' => 'parent.Smoothbox.close()',
			'decorators' => array('ViewHelper')
		));
		$this -> addDisplayGroup(array(
			'submit',
			'cancel'
		), 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}

}
