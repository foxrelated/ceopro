<?php
class Ynmultilisting_Form_Import_ModuleSearch extends Engine_Form {
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

        //Feature Filter
        $this->addElement('Text', 'item_title', array(
            'label' => 'Search Title'
        ));
        
        $this->addElement('Select', 'imported', array(
            'label' => 'Imported',
            'multiOptions' => array(
                ''   => '',
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'value' => ''
        ));
        
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->addElement('Hidden', 'category_id', array(
            'order' => 101,
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'module_id', array(
            'order' => 102,
        ));
        
        $this->search->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}