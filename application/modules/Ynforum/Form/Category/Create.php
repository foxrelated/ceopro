<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Create.php 7481 2010-09-27 08:41:01Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Category_Create extends Engine_Form {  
    protected $_category;
    protected $_orderCategories;
    
    public function __construct($options = null) {      
        if (is_array($options) && array_key_exists('category', $options)) {
            $this->_category = $options['category'];
            unset ($options['category']);
        }
        
        parent::__construct($options);
    }
    
    public function getInputedValues() {
        $values = $this->getValues();
        $values['title'] = htmlspecialchars($values['title']);
        $values['description'] = htmlspecialchars($values['description']);
        if ($values['parent_category_id'] == '') {
            $values['parent_category_id'] = null;
        }
        return $values;
    }
    
    public function init() {
        $this->setTitle('Create Category');

        // Element: title
        $this->addElement('Text', 'title', array(
            'label' => 'Category Title',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('StringLength', true, array(1, 64)),
            ),
        ));
		$this->getElement('title')->setAttrib('required', true);
		
        // Element: description
        $this->addElement('Text', 'description', array('label' => 'Description'));
        // Element : dropdown list parent category
        $this->addElement('Select', 'parent_category_id', array('label' => 'Parent Category'));
        
        // Icon
        $this->addElement('File', 'icon', array(
            'label' => 'Attach an icon (The icon will be resized to 48px * 48px)', 
            'size' => '40',
            'accept' => 'image/*'));
        $this->getElement('icon')->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        //$this->getElement('icon')->setAttrib('required', true);
        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');               
        
        $this->_fillDataInForm();
    }
    
    /**
     * fill in the category list to the category combo box
     */
    protected function _fillDataInForm() {
        $this->_orderCategories = Engine_Api::_()->getItemTable('ynforum_category')->getCategoriesOrderByLevel();
        $this->parent_category_id->addMultiOption('', '');
        foreach ($this->_orderCategories as $category) {
            $this->parent_category_id->addMultiOption($category->getIdentity(), str_repeat('--', $category->level) . $category->title);
        }
        
        if ($this->_category) {
            $this->title->setValue($this->_category->title);
            $this->description->setValue($this->_category->description);
            $this->parent_category_id->setValue($this->_category->parent_category_id);            
        }
    }
}