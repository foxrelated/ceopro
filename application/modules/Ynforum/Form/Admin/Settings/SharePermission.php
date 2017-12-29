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
class Ynforum_Form_Admin_Settings_SharePermission extends Engine_Form {
    private $_moderators;
    
    public function __construct($options = null) {        
        if (is_array($options) && array_key_exists('moderators', $options)) {
            $this->_moderators = $options['moderators'];
            unset ($options['moderators']);
        }
        parent::__construct($options);
    }
    public function init() {
        parent::init();
        
        $this->setTitle('Share Permission Settings')
                ->setDescription('YNFORUM_FORM_ADMIN_SHARE_PERMISSION_DESCRIPTION')
                ->setAttrib('id', 'form-share-permission');
        
        // Element: level_id
        $this->addElement('Select', 'moderator', array(
            'label' => 'Moderator',            
            'onchange' => 'javascript:reloadPermission(this.value);',
            'ignore' => true,
        ));
        
        if ($this->_moderators) {
            foreach($this->_moderators as $moderator) {
                $this->moderator->addMultiOption($moderator->getIdentity(), $moderator->getTitle());
            }
            $defaultModerator = $this->_moderators->getRow(0, true);
            $this->moderator->setValue($defaultModerator->getIdentity());
        }
        
        $this->addElement('Radio', 'yntopic_edit', array(
            'label' => "Allow editing of other members' topics/posts",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_EDITING_OTHER_TOPIC_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow editing other members' topics.",
                0 => 'No, do not allow topics to be edited.',
            ),
        ));
        
        $this->addElement('Radio', 'yntopic_delete', array(
            'label' => "Allow deleting of other members' topics/posts",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_DELETING_OTHER_TOPIC_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow deleting other members' topics.",
                0 => 'No, do not allow topics to be deleted.',
            ),
        ));
        
        $this->addElement('Radio', 'yntopic_approve', array(
            'label' => "Allow approval of others' topics",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_APPROVAL_OTHER_TOPIC_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow approval other members' topics.",
                0 => 'No, do not allow topics to be approved.',
            ),
        ));
        
        $this->addElement('Radio', 'yntopic_sticky', array(
            'label' => "Allow sticky make of other members' topics",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_STICKY_OTHER_TOPIC_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow sticky make of topics.",
                0 => 'No, do not allow topics to be made sticky.',
            ),
        ));
        
        $this->addElement('Radio', 'yntopic_close', array(
            'label' => "Allow close of other members' topics",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_CLOSE_OTHER_TOPIC_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow close of topics.",
                0 => 'No, do not allow topics to be closed.',
            ),
        ));
        
        $this->addElement('Radio', 'yntopic_move', array(
            'label' => "Allow move of other members' topics",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_MOVE_OTHER_TOPIC_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow move of topics.",
                0 => 'No, do not allow topics to be moved.',
            ),
        ));
        
        $this->addElement('Radio', 'ynannoun_create', array(
            'label' => "Allow create of announcement.",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_ANNOUNCEMENT_CREATE_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow create of announcement.",
                0 => 'No, do not allow create of announcement.',
            ),
        ));
		$this->addElement('Radio', 'ynannoun_edit', array(
            'label' => "Allow edit of announcement.",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_ANNOUNCEMENT_EDIT_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow edit of announcement.",
                0 => 'No, do not allow edit of announcement.',
            ),
        ));
		$this->addElement('Radio', 'ynannoun_delete', array(
            'label' => "Allow delete of announcement.",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_ANNOUNCEMENT_DELETE_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow delete of announcement.",
                0 => 'No, do not allow delete of announcement.',
            ),
        ));
		$this->addElement('Radio', 'ynannoun_hlight', array(
            'label' => "Allow highlight of announcement.",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_ANNOUNCEMENT_HIGHLIGHT_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow highlight of announcement.",
                0 => 'No, do not allow highlight of announcement.',
            ),
        ));
		
		$this->addElement('Radio', 'fevent_edit', array(
            'label' => "Allow editing of other members' events",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_EDITING_OTHER_EVENT_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow editing other members' events.",
                0 => 'No, do not allow events to be edited.',
            ),
        ));
        $this->addElement('Radio', 'fevent_delete', array(
            'label' => "Allow deleting of other members' events",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_DELETING_OTHER_EVENT_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow deleting other members' events.",
                0 => 'No, do not allow events to be deleted.',
            ),
        ));
        $this->addElement('Radio', 'fevent_hlight', array(
            'label' => "Allow highlight of event.",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_EVENT_HIGHLIGHT_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow highlight of event.",
                0 => 'No, do not allow highlight of event.',
            ),
        ));
        
		$this->addElement('Radio', 'fgroup_edit', array(
            'label' => "Allow editing of other members' groups",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_EDITING_OTHER_GROUP_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow editing other members' groups.",
                0 => 'No, do not allow groups to be edited.',
            ),
        ));
        $this->addElement('Radio', 'fgroup_delete', array(
            'label' => "Allow deleting of other members' groups",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_DELETING_OTHER_GROUP_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow deleting other members' groups.",
                0 => 'No, do not allow groups to be deleted.',
            ),
        ));
        $this->addElement('Radio', 'fgroup_hlight', array(
            'label' => "Allow highlight of group.",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_GROUP_HIGHLIGHT_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow highlight of group.",
                0 => 'No, do not allow highlight of group.',
            ),
        ));
		
		$this->addElement('Radio', 'fpoll_edit', array(
            'label' => "Allow editing of other members' polls",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_EDITING_OTHER_POLL_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow editing other members' polls.",
                0 => 'No, do not allow polls to be edited.',
            ),
        ));
        $this->addElement('Radio', 'fpoll_delete', array(
            'label' => "Allow deleting of other members' polls",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_DELETING_OTHER_POLL_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow deleting other members' polls.",
                0 => 'No, do not allow polls to be deleted.',
            ),
        ));
        $this->addElement('Radio', 'fpoll_hlight', array(
            'label' => "Allow highlight of poll.",
            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_POLL_HIGHLIGHT_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                1 => "Yes, allow highlight of poll.",
                0 => 'No, do not allow highlight of poll.',
            ),
        ));
        
         // Element: submit
        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
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
        ));              
    }
}