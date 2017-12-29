<?php
class Ynmobileview_Form_Admin_EditStyle extends Engine_Form
{
	public function init()
	{
		$view = Zend_Registry::get('Zend_View');
		$view -> headScript() -> appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynmobileview/externals/scripts/jscolor.js');
		$this -> setAttrib('style', "width: 600px");
		$this -> setTitle("Edit Custom Style");

		$this -> addElement('Text', 'title', array(
			'label' => 'Name',
			'required' => true,
			'allowEmpty' => false,
		));

		$this -> addElement('dummy', 'head_detail', array('label' => 'Custom CSS details', ));
		
		$this -> addElement('Text', 'css_main_background', array(
			'label' => 'Main Background',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "e5e5e5",
		));
		$this -> addElement('Text', 'css_header_background', array(
			'label' => 'Header Background',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "01a0db",
		));
		$this -> addElement('Text', 'css_header_border_color', array(
			'label' => 'Header Border Color',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "0c6f96",
		));
		$this -> addElement('Text', 'css_left_menu_background', array(
			'label' => 'Left Menu Background',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "1c1c1c",
		));
		$this -> addElement('Text', 'css_left_menu_background_active', array(
			'label' => 'Left Menu Background (active)',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "1c1c1c",
		));	

		$this -> addElement('Text', 'css_menu_text_color', array(
			'label' => 'Text Menu Color',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "c4ccda",
		));

		$this -> addElement('Text', 'css_username_color', array(
			'label' => "Username's Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "c4ccda",
		));
		
		$this -> addElement('Text', 'css_top_menu_background', array(
			'label' => 'Top Menu Background',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "fafafa",
		));
		$this -> addElement('Text', 'css_top_menu_background_active', array(
			'label' => 'Top Menu Background (active)',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "ffffff",
		));
		
		$this -> addElement('Text', 'css_top_menu_text_color', array(
			'label' => 'Text Top Menu Color',
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "6d6e71",
		));

		$this -> addElement('Text', 'css_content_background', array(
			'label' => "Content's Background",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "ffffff",
		));

		$this -> addElement('Text', 'css_title_color', array(
			'label' => "Title's Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "eef2f3",
		));
		$this -> addElement('Text', 'css_feed_background', array(
			'label' => "Feed's Background",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "e5e5e5",
		));
		$this -> addElement('Text', 'css_item_background', array(
			'label' => "Item Background",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "ffffff",
		));
		$this -> addElement('Text', 'css_text_color', array(
			'label' => "Text's Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "555555",
		));
		$this -> addElement('Text', 'css_link_background', array(
			'label' => "Link's Background (like,comment,share)",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "FFFFFF",
		));
		$this -> addElement('Text', 'css_text_link_color', array(
			'label' => "Text Link's Color (like,comment,share)",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "5e5e5e",
		));
		$this -> addElement('Text', 'css_submit_button_color', array(
			'label' => "Submit Button Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "01a0db",
		));
		
		$this -> addElement('Text', 'css_submit_button_border_color', array(
			'label' => "Submit Button Border Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "01a0db",
		));
		$this -> addElement('Text', 'css_login_background', array(
			'label' => "Login Page Background",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "e5e5e5",
		));
		$this -> addElement('Text', 'css_login_button_color', array(
			'label' => "Login Button Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "01A0DB",
		));
		$this -> addElement('Text', 'css_login_button_border_color', array(
			'label' => "Login Button Border Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "01A0DB",
		));
		$this -> addElement('Text', 'css_login_text_color', array(
			'label' => "Login Button Text Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "ffffff",
		));
		$this -> addElement('Text', 'css_signup_button_color', array(
			'label' => "Signup Button Color (from)",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "4a8532",
		));
		
		$this -> addElement('Text', 'css_signup_button_border_color', array(
			'label' => "Signup Button Border Color",
			'class' => 'color',
			'allowEmpty' => false,
			'value' => "4a8532 ",
		));
		// Add submit button
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
		));
	}

}
