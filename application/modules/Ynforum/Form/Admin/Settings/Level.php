<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Level.php 7514 2010-10-01 02:53:55Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        parent::init();

        // My stuff
        $this->setTitle('Member Level Settings')->setDescription('YNFORUM_FORM_ADMIN_LEVEL_DESCRIPTION');

        // Element: view
        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Forums?',
            'description' => 'YNFORUM_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
            'value' => 1,
            'multiOptions' => array(
                2 => 'Yes, allow viewing and subscription of forums, even private ones.',
                1 => 'Yes, allow viewing and subscription of forums.',
                0 => 'No, do not allow forums to be viewed or subscribed to.',
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if (!$this->isModerator()) {
            unset($this->view->options[2]);
        }

        if (!$this->isPublic()) {
            // Element: topic_create
            $this->addElement('Radio', 'topic_create', array(
                'label' => 'Allow Creation of Topics?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_TOPIC_CREATE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow creation of topics in forums, even private ones.',
                    1 => 'Yes, allow creation of topics.',
                    0 => 'No, do not allow topics to be created.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->topic_create->options[2]);
            }
            
            // Element: post_create
            $this->addElement('Radio', 'post_create', array(
                'label' => 'Allow Posting?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_POST_CREATE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow posting to forums, even private ones.',
                    1 => 'Yes, allow posting to forums.',
                    0 => 'No, do not allow posting to forums.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->post_create->options[2]);
            }

            // Element: post_edit
            $this->addElement('Radio', 'post_edit', array(
                'label' => 'Allow Editing of Posts?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_POST_EDIT_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow editing of posts, including other members\' posts.',
                    1 => 'Yes, allow editing of posts.',
                    0 => 'No, do not allow forum posts to be edited.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->post_edit->options[2]);
            }
            
            // Element: post_edit
            $this->addElement('Radio', 'post_delete', array(
                'label' => 'Allow Deletion of Posts?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_POST_DELETE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow deletion of posts, including other members\' posts.',
                    1 => 'Yes, allow deletion of posts.',
                    0 => 'No, do not allow forum posts to be deleted.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->post_delete->options[2]);
            }						
			
			// Element: Event create, edit, delete
            $this->addElement('Radio', 'fevent_create', array(
                'label' => 'Allow Creation of Event in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_EVENT_CREATE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow creation of events in forums, event private ones.',
                    1 => 'Yes, allow creation of events.',
                    0 => 'No, do not allow events to be created.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fevent_create->options[2]);
            }
			$this->addElement('Radio', 'fevent_edit', array(
                'label' => 'Allow Edition of Event in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_EVENT_EDIT_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow edition of events in forums, event private ones.',
                    1 => 'Yes, allow edition of events.',
                    0 => 'No, do not allow events to be edited.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fevent_edit->options[2]);
            }
			$this->addElement('Radio', 'fevent_delete', array(
                'label' => 'Allow Deletion of Event in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_EVENT_DELETE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow deletion of events in forums, event private ones.',
                    1 => 'Yes, allow deletion of events.',
                    0 => 'No, do not allow events to be deleted.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fevent_delete->options[2]);
            }
			
			if ($this->isModerator()) {	
				 $this->addElement('Radio', 'fevent_hlight', array(
			            'label' => "Allow highlight of event.",
			            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_EVENT_HIGHLIGHT_DESCRIPTION',
			            'value' => 1,
			            'multiOptions' => array(
			                1 => "Yes, allow highlight of event.",
			                0 => 'No, do not allow highlight of event.',
			            ),
				));
			}
			// Element: Group create, edit, delete
            $this->addElement('Radio', 'fgroup_create', array(
                'label' => 'Allow Creation of Group in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_GROUP_CREATE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow creation of groups in forums, group private ones.',
                    1 => 'Yes, allow creation of groups.',
                    0 => 'No, do not allow groups to be created.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fgroup_create->options[2]);
            }
			
			$this->addElement('Radio', 'fgroup_edit', array(
                'label' => 'Allow Edition of Group in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_GROUP_EDIT_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow edition of groups in forums, group private ones.',
                    1 => 'Yes, allow edition of groups.',
                    0 => 'No, do not allow groups to be edited.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fgroup_edit->options[2]);
            }
			
			$this->addElement('Radio', 'fgroup_delete', array(
                'label' => 'Allow Deletion of Group in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_GROUP_DELETE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow deletion of groups in forums, group private ones.',
                    1 => 'Yes, allow deletion of groups.',
                    0 => 'No, do not allow groups to be deleted.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fgroup_delete->options[2]);
            }
			if ($this->isModerator()) {
				$this->addElement('Radio', 'fgroup_hlight', array(
		            'label' => "Allow highlight of group.",
		            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_GROUP_HIGHLIGHT_DESCRIPTION',
		            'value' => 1,
		            'multiOptions' => array(
		                1 => "Yes, allow highlight of group.",
		                0 => 'No, do not allow highlight of group.',
		            ),
		        ));
			}
			
			// Element: Poll create, edit, delete
            $this->addElement('Radio', 'fpoll_create', array(
                'label' => 'Allow Creation of Poll in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_POLL_CREATE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow creation of polls in forums, poll private ones.',
                    1 => 'Yes, allow creation of polls.',
                    0 => 'No, do not allow polls to be created.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fpoll_create->options[2]);
            }
			$this->addElement('Radio', 'fpoll_edit', array(
                'label' => 'Allow Edition of Poll in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_POLL_EDIT_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow edition of polls in forums, poll private ones.',
                    1 => 'Yes, allow edition of polls.',
                    0 => 'No, do not allow polls to be edited.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fpoll_edit->options[2]);
            }
			$this->addElement('Radio', 'fpoll_delete', array(
                'label' => 'Allow Deletion of Poll in Forum?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_POLL_DELETE_DESCRIPTION',
                'multiOptions' => array(
                    2 => 'Yes, allow deletion of polls in forums, poll private ones.',
                    1 => 'Yes, allow deletion of polls.',
                    0 => 'No, do not allow polls to be deleted.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->fpoll_delete->options[2]);
            }				
			
			if ($this->isModerator()) {
				$this->addElement('Radio', 'fpoll_hlight', array(
		            'label' => "Allow highlight of poll.",
		            'description' => 'YNFORUM_FORM_ADMIN_ALLOW_POLL_HIGHLIGHT_DESCRIPTION',
		            'value' => 1,
		            'multiOptions' => array(
		                1 => "Yes, allow highlight of poll.",
		                0 => 'No, do not allow highlight of poll.',
		            ),
		        ));
			}
            // Element: commentHtml
            $this->addElement('Text', 'commentHtml', array(
                'label' => 'Allow HTML in posts?',
                'description' => 'YNFORUM_FORM_ADMIN_LEVEL_CONTENTHTML_DESCRIPTION',
            ));			

            if ($this->isModerator()) {
				
                $this->addElement('Radio', 'yntopic_edit', array(
                    'label' => 'Allow editing of other members\' topics',
                    'description' => 'YNFORUM_FORM_ADMIN_ALLOW_EDITING_OTHER_TOPIC_DESCRIPTION',
                    'value' => 0,
                    'multiOptions' => array(
                        1 => 'Yes, allow editing other members\' topics.',
                        0 => 'No, do not allow topics to be edited.',
                    ),
                ));

                $this->addElement('Radio', 'yntopic_delete', array(
                    'label' => 'Allow deleting of other members\' topics',
                    'description' => 'YNFORUM_FORM_ADMIN_ALLOW_DELETING_OTHER_TOPIC_DESCRIPTION',
                    'value' => 0,
                    'multiOptions' => array(
                        1 => 'Yes, allow deleting other members\' topics.',
                        0 => 'No, do not allow topics to be deleted.',
                    ),
                ));

                $this->addElement('Radio', 'yntopic_approve', array(
                    'label' => 'Allow approval of others\' topics',
                    'description' => 'YNFORUM_FORM_ADMIN_ALLOW_APPROVAL_OTHER_TOPIC_DESCRIPTION',
                    'value' => 0,
                    'multiOptions' => array(
                        1 => 'Yes, allow approval other members\' topics.',
                        0 => 'No, do not allow topics to be approved.',
                    ),
                ));

                $this->addElement('Radio', 'yntopic_sticky', array(
                    'label' => 'Allow sticky make of other members\' topics',
                    'description' => 'YNFORUM_FORM_ADMIN_ALLOW_STICKY_OTHER_TOPIC_DESCRIPTION',
                    'value' => 0,
                    'multiOptions' => array(
                        1 => 'Yes, allow sticky make of topics.',
                        0 => 'No, do not allow topics to be made sticky.',
                    ),
                ));

                $this->addElement('Radio', 'yntopic_close', array(
                    'label' => 'Allow close of other members\' topics',
                    'description' => 'YNFORUM_FORM_ADMIN_ALLOW_CLOSE_OTHER_TOPIC_DESCRIPTION',
                    'value' => 0,
                    'multiOptions' => array(
                        1 => 'Yes, allow close of topics.',
                        0 => 'No, do not allow topics to be closed.',
                    ),
                ));

                $this->addElement('Radio', 'yntopic_move', array(
                    'label' => 'Allow move of other members\' topics',
                    'description' => 'YNFORUM_FORM_ADMIN_ALLOW_MOVE_OTHER_TOPIC_DESCRIPTION',
                    'value' => 0,
                    'multiOptions' => array(
                        1 => 'Yes, allow move of topics.',
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
            }
        }
    }
}