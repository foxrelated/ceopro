<?php
class Ynwiki_Form_Attach extends Engine_Form {
	public function init() {
		$this -> setTitle('Attach file')
        ->setAttrib('enctype', 'multipart/form-data') 
        -> setMethod('post') -> setAttrib('id', 'ynwiki_attach');
        
         $this->addElement('File', 'attach', array(
            'label' => 'Choose file*',
            'required'=>true,    
          ));
          $max_file_size = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.maxFileSizeAttach', 10000);
          $file_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.fileType', 'gif, png, jpg, jpeg, doc, docx, xls, xlsx, ppt, pptx, pdf, zip, rar, mp3, wav, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz, txt, html, php, tpl, xmind');
          $this->attach->setDestination(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'); 
          $this->attach->addValidator(new Zend_Validate_File_FilesSize(array('min'=>1,
            'max'=>$max_file_size * 1024,'bytestring'=>true)));
          $this->attach->addValidator('Extension', false, $file_type); 

		$this -> addElement('Button', 'submit', array(
			'label' => 'Submit',
			'type' => 'submit',
            'onclick' => 'removeSubmit()',
			'ignore' => true,
            'decorators' => array(
            'ViewHelper',
            ),
		));

		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true, 
			'prependText' => ' or ',
			'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view','pageId' => Zend_Controller_Front::getInstance()->getRequest()->getParam('pageId')), 'ynwiki_general', true),
            'decorators' => array(
            'ViewHelper',
          ),
		));
         // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
	}

}
