<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Delete.php 7481 2010-09-27 08:41:01Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Forum_Watch extends Engine_Form {
    private $_watch = false;
    
    public function __construct($options = null) {
        if (is_array($options) && array_key_exists('watch', $options)) {
            $this->_watch = $options['watch'];
            unset($options['watch']);
        }

        parent::__construct($options);
    }

    public function init() {
        if ($this->_watch) {
            $title = 'Watch Forum';
            $description = 'Do you want to watch this forum ?';
            $label = 'Watch all sub forums in this forum';
			$button = 'Watch Forum';
        } else {
            $title = 'Stop Watching Forum';
            $description = 'Do you want to stop watching this forum ?';
            $label = 'Stop watching all sub forums in this forum';
			$button = 'Stop Watching Forum';
        }
        $this->setTitle($title)->setDescription($description);
        
        $this->addElement('Checkbox', 'watch_sub_forum', array(
            'label' => $label,
            'value' => '0',
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => $button,
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }
}