<?php
class Ynmultilisting_Form_Admin_Comparison_Manage extends Engine_Form 
{
	protected $_category;
	protected $_formArgs;
	
	public function getCategory()
	{
		return $this -> _category;
	}
	public function setCategory($category)
	{
		$this -> _category = $category;
	} 
	
	public function getFormArgs()
	{
		return $this -> _formArgs;
	}
	public function setFormArgs($formArgs)
	{
		$this -> _formArgs = $formArgs;
	}
	
    public function init() 
    {
        $this->setTitle('Comparison Settings');
        $this->setDescription('Choose the information you want users to compare on earch category');
        
        $typeList = Engine_Api::_()->getItemTable('ynmultilisting_listingtype') -> getTypeAssoc();
        $this -> addElement('Select', 'listingtype_id', array(
        	'label' => 'Listing Type',
        	'multiOptions' => $typeList,
        	'value' => $this -> _category -> listingtype_id,
        	'disabled' => 'disabled'
        ));

        $comparisonTbl = Engine_Api::_()->getDbTable('comparisons', 'ynmultilisting');
        $compare = $comparisonTbl -> getCategoryComparison($this->_category->category_id);

        $type = $this->_category->getListingType();
    	$categories = $type -> getCategories();
		unset($categories[0]);
        $this -> addElement('Select', 'category_id', array(
        	'label' => 'Category',
        	'multiOptions' => array(),
        	'value' => $this -> _category -> category_id,
        	'disabled' => 'disabled'
        ));
    	foreach ($categories as $item) {
			$this -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
		}

		$generalFields = array(
			'photo' => 'Main image',
			'title' => 'Listing title',
			'price' => 'Price',
            'owner' => 'Added By',
            'creation_date' => 'Added Date',
			'expiration_date' => 'Expiration Date',
            'short_description' => 'Short Description'
		);
		
		$this -> addElement('MultiCheckbox', 'common_fields', array(
			'label' => 'General Information',
			'multiOptions' => $generalFields,
            'value' => $compare -> common_fields
		));
		
		$ratingTypeTbl = Engine_Api::_()->getItemTable('ynmultilisting_ratingtype');
		$ratingFields = $ratingTypeTbl -> getRatingTypeAssocByCategory($this->_category);
		if (count($ratingFields))
		{
			$this -> addElement('MultiCheckbox', 'rating_fields', array(
				'label' => 'Ratings',
				'multiOptions' => $ratingFields,
                'value' => $compare -> rating_fields
			));
		}
		
		$reviewTypeTbl = Engine_Api::_()->getItemTable('ynmultilisting_reviewtype');
		$reviewFields = $reviewTypeTbl -> getReviewTypeAssocByCategory($this->_category);
		if (count($reviewFields))
		{
			$this -> addElement('MultiCheckbox', 'review_fields', array(
				'label' => 'Reviews',
				'multiOptions' => $reviewFields,
                'value' => $compare -> review_fields
			));
		}
		
		$customFields = new Ynmultilisting_Form_Custom_Fields($this -> _formArgs);
		$customFields->setIsCreation(true);
		$elements = $customFields -> getElements();
		$customOptions = array();
		foreach ($elements as $elm)
		{
			$customOptions[$elm->getName()] = $elm->getLabel();
		}
		if (count($customOptions))
		{
			$this -> addElement('MultiCheckbox', 'custom_fields', array(
				'label' => 'Listing specifications',
				'multiOptions' => $customOptions,
                'value' => $compare -> custom_fields
			));
		}
		
        $this->addElement('Button', 'submit_btn', array(
            'type' => 'submit',
            'label' => 'Submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
    }
}