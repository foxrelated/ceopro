<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Create.php 8794 2011-04-06 00:08:11Z alex $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Post_AddReputation extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->setTitle('Add Reputation');
        $this->setAttrib('id', 'add-reputation-form');
        $this->setAttrib('class', 'global_form_popup add_reputation_form_popup');
        $this->addElement('Radio', 'reputation', array(
            'label' => "Increase/decrease reputation points for this user",
            'description' => 'Do you want to add reputation for this user by this post?',
            'value' => 1,
            'multiOptions' => array(
                1 => "Increase one point for this user.",
                0 => 'Decrease one point for this user.',
            ),
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Increase/Decrease',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper'
            ),            
            'onclick' => 'parent.Smoothbox.close();',
        ));
    }
}