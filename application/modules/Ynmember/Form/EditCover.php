<?php
class Ynmember_Form_EditCover extends Engine_Form
{
  public function init()
  {
  	 $view = Zend_Registry::get('Zend_View');
  	 $this->setTitle('Edit Cover Photo')
	 			->setAttrib('style', 'width:320px')
                ->setAttrib('class', 'global_form_popup');
    $user = Engine_Api::_()->user()->getViewer();
	
    $this
      ->setTitle('Edit Cover Photo');


	// Cover Photo
    $this->addElement('File', 'cover_thumb', array(
      'label' => $view ->translate('Cover Photo')
    ));
    $this->cover_thumb->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

     $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
