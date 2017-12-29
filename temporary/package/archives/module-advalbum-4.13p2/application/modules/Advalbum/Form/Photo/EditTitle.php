<?php

class Advalbum_Form_Photo_EditTitle extends Engine_Form
{

    public function init ()
    {
        $this->setTitle('Change Photo Title')
            ->setDescription('Please enter new title and click Save to change')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(
                Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble(array()))
            ->setMethod('POST');

        $this->addElement('Text', 'title',
                array(
                        'label' => 'Title',
                        'maxlength' => 128,
                        'style' => 'width: 80%'
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