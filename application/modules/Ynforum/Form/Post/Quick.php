<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Quick.php 8787 2011-04-05 02:23:49Z alex $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Post_Quick extends Engine_Form {
    private $_forum;
    
    public function __construct($options = null) {
        if (is_array($options) && array_key_exists('forum', $options)) {
            $this->_forum = $options['forum'];
            unset($options['forum']);
        }

        parent::__construct($options);
    }
    
    public function init() {
        $this->setAttrib('name', 'forum_post_quick')->setAttrib('class', '');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();

        $filter = new Engine_Filter_Html();
        $allowed_tags = explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'forum', 'commentHtml'));

        if ($settings->getSetting('forum_html', 0) == '0') {
            $filter->setForbiddenTags();
            $filter->setAllowedTags($allowed_tags);
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $allowHtml = (bool) $settings->getSetting('forum_html', 0);
        $allowBbcode = (bool) $settings->getSetting('forum_bbcode', 0);

        if (!$allowHtml) {
            $filter = new Engine_Filter_HtmlSpecialChars();
        } else {
            $filter = new Engine_Filter_Html();
            $filter->setForbiddenTags();
            $allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'forum', 'commentHtml')));
            $filter->setAllowedTags($allowed_tags);
        }
        
        if ($allowHtml || $allowBbcode) {
            $this->addElement('TinyMce', 'body', array(
                'disableLoadDefaultDecorators' => true,
                'required' => true,
                'editorOptions' => array(
					'bbcode' => $settings -> getSetting('forum_bbcode', 0),
					'html' => $settings -> getSetting('forum_html', 0),
					 'plugins' => array(
							'table', 'fullscreen', 'media', 'preview', 'paste',
							'code', 'image', 'textcolor', 'jbimages', 'link'
					 ),
					'toolbar1' => array(
					  'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
					  'image', '|', 'link', '|', 'jbimages', '|', 'fullscreen', '|', 'preview'
					),  
					'width' => '100%',
					'upload_url' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('forum_id' => $this -> _forum -> getIdentity()), 'ynforum_upload_photo', true)
				),
                'allowEmpty' => false,
                'decorators' => array('ViewHelper'),
                'filters' => array(
                    $filter,
                    new Engine_Filter_Censor(),
                )
            ));
        } else {
            $this->addElement('textarea', 'body', array(
                'required' => true,
                'allowEmpty' => false,
                'attribs' => array(
                    'rows' => 24,
                    'cols' => 80,
                    'style' => 'width:553px; max-width:553px; height:158px;'
                ),
                'filters' => array(
                    $filter,
                    new Engine_Filter_Censor(),
                ),
            ));
        }

        // Element: photo
        // Need this hack for some reason
        $this->addElement('File', 'photo', array(
            'attribs' => array('style' => 'display:none;')
        ));

		 // Need this hack for some reason
        $this->addElement('File', 'attach', array(
            'attribs' => array('style' => 'display:none;')
        ));
		
        // Element: watch
        $this->addElement('Checkbox', 'watch', array(
            'label' => 'Send me notifications when other members reply to this topic.',
            'value' => '0',
        ));

        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Post Reply',
            'type' => 'submit',
        ));
    }

}