<?php

class Advalbum_Form_Photo_EditDate extends Engine_Form
{

    public function init ()
    {
        $this->setTitle('Change Date')
            ->setDescription('Where was this photo taken?')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(
                Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble(array()))
            ->setMethod('POST');

        $this->addElement('Date', 'taken_date', array(
                'label' => 'Date Taken',
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