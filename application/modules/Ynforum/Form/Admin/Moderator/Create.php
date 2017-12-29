<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Create.php 8221 2011-01-15 00:24:02Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Admin_Moderator_Create extends Engine_Form {
    private $_type = 'forum';
    
    public function __construct($options = null) {
        if ($options && is_array($options) && array_key_exists('form_type', $options)) {
            $this->_type = $options['form_type'];
            unset ($options['form_type']);
        }
        parent::__construct($options);
    }
    
    public function init() {
        $this->setTitle('Add Moderator')
                ->setDescription("Search for a member to add as a moderator for this $this->_type.")
                ->setAttrib('id', 'forum_form_admin_moderator_create')
                ->setAttrib('class', 'global_form_popup')
        ;

        $this->addElement('Text', 'username', array(
            'label' => 'Member Name'
        ));
        
        $this->addElement('Checkbox', 'add_moderator', array(
            'label' => 'Add the moderator to its sub-forums/sub-categories.',
            'value' => '0',
        ));

        $this->addElement('Hidden', 'user_id', array(
            'label' => 'User Identity',
            'required' => true,
            'allowEmpty' => false,
        ));

        // Buttons
        $this->addElement('Button', 'execute', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
        //$button_group->addDecorator('DivDivDivWrapper');
    }
}