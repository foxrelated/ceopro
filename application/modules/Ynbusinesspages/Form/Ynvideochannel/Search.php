<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/20/2016
 * Time: 10:17 AM
 */
class Ynbusinesspages_Form_Ynvideochannel_Search extends Engine_Form
{
    public function init()
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box'
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)));

        // Page ID
        $this->addElement('Hidden', 'page');

        // Search text
        $this->addElement('Text', 'search', array(
            'label' => 'Search Video',
            'alt' => $translate->translate('Search Video'),
        ));

        //Order
        $this->addElement('Select', 'browse_by', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'creation_date' => 'Recently Created',
                'most_liked' => 'Most Liked',
                'most_viewed' => 'Most Viewed',
                'most_commented' => 'Most Discussed',
            ),
            'value' => 'creation_date'
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }

}