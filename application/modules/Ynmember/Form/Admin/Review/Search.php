<?php
class Ynmember_Form_Admin_Review_Search extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form',
            'method'=>'GET',
        ));
        
        $this->addElement('Text', 'reviewer_name', array(
            'label' => 'Review by',
        ));
		
		$this->addElement('Text', 'review_for', array(
            'label' => 'Review for',
        ));
		
		$this->addElement('Text', 'title', array(
            'label' => 'Review Title',
        ));
        
        $this->addElement('Text', 'from_date', array(
            'label' => 'From Date',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Text', 'to_date', array(
            'label' => 'To Date',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->submit->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}