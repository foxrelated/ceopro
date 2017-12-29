<?php
class Ynforum_Form_User_Signature extends Engine_Form
{
	public function init()
	{
		$filter = new Engine_Filter_Html();
		$allowTags = explode(', ', 'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr');
		$filter->setAllowedTags($allowTags);

		$this->setMethod("POST");

		$this->addElement('TinyMce', 'body', array(
			'disableLoadDefaultDecorators' => true,
			'required'                     => false,
			'editorOptions'                => array(
				'bbcode'                  => true,
				'html'                    => true,
				'theme_advanced_buttons1' => array(
					'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|', 'media', 'image', 'fullscreen', 'preview', 'emotions'
				),
				'theme_advanced_buttons2' => array(
					'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
					'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
					'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
				),
				'width'                   => '100%',
			),
			'allowEmpty'                   => true,
			'decorators'                   => array('ViewHelper'),
			'filters'                      => array(
				$filter,
				new Engine_Filter_Censor(),
			),
		));

		// Buttons
		$this->addElement('Button', 'submit', array(
			'label'      => 'Save Changes',
			'type'       => 'submit',
			'ignore'     => true,
			'decorators' => array(
                array('HtmlTag2', array('class' => 'ynforum-spaces')),
				array('ViewHelper', array('class' => 'buttons'))
			)));
	}
}