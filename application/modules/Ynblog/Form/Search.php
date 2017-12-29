<?php

class Ynblog_Form_Search extends Engine_Form
{
    /*----- Init Form Function -----*/
    public function init()
    {
        $this
            ->setAttribs(array(
                'id' => 'ynblog_filter_form',
                'class' => 'global_form_box',
                'style' => 'margin-bottom: 15px',
                'method' => 'GET',
            ))
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        //Text filter element

        $zend_View = Zend_Registry::get('Zend_View');

        $this->addElement('Text', 'search', array(
            'label' => 'Search Blogs',
            'placeholder' => $zend_View->translate('Search blogs...')
        ));

        //Browse By Filter Element
        $this->addElement('Select', 'orderby', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'creation_date' => 'Most Recent',
                'view_count' => 'Most Viewed',
                'comment_count' => 'Most Commented',
            ),
        ));

        //Mode Filter Element
        $draft_array = array(
            '' => 'All Entries',
            '2' => 'Only Approved',
            '1' => 'Only Pending',
            '0' => 'Only Drafts',
        );

        $this->addElement('Select', 'mode', array(
            'label' => 'Show',
            'multiOptions' => $draft_array,
        ));

        //Users Filter Element
        $this->addElement('Select', 'show', array(
            'label' => 'Show',
            'multiOptions' => array(
                '1' => 'Everyone\'s Blogs',
                '2' => 'Only My Friends\' Blogs',
            ),
            'onchange' => 'this.form.submit();',
        ));

        $cat_arrays = Engine_Api::_()->getItemTable('blog_category')->getCategoriesAssoc();
        //Category Filter Element
        $this->addElement('Select', 'category', array(
            'label' => 'Category',
            'multiOptions' => $cat_arrays,
        ));

        $this->addElement('Hidden', 'page', array(
            'order' => 100
        ));

        $this->addElement('Hidden', 'tag', array(
            'order' => 101
        ));

        $this->addElement('Hidden', 'start_date', array(
            'order' => 102
        ));

        $this->addElement('Hidden', 'end_date', array(
            'order' => 103
        ));

        $this->addElement('Hidden', 'user_id', array(
            'order' => 104
        ));

        $this->addElement('Hidden', 'date', array(
            'order' => 105
        ));


        // Buttons
        $this->addElement('Button', 'submit_button', array(
            'value' => 'submit_button',
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}