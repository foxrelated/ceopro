<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
class Ynforum_Form_Topic_Create extends Engine_Form
{
	private $_forum;
	private $_icon_id;
	public function __construct($options = null)
	{
		if (is_array($options) && array_key_exists('forum', $options))
		{
			$this -> _forum = $options['forum'];
			unset($options['forum']);
		}
		
		if (is_array($options) && array_key_exists('icon_id', $options))
		{
			$this -> _icon_id = $options['icon_id'];
			unset($options['icon_id']);
		}

		parent::__construct($options);
	}

	public function init()
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		$this -> setAttrib('id', 'forum_post_create') -> setAttrib('name', 'forum_post_create') -> setAttrib('enctype', 'multipart/form-data') -> setAttrib('class', 'global_form form_forum') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array())) -> setMethod("POST");

		$this -> addElement('Text', 'title', array(
			'label' => 'Topic title',
			'allowEmpty' => false,			
			'filters' => array(new Engine_Filter_Censor(), ),
			'validators' => array( array(
					'StringLength',
					true,
					array(
						1,
						64
					)
				), ),
		));						
				
		// LuanND START //			
		$this->addElement('Dummy', 'icon_id', array(
  		  'label' => 'Save Changes',	
  		  'icon_id' => $this->_icon_id,
	      'decorators' => array(		          
		          array('ViewScript',array(
		                'viewScript' => '_topic_icon.tpl',
		                'class'      => 'form element'
		          ))
		      ),
		));
		// LuanND END //
		
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$allowHtml = (bool)$settings -> getSetting('forum_html', 0);
		$allowBbcode = (bool)$settings -> getSetting('forum_bbcode', 0);

		if (!$allowHtml)
		{
			$filter = new Engine_Filter_HtmlSpecialChars();
		}
		else
		{
			$filter = new Engine_Filter_Html();
			$filter -> setForbiddenTags();
			$allowed_tags = array_map('trim', explode(',', Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'forum', 'commentHtml')));
			$filter -> setAllowedTags($allowed_tags);
		}

		if ($allowHtml || $allowBbcode)
		{
			$this -> addElement('TinyMce', 'body', array(
				'disableLoadDefaultDecorators' => true,
				'editorOptions' => array(
					'bbcode' => $settings -> getSetting('forum_bbcode', 0),
					'html' => $settings -> getSetting('forum_html', 0),
					 'plugins' => array(
							'table', 'fullscreen', 'media', 'preview', 'paste',
							'code', 'image', 'textcolor', 'jbimages', 'link', 'emoticons'
					 ),
					'toolbar1' => array(
					  'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 'emoticons', '|', 
					  'image', '|', 'link', '|', 'jbimages', '|', 'fullscreen', '|', 'preview'
					),  
					'width' => '100%',
					'upload_url' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('forum_id' => $this -> _forum -> getIdentity()), 'ynforum_upload_photo', true)
				),
				'required' => true,
				'allowEmpty' => false,
				'decorators' => array('ViewHelper'),
				'filters' => array(
					$filter,
					new Engine_Filter_Censor(),
				),
				'label' => 'Body',
			));
		}
		else
		{
			$this -> addElement('textarea', 'body', array(
				'required' => true,
				'attribs' => array(
					'rows' => 24,
					'cols' => 80,
					'style' => 'width:553px; max-width:553px;height:158px;'
				),
				'allowEmpty' => false,
				'filters' => array(
					$filter,
					new Engine_Filter_Censor(),
				),
			));
		}
		
		
		$translate = Zend_Registry::get('Zend_Translate');
		// Photo
		$this -> addElement('File', 'attach', array(
			'label' => 'File Attachment',
			'required' => false,
		));
		$max_file_size = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('forum.maxFileSizeAttach', 10000);
		$file_type = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('forum.fileType', 'gif, png, jpg, jpeg, doc, docx, xls, xlsx, ppt, pptx, pdf, zip, rar, mp3, wav, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz, txt, html, php, tpl');
		$this -> attach -> setDestination(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary');
		$this -> attach -> addValidator(new Zend_Validate_File_FilesSize( array(
			'min' => 1,
			'max' => $max_file_size * 1024,
			'bytestring' => true
		)));
		$this -> attach -> addValidator('Extension', false, $file_type);

		$this -> addElement('Checkbox', 'watch', array(
			'label' => 'Send me notifications when other members reply to this topic.',
			'value' => '1',
		));
		
		
		$this -> addElement('Button', 'managePhoto', array(
			'label' => 'Attach Photo',
			'type' => 'submit',
			'decorators' => array('ViewHelper')
		));

		$this -> addElement('Button', 'submit', array(
			'label' => 'Post Topic',
			'type' => 'submit',
			'decorators' => array('ViewHelper')
		));

		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'decorators' => array('ViewHelper')
		));
		$this -> addDisplayGroup(array(
			'managePhoto',
			'submit',
			'cancel'
		), 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
		$button_group -> addDecorator('DivDivDivWrapper');
	}

}
