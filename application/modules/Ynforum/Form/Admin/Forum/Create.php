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
class Ynforum_Form_Admin_Forum_Create extends Ynforum_Form_Forum_Create {

    public function init() {
        parent::init();

        // Element: levels
        $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
        $multiOptions = array();
        foreach ($levels as $level) {
            $multiOptions[$level->getIdentity()] = $level->getTitle();
        }
        reset($multiOptions);
        $this->addElement('Multiselect', 'levels', array(
            'label' => 'Who can view this forum',
            'order' => 5,
            'multiOptions' => $multiOptions,
            'value' => array_keys($multiOptions),
            'required' => true,
            'allowEmpty' => false,
        ));
		// START - LEVEL FORUM'S EVENT //
		$this->addElement('Multiselect', 'forum_events', array(
            'label' => "Who can create, edit and delete own forum's events?",
            'order' => 6,
            'multiOptions' => $multiOptions,
            'value' => array_keys($multiOptions),
            'required' => true,
            'allowEmpty' => false,
        ));
		
		$this->addElement('Multiselect', 'forum_groups', array(
            'label' => "Who can create, edit and delete own forum's groups?",
            'order' => 7,
            'multiOptions' => $multiOptions,
            'value' => array_keys($multiOptions),
            'required' => true,
            'allowEmpty' => false,
        ));
		
		$this->addElement('Multiselect', 'forum_polls', array(
            'label' => "Who can create, edit and delete own forum's polls?",
            'order' => 8,
            'multiOptions' => $multiOptions,
            'value' => array_keys($multiOptions),
            'required' => true,
            'allowEmpty' => false,
        ));
        // END - LEVEL FORUM'S EVENT //
		
    }

}