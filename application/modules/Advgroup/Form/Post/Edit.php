<?php
class Advgroup_Form_Post_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Post');

    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'group_general', true);

    $this->addElement('TinyMce', 'body', array(
        'disableLoadDefaultDecorators' => true,
      'editorOptions' => array(
          'bbcode' => 1,
          'html' => 1,
          'theme_advanced_buttons1' => array(
              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
              'media', 'image', 'link', 'unlink', 'fullscreen', 'preview', 'emotions'
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
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Post',
      'ignore' => true,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}