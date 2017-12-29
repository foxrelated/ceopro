<?php
class Ynforum_Form_Admin_Report_Filter extends Engine_Form {
	public function init() {

		$this -> clearDecorators() -> addDecorator('FormElements') -> addDecorator('Form') -> addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search')) -> addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear')) -> setAttribs(array('id' => 'filter_form', ));
		$this -> addElement('Text', 'description', array('label' => 'Description', ));
		$this->addElement('Select', 'category', array(
	      'label' => 'Reason',
	      'allowEmpty' => false,
	      'multiOptions' => array(
	        '' => 'All',
	        'spam' => 'Spam',
	        'abuse' => 'Abuse',
	        'inappropriate' => 'Inappropriate Content',
	        'licensed' => 'Licensed Material',
	        'other' => 'Other',
	      ),
	    ));
		
		$date_validate = new Zend_Validate_Date("YYYY-MM-dd");
		$date_validate -> setMessage("Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT);

		$this -> addElement('Text', 'start_date', array('label' => 'Date From', 'required' => false, ));
		$this -> getElement('start_date') -> addValidator($date_validate);

		$this -> addElement('Text', 'end_date', array('label' => 'Date To', 'required' => false, ));
		$this -> getElement('end_date') -> addValidator($date_validate);

		$this -> addElement('Hidden', 'order', array('order' => 10004, ));
		// Element: direction
		$this -> addElement('Hidden', 'direction', array('order' => 10005, ));

		$this -> addElement('Button', 'search', array('label' => 'Search', 'type' => 'submit', 'ignore' => true, ));
	}

}
