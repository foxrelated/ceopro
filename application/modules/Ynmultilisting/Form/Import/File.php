<?php
class Ynmultilisting_Form_Import_File extends Engine_Form {
	
	protected $_listingType;
	
	public function getListingType() {
		return $this->_listingType;
	}
	
	public function setListingType($listingType) {
		$this->_listingType = $listingType;	
	}
	
    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
    	$id = $user -> level_id;
        
        // Init form
        $this
          ->setAttrib('name', 'ynmultilisting-import-file')
          ->setAttrib('enctype', 'multipart/form-data')
          ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array())) ;
    	
    	// Init path
        $this->addElement('File', 'file', array(
          'label' => 'Select File',
          'description' => 'Choose a file XLS, CSV to import.' 
        ));
        $this->file->addValidator('Extension', false, 'csv,xls');	
        
        $this->addElement('Radio', 'approved', array(
            'label' => 'Approve Listings',
            'multiOptions' => array(
                1 => 'Approve these listings automatically',
                0 => 'These listings must wait for approval'
            ),
            'value' => 1
        ));
		
		//package
		$this->addElement('Select','package_id', array(
			'label' => 'Package',
			'description' => 'Choose Package for imported listings',
		));
		
        // Privacy
        $availableOptions = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );
        
        $auths = array('auth_view', 'auth_comment', 'auth_share', 'auth_photo', 'auth_video', 'auth_discussion');
        foreach ($auths as $auth) {
        	$listingtype = $this->getListingType();
			if ($listingtype) {
				$options = (array) $listingtype->getPermission(null, 'ynmultilisting_listing', $auth);
			}
			else {
            	$options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmultilisting_listing', $id, $auth);
        	}
            $options = array_intersect_key($availableOptions, array_flip($options));
            
            if( !empty($options) && count($options) >= 1 ) {
                // Make a hidden field
                if(count($options) == 1) {
                    $this->addElement('hidden', $auth, array('value' => key($options)));
                // Make select box
                } else {
                    $this->addElement('Select', $auth, array(
                        'label' => 'YNMULTILISTING_'.strtoupper($auth).'_LABEL',
                        'description' => 'YNMULTILISTING_'.strtoupper($auth).'_IMPORT_DESCRIPTION',
                        'multiOptions' => $options,
                        'value' => key($options),
                    ));
                    $this->$auth->getDecorator('Description')->setOption('placement', 'append');
                }
            }
        }
    
        // Init submit
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Import Listings',
            'type'  => 'button',
            'onclick' => 'import_listings(event)'
        ));
  }

}
