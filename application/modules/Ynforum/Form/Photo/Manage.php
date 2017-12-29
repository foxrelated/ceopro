<?php
class Ynforum_Form_Photo_Manage extends Engine_Form
{
	private $_fileIds;
	
	public function __construct($options = null) {
		if (is_array($options) && array_key_exists('fileIds', $options)) {
			$this->_fileIds = $options['fileIds'];
			unset($options['fileIds']);
		}
	
		parent::__construct($options);
	}
	
  public function init()
  {
  	$translate = Zend_Registry::get('Zend_Translate');
    // Init form
    $this
      ->setTitle('Manage Photo(s)')
      ->setAttribs(array())
      ;
  	$this->addElement('Radio', 'photo_option', array(
       
       'multiOptions' => array(                
                1 => 'Attach from computer.',
                0 => 'Choose from my library.',
       ),
       'onclick' => 'post_photo_choose();',
       'value' => 1,
    ));
	
	$this -> addElement('Dummy', 'html5_upload', 
		array(
			
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_Html5Upload.tpl',
					'class' => 'form element',
				)
			)), ));	
	
	$this -> addElement('Hidden', 'post_id', array('order' => 1000));
	$this -> addElement('Hidden', 'html5uploadfileids', array(
		'value' => '',
		'order' => 1001
	));
	
	$this -> addElement('Hidden', 'html5importfile', array(
		'value' => '',
		'order' => 1002
	));
	
	$this -> addElement('Hidden', 'ynforumpostuploadfile', array(
		'value' => $this->_fileIds,
		'order' => 1003
		
	));

	

   	$this->addElement('Button', 'submit', array(
      'label' => 'Finish',
      'type' => 'submit',
      //'onclick' => 'import_file()',
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
	$this->addElement('Button', 'managePhoto', array(
      'label' => 'Skip & Finish',
      'type' => 'submit',      
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
  

	 $this->addDisplayGroup(array(
      'submit',
      'managePhoto',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}