<?php
class Ynmultilisting_Form_Admin_Widget_QuickLinkLink extends Core_Form_Admin_Widget_Standard {
  	public function init() {
    	parent::init();
    
    	// Set form attributes
    	$this->setTitle('Listing Quick Links - Link Only');
		
		$listingtypeRows = Engine_Api::_()->getItemTable('ynmultilisting_listingtype')->getAvailableListingTypes();
		$listingtypes = array();
		$listingtypes = array('0' => 'Current Listing Type', 'all' => 'All Listing Types');
		foreach ($listingtypeRows as $row) {
			$listingtypes[$row->getIdentity()] = $row->getTitle();
		}
		
		$this->addElement('Text', 'title', array(
			'label' => 'Title',
			'description' => 'Maximum 64 characters',
      		'validators' => array(
        		array('StringLength', false, array(0, 64)),
      		),
      		'filters' => array(
        			'StripTags',
        		new Engine_Filter_Censor(),
      		),
		));
		$this->title->getDecorator("Description")->setOption("placement", "append");
		
		$this->addElement('Select', 'listingtype', array(
			'label' => 'Listing Type',
			'multiOptions' => $listingtypes,
		));
		$listingtype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('listingtype_id', 0);
		if ($listingtype_id) {
			$this->listingtype->setValue($listingtype_id);
		}
		
		$this->addElement('Multiselect', 'quicklinks', array(
			'label' => 'Select Quick Links',
		));
		$this->getElement('quicklinks')->setRegisterInArrayValidator(false);
		
		$this->addElement('hidden', 'quicklink_ids', array(
			'value' => '',
			'order' => 999
		));
		
		$view = Zend_Registry::get('Zend_View');
		$view -> headScript() -> appendFile($view -> baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/quicklink_slide.js');
	}
}