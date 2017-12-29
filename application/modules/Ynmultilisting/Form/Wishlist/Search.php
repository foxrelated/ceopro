<?php
class Ynmultilisting_Form_Wishlist_Search extends Engine_Form {
    public function init() {
        $this->setAttribs(array('class' => 'global_form_box search_form', 'id' => 'filter_form'))
             ->setMethod('GET');
        
        $this->addElement('Text', 'title', array(
            'label' => 'Search',
            'placeholder' => Zend_Registry::get('Zend_Translate')->_('Search Wish List'),
        ));
		
		$this->addElement('Text', 'owner_name', array(
            'label' => 'Member\'s Name/Email',
        ));
		
		$this->addElement('Select', 'owner_type', array(
			'label' => 'Wish List',
			'multiOptions' => array(
				'all' => 'All Wish Lists',
				'friend' => 'My Friend\'s Wish Lists'
			)
		));

        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}