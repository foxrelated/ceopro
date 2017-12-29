<?php
class Ynforum_Form_Photo_Upload extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Select Images(s)')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('class', 'global_form_popup ynforum_form_upload')
      ->setAttrib('name', 'albums_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

	
    $this -> addElement('Dummy', 'html5_upload', array('decorators' => array( array(
						'ViewScript',
						array(
							'viewScript' => '_Html5Upload.tpl',
							'class' => 'form element',
						)
					)), ));
			$this -> addElement('Hidden', 'campaign_id', array('order' => 1));
			$this -> addElement('Hidden', 'html5uploadfileids', array(
				'value' => '',
				'order' => 2
			));

    // Init submit
    $this->addElement('Button', 'submit_change', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'decorators' => array(
		        'ViewHelper',
		        ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => Zend_Registry::get('Zend_Translate')->_('or '),
      'link' => true,
      //'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'create'), 'ynfundraising_general', true),
      'href' => 'javascript:void()',
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	 // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit_change',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
  }
}