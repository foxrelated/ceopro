<?php

class Advalbum_Form_Album_MovePhotos extends Engine_Form
{
	public function init ()
    {
        $this->setTitle('Move to other album')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(
                Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble(array()))
            ->setMethod('POST');

        $this->addElement('Select', 'album_id',
                array(
                ));
        // Buttons
        $this->addElement('Button', 'submit',
                array(
                        'label' => 'Move',
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
    }
}