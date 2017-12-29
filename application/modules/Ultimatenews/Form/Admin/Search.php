<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Ultimate News
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Search.php
 * @author     Minh Nguyen
 */
class Ultimatenews_Form_Admin_Search extends Engine_Form {

  private $_enableDate;
  
  public function setEnableDate($value) {
  	$this->_enableDate = $value;
  }
  public function init() {
  	
    $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;
     $this   ->setAttribs(array(
                'id' => 'admin_filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
			
    $title = new Zend_Form_Element_Text('title');
    $title   ->setLabel('Title')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
           ;
    $this->addElement($title);
	 
	if (isset($this->_enableDate)){
		// Start date
	    $startdate = new Engine_Form_Element_Date('start_date');
	    $startdate->setLabel("Start Date");
	    $startdate->setAttrib('style', "width:70px;margin-bottom:5px;");
	    $this->addElement($startdate);
		
	    // End time
	    $enddate = new Engine_Form_Element_Date('end_date');
	    $enddate->setLabel("End Date");
	    $enddate->setAttrib('style', "width:70px;margin-bottom:5px;");
	    $this->addElement($enddate);
	}
	
    $submit = new Zend_Form_Element_Button('search_all', array('type' => 'submit'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
     // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));
    $this->addElements(array(
        $submit
    ));

  }

}
