<?php
class Ynmultilisting_Form_Admin_Listingtype_Delete extends Engine_Form {
	
	protected $_ids = null;
	protected $_listingtype = null;
	
	public function getIds() {
        return $this -> _ids;
    }
	
    public function setIds($ids) {
        $this -> _ids = $ids;
    }
	
	public function getListingtype() {
        return $this -> _listingtype;
    }
	
    public function setListingtype($listingtype) {
        $this -> _listingtype = $listingtype;
    }
	
	public function init() 
	{
		$this->setTitle('Delete Listing Type');
		$this->setDescription('YNMULTILISTING_DELETE_LISTING_TYPE_DESCRIPTION');
		
		$typeTbl = Engine_Api::_()->getItemTable('ynmultilisting_listingtype');
		$typeOptions = $typeTbl -> getTypeAssoc();
		
		//unset value when click deleted
		if (count($this -> _ids)) {
			foreach($this -> _ids as $id) {
				unset($typeOptions[$id]);
			}
		}
		
		//unset value when click delete
		if($this -> _listingtype) {
			unset($typeOptions[$this ->_listingtype -> getIdentity()]);
		}
		
		$this->addElement('Select', 'listingtype_id', array(
	      	'label'     => 'Move to Listing Type',
			'multiOptions' => $typeOptions
		));
		
		// Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Delete',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
		
	   $this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	   	  'onclick' => 'parent.Smoothbox.close();',
	      'prependText' => ' or ',
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