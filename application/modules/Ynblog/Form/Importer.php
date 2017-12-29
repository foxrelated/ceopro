<?php

class Ynblog_Form_Importer extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
    $this->setTitle('Import Blogs')
      ->setDescription('BLOG_IMPORT_DESCRIPTION')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'blogs_import');
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    $this->addElement('Select', 'system', array(
      'label' => 'System',
      'multiOptions' => array("0"=>"","1"=>"WordPress", "2"=>"Blogger","3"=>"Tumblr","4"=>"URL"),
	  'onchange' => "updateTextFields()",
      'description' => 'Choose a system to import.'
    ));

	// Init path
    $this->addElement('File', 'filexml', array(
      'label' => 'File XML',
      'description' => 'Choose a file XML to import.' 
    ));
    $this->filexml->addValidator('Extension', false, 'xml');

  // Init URL link
    $this->addElement('Text','url',array(
        'label'=> 'XML File URL',
        'require' => true,
        'description' => 'FILE_XML_INPUT_DESCRIPTION',
    ));
    
	// Init Username
    $this->addElement('Text', 'username', array(
      'label' => 'Username',
      'description' => 'USERNAME_DISCRIPTION',
    ));
	 
     $availableLabels = array(
      'everyone'            => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me'
    );
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Import Blogs',
      'type' => 'submit',
    ));
  } 

}
