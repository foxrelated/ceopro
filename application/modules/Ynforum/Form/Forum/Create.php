<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */

class Ynforum_Form_Forum_Create extends Engine_Form {
    protected $_orderForums;
    
    public function getInputedValues() {
        $values = $this->getValues();
        $values['title'] = htmlspecialchars($values['title']);
        $values['description'] = htmlspecialchars($values['description']);
        if ($values['parent_forum_id'] == '') {
            $values['parent_forum_id'] = null;
        }
        return $values;
    }
    
    protected function _fillDataInForm() {
        $categories = Engine_Api::_()->getItemTable('ynforum_category')->getCategoriesOrderByLevel();
        $forumTable = Engine_Api::_()->getItemTable('ynforum_forum');
        $this->_orderForums = $forumTable->fetchAllAndOrderByHierachy();
        $parent_category_forum = $this->getElement('parent_category_forum');
        foreach($categories as $category) {
            $parent_category_forum->addMultiOption('category_id=' . $category->getIdentity(), 
                    str_repeat('--', $category->level) . $category->title);
            if (array_key_exists($category->getIdentity(), $this->_orderForums)) {
                foreach ($this->_orderForums[$category->getIdentity()] as $forum) {
                    $parent_category_forum->addMultiOption('forum_id=' . $forum->getIdentity(),
                        str_repeat('--', $category->level + $forum->level + 1) . 'Forum::' . $forum->title);
                }
            }
        }
    }
    
    public function init() {
        $this->setTitle('Create Forum');

        // Element: title
        $this->addElement('Text', 'title', array(
            'label' => 'Forum Title',
            'order' => 1,
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('StringLength', true, array(1, 64)),
            ),
        ));

        // Element: description
        $this->addElement('Text', 'description', array(
            'label' => 'Forum Description',
            'order' => 2,
            'style' => 'width:98%',
        ));
        
        $parent_category_forum = new Engine_Form_Element_Select('parent_category_forum', array(
            'label' => 'Parent Category/Forum',
            'order' => 4,
        ));
         $this->addElement($parent_category_forum);
        
        // Icon
        $this->addElement('File', 'icon', array('label' => 'Attach an icon (The icon will be resized to 56px * 41px)', 'size' => '40', 'order' => 19));

        // Element: submit
        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'order' => 20,
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
            'order' => 21,
        ));
        
        $this->addDisplayGroup(array(
            'execute',
            'cancel'
                ), 'buttons', array(
            'order' => 22,
        ));
        
        $this->_fillDataInForm();
    }
}