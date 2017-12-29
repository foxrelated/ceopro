<?php

class Advgroup_Form_Search extends Engine_Form
{
    protected $_location;
    public function setLocation($location)
    {
        $this -> _location = $location;
    }
    public function init()
    {
        $translate = Zend_Registry::get("Zend_Translate");
        //Set Form Layout And Attributes.
        $this
            ->setAttribs(array('id' => 'filter_form',
                'class' => 'global_form_box',
                'method' => 'GET'
            ));

        //Search Text Field.
        $this->addElement('Text', 'text', array(
            'label' => 'Search Groups:',
        ));

        $this->text->setAttrib('placeholder', 'Search groups');

        //Category Field.
        $categories = Engine_Api::_()->getDbtable('categories', 'advgroup')->getAllCategoriesAssoc();
        if (count($categories) >= 1) {
            $this->addElement('Select', 'category_id', array(
                'label' => 'Category:',
                'multiOptions' => $categories,

            ));
        }

        //View Field.
        $this->addElement('Select', 'view', array(
            'label' => 'View:',
            'multiOptions' => array(
                '0' => 'Everyone\'s Groups',
                '1' => 'Only My Friends\' Groups',
            ),

        ));

        //Order Field
        $this->addElement('Select', 'order', array(
            'label' => 'List By:',
            'multiOptions' => array(
                'creation_date' => 'Recently Created',
                'member_count' => 'Most Popular',
                'most_active' => 'Most Active',
                'alpha_az' => 'Alphabetical: A to Z',
                'alpha_za' => 'Alphabetical: Z to A',
            ),
            'value' => 'creation_date',

        ));

        $this->addElement('Text', 'location', array(
            'label' => 'Location',
            'decorators' => array(array(
                'ViewScript',
                array(
                    'viewScript' => '_location_search.tpl',
                    'viewModule' => 'advgroup',
                    'class' => 'form element',
                    'location_address' => $this->_location
                )
            )),
        ));

        $within_description = 'Radius (mile)';
        if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi') == 'km')
            $within_description = 'Radius (kilometer)';
        $this->addElement('Text', 'within', array(
            'label' => $within_description,
            'placeholder' => Zend_Registry::get('Zend_Translate')->_($within_description . '..'),
            'maxlength' => '60',
            'required' => false,
            'style' => "display: block",
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
            ),
        ));

        $this->addElement('hidden', 'lat', array(
            'value' => '0',
            'order' => 98
        ));

        $this->addElement('hidden', 'long', array(
            'value' => '0',
            'order' => 99
        ));

        $this->addElement('Hidden', 'page', array(
            'order' => 100
        ));

        $this->addElement('Hidden', 'tag', array(
            'order' => 101
        ));

        // Buttons
        $this->addElement('Button', 'Search', array(
            'label' => 'Search',
            'type' => 'submit',
        ));
    }
}