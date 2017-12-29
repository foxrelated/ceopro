<?php

class Ynwiki_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
     $translate = Zend_Registry::get('Zend_Translate'); 
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
     
     // set allow print wiki page
     $this->addElement('Radio', 'ynwiki_print', array(
        'label' => 'Print Wiki?',
        'description' => 'Allow guests to print wikis? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.print', 0),
      ));
     
     // set allow dowload wiki page
     $this->addElement('Radio', 'ynwiki_download', array(
        'label' => 'Download Wiki?',
        'description' => 'Allow guests to download wikis? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.download', 0),
      ));
     /* 
     // set allow reate wiki page
     $this->addElement('Radio', 'ynwiki_rate', array(
        'label' => 'Rate Wiki?',
        'description' => 'Allow owner to rate their own wikis? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.rate', 0),
      ));
      */
      $this->addElement('Text', 'ynwiki_fileType',array(
      'label'=>$translate->translate('Allow file type attachment'),
      'title' => $translate->translate('Allow file type attachment'),  
      'description' => 'If you want to allow file type, you can enter them below (separated by commas). Example: gif,png,jpg,rar,doc',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.fileType', 'gif, png, jpg, jpeg, doc, docx, xls, xlsx, ppt, pptx, pdf, zip, rar, mp3, wav, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz, txt, html, php, tpl'),
    ));
      
      $this->addElement('Text', 'ynwiki_maxFileSizeAttach',array(
      'label'=>$translate->translate('Maximum file size attachment'),
      'title' => $translate->translate('Maximum file size attachment'),  
      'description' => 'Enter the maximum filesize for uploaded files in KB. This must be a number between 1 and 204800.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.maxFileSizeAttach', 10000),
    ));
    
    $this->addElement('Text', 'ynwiki_page', array(
      'label' => 'Wiki Pages Per Page',
      'description' => 'How many wiki pages will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.page', 10),
    ));


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}