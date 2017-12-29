<?php

class Advalbum_Form_Photo_AddToVirtual extends Engine_Form
{
	public function init ()
    {
    	$viewer = Engine_Api::_() -> user() -> getViewer();
    	$albumTbl = Engine_Api::_()->getDbTable("albums", "advalbum");
    	$virtualAlbumAssoc = $albumTbl->getVirtualAlbumsAssoc($viewer);
    	
        $this->setTitle('Add Photo to Virtual Album')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(
                Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble(array()))
            ->setMethod('POST');

        
        
        $this->addElement('Select', 'album_id',
                array(
                        'label' => 'Select Album',
                        'style' => 'width: 100%',
                		'multiOptions' => $virtualAlbumAssoc
                ));
        // Buttons
        $this->addElement('Button', 'submit',
                array(
                        'label' => 'Save',
                        'type' => 'submit',
                        'ignore' => true,
                        'decorators' => array(
                                'ViewHelper'
                        )
                ));

        $this->addElement('Cancel', 'cancel',
                array(
                        'label' => 'cancel',
                        'link' => true,
                        'prependText' => ' or ',
                        'href' => '',
                        'onclick' => 'parent.Smoothbox.close();',
                        'decorators' => array(
                                'ViewHelper'
                        )
                ));
        $this->addDisplayGroup(array(
                'submit',
                'cancel'
        ), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }
}