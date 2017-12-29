<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ultimatenews
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Level.php 6858 2010-07-27 01:16:32Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Ultimatenews
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ultimatenews_Form_Admin_Manage_Createcategory extends Engine_Form
{

    public function init()
    {
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
        $this   ->setAttribs(array(
            'id' => 'admin_createcategory_form',
        ));

        //init category name
        $this->addElement('Text', 'category_name', array(
            'label' => 'Category Name',
            'required' => true,
            'allowEmpty' => false,
            'style' => 'width:370px;',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags'
            )
        ));

        $this->addElement('Textarea', 'category_description', array(
            'label' => 'Category Description',
            'style' => 'width:370px;',
            'filters'=>array(new Engine_Filter_Censor(),'StringTrim', 'StripTags'),
        ));

        $this->addElement('Checkbox', 'is_active', array(
            'label' => "Active Category?",
            'value' => 1,
            'checked' => true,
        ));

        // Element: order
        $this->addElement('Hidden', 'order', array(
            'order' => 10004,
        ));

        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 10005,
        ));

        // Buttons
        $this->addElement('Button', 'add', array(
            'label' => 'Save Category',
            'type' => 'submit',
            'ignore' => true,
            'value' => 'submit',
            'style'=>'border:none;margin-top:10px',
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick'=> 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            )
        ));
        $this->addDisplayGroup(array('add', 'cancel'), 'buttons');
    }

}