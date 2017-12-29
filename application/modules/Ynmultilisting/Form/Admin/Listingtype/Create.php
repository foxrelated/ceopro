<?php
class Ynmultilisting_Form_Admin_Listingtype_Create extends Engine_Form {
	public function init() {
		$this->setTitle('Add New Listing Type');
		$this->addElement('Text', 'title', array(
	      	'label'     => 'Listing Type Name',
	      	'required'  => true,
	      	'allowEmpty'=> false,
	      	'filters' => array(
	        	'StripTags',
				new Engine_Filter_Censor(),
			),
			'validators' => array(
		        array('NotEmpty', true),
		        array('StringLength', false, array(1, 128)),
			),
		));
		
		$this -> addElement('dummy', 'list_view', array(
				'label'     => 'Select List View',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Select List View',
						'name' => 'list_view'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'grid_view', array(
				'label'     => 'Select Grid View',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Select Grid View',
						'name' => 'grid_view'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'pin_view', array(
				'label'     => 'Select PinBoard View',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Select PinBoard View',
						'name' => 'pin_view'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'feature_widget', array(
				'label'     => 'Layout of Featured Listings Widget',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Layout of Featured Listings Widget',
						'name' => 'feature_widget'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'category_widget', array(
				'label'     => 'Layout of Main Category Widget',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Layout of Main Category Widget',
						'name' => 'category_widget'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'most_viewed_widget', array(
				'label'     => 'Layout of Most Viewed Widget',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Layout of Most Viewed Widget',
						'name' => 'most_viewed_widget'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'most_liked_widget', array(
				'label'     => 'Layout of Most Liked Widget',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Layout of Most Liked Widget',
						'name' => 'most_liked_widget'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'most_discussed_widget', array(
				'label'     => 'Layout of Most Discussed Widget',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Layout of Most Discussed Widget',
						'name' => 'most_discussed_widget'
					)
				)), 
		));
		
		$this -> addElement('dummy', 'most_commented_widget', array(
				'label'     => 'Layout of Most Commented Widget',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_custom_radio.tpl',
						'class' => 'form element',
						'label'     => 'Layout of Most Commented Widget',
						'name' => 'most_commented_widget'
					)
				)), 
		));
		
		$this -> addElement('Checkbox', 'show', array(
			'label' => 'Show',
			'value' =>  1,
			'checked' => true,
		));
		
		// Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Add',
	      //'onclick' => 'removeSubmit()',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
		
	   $this->addElement('Cancel', 'cancel', array(
	      'label' => 'Cancel',
	   	  'onclick' => 'parent.Smoothbox.close();',
	      'link' => true,
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