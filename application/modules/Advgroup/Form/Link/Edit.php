<?php
class Advgroup_Form_Link_Edit extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Edit Useful Link') -> setAttrib('id', 'group_link_create');
		$this -> addElement('Hidden', 'link_id');
		$this -> addElement('Text', 'title', array(
			'label' => 'Title',
			'allowEmpty' => false,
			'required' => true,
			'style' => 'width:300px; margin: 5px 0 10px;',
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_HtmlSpecialChars(),
			),
		));

		$this -> addElement('Textarea', 'description', array(
			'label' => 'Description',
			'allowEmpty' => false,
			'required' => true,
			'description' => '(Max 256 characters)',
			'style' => 'margin: 5px 0 10px;',
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_HtmlSpecialChars(),
			)
		));
		$this -> addElement('Text', 'url', array(
			'label' => 'URL Link',
			'allowEmpty' => false,
			'style' => 'width:300px; margin: 5px 0 10px;',
			'required' => true,
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_HtmlSpecialChars(),
			),
		));

		$this -> addElement('Button', 'submit', array(
			'label' => 'Edit Link',
			'ignore' => true,
			'type' => 'submit',
			'decorators' => array('ViewHelper', ),
		));
		$onclick = 'parent.Smoothbox.close();';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$onclick = '';
		}
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'prependText' => ' or ',
			'link' => true,
			'href' => '',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper', ),
		));

		$this -> addDisplayGroup(array(
			'submit',
			'cancel'
		), 'buttons');
	}

}
?>
