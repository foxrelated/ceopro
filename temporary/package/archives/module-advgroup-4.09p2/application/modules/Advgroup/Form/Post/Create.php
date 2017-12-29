<?php
class Advgroup_Form_Post_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Reply')
      ->setAction(
        Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(array('action' => 'post', 'controller' => 'topic'), 'group_extended', true)
      );

    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'group_general', true);

    $this->addElement('TinyMce', 'body', array(
        'disableLoadDefaultDecorators' => true,
    		
      'editorOptions' => array(
      		'force_br_newlines' => true,
      		'force_p_newlines' => false,
      		'forced_root_block' => '',
          'bbcode' => 1,
          'html' => 1,
          'theme_advanced_buttons1' => array(
              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
              'media', 'image', 'link','unlink','fullscreen', 'preview', 'emotions'
          ),
          'theme_advanced_buttons2' => array(
              'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
              'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
              'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
          ),
          'width' => '100%',
          'toolbar1' => array(
              'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|',
              'image', '|', 'link', '|', 'jbimages', '|', 'fullscreen', '|', 'preview',  'emoticons'
          ),
          'plugins' => array(
              'table', 'fullscreen', 'media', 'preview', 'paste',
              'code', 'image', 'textcolor', 'jbimages','link', 'emoticons'
          ),
          'upload_url' => $upload_url
      ),
      'decorators' => array('ViewHelper'),
      'label' => 'Body',
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Checkbox', 'watch', array(
      'label' => 'Send me notifications when other members reply to this topic.',
      'value' => '1',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Post Reply',
      'ignore' => true,
      'type' => 'submit',
    ));

    $this->addElement('Hidden', 'topic_id', array(
      'order' => '920',
      'filters' => array(
        'Int'
      )
    ));
    
    $this->addElement('Hidden', 'ref');
  }
}