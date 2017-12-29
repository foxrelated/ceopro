<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 8701 2011-03-24 23:05:37Z char $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Post_Edit extends Engine_Form {

    protected $_post;
	protected $_forum;

    public function setPost($post) {
        $this->_post = $post;
		$topic = $post -> getParent();
		$this->_forum = $topic -> getParent();	
    }	

    public function init() {
        $this
                ->setMethod("POST")
                ->setAttrib('name', 'forum_post_edit')
                ->setAttrib('id', 'form-upload')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib('class', 'global_form form_forum')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
		
		$this -> addElement('Text', 'title', array(
			'label' => 'Post title',
			'allowEmpty' => false,
			'filters' => array(new Engine_Filter_Censor(), ),
		));			

		$this->addElement('Dummy', 'icon_id', array(
  		  'label' => 'Save Changes',	
  		  'item_id' => $this->_post->icon_id,
	      'decorators' => array(		          
		          array('ViewScript',array(
		                'viewScript' => '_topic_icon.tpl',
		                'class'      => 'form element'
		          ))
		      ),
		));
		// LuanND END //

        $viewer = Engine_Api::_()->user()->getViewer();
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
							'code', 'image', 'textcolor', 'jbimages', 'link', 'emoticons'
					 ),
					'toolbar1' => array(
					  'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 'emoticons', '|', 
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
                ),
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
		
		if( !empty($this->_post->file_id) ) {
	      $photo_delete_element = new Engine_Form_Element_Checkbox('photo_delete', array('label'=>'This post has a photo attached. Do you want to delete it?'));
	      $photo_delete_element->setAttrib('onchange', 'updateUploader()');
	      $this->addElement($photo_delete_element);
	      $this->addDisplayGroup(array('photo_delete'), 'photo_delete_group');    
	    }
	
	    if( !empty($this->_post->file_id) ) {
	      $this->getDisplayGroup('photo_group')->getDecorator('HtmlTag')->setOption('style', 'display:none;');
	    }
		
		if(count($this->_post->getAttachments()))
		{
			$attachments = $this->_post->getAttachments();
			$attach_name = $attachments[0]->title;
			$file =  Engine_Api::_()->getItem('storage_file', $attachments[0]->file_id);
			$file_size = 0;
		    $req = Zend_Controller_Front::getInstance()->getRequest();
		    $domain = $req-> getScheme() . '://' . $req -> getHttpHost();
		    $path = $file->map();
			if(strstr($path, 'http') == "")
			{
				$path = $domain.$path;
			}
			$translate= Zend_Registry::get('Zend_Translate');
			if($file->size < 1024*1024)
		        $file_size = round($file->size/(1024),2)." Kb)";
		    else
		        $file_size = round($file->size/(1024*1024),2)." Mb)";
			$html = '<div class = "post_attachment">
					<span><img src="./application/modules/Ynforum/externals/images/attach.png"/></span>'.
				    '<a href="./application/modules/Ynforum/externals/scripts/download.php?f='.
				    $path.'&fc='.$attach_name.'">'.$attach_name.'</a></td>'.
				    '<span>&nbsp;('.$file_size.'</span>'.
				    '<a href="javascript:;" onclick = "deleteAttach()" class="buttonlink icon_forum_post_delete">'.$translate->translate("Delete").'</a>'.
					'</div>';
			$this -> addElement('Dummy', 'attached', array(
					'label' => 'Attachment',
					'content'=>$html
					));
			$this->addElement('Hidden', 'check_delete', array(
		      'value' => 0,
		    ));
		}
		
		$this->addElement('File', 'attach', array(
            'label' => 'File Attachment',
            'required'=>false,    
          ));
        $max_file_size = Engine_Api::_()->getApi('settings', 'core')->getSetting('forum.maxFileSizeAttach', 10000);
        $file_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('forum.fileType', 'gif, png, jpg, jpeg, doc, docx, xls, xlsx, ppt, pptx, pdf, zip, rar, mp3, wav, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz, txt, html, php, tpl');
        $this->attach->setDestination(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'); 
        $this->attach->addValidator(new Zend_Validate_File_FilesSize(array('min'=>1,
        'max'=>$max_file_size * 1024,'bytestring'=>true)));
        $this->attach->addValidator('Extension', false, $file_type); 
		$this->addDisplayGroup(array('attach'), 'attach_group');
		
		if(count($this->_post->getAttachments())) 
		{
      		$this->getDisplayGroup('attach_group')->getDecorator('HtmlTag')->setOption('style', 'display:none;');
    	}

        $this->addElement('Checkbox', 'watch', array(
            'label' => 'Send me notifications when other members reply to this topic.',
            'value' => '0',
        ));
		
		$this -> addElement('Button', 'managePhoto', array(
			'label' => 'Manage Photo',
			'type' => 'submit',
			'decorators' => array('ViewHelper')
		));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
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
            )
        ));
        $this->addDisplayGroup(array('managePhoto','submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
        $button_group->addDecorator('DivDivDivWrapper');
    }
}