<?php
class Ynmultilisting_Form_Create extends Engine_Form
{
	protected $_category;
	protected $_formArgs;
    protected $_package;
	
    public function getPackage()
	{
		return $this -> _package;
	}
	
	public function setPackage($package)
	{
		$this -> _package = $package;
	} 
    
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
  		$view = Zend_Registry::get('Zend_View');
		$id = Engine_Api::_()->user()->getViewer() -> level_id;
	    $this->setTitle('Post New Listing');
		$this->setAttrib('onsubmit', 'removeSubmit()');
		$this->addElement('Select', 'category_id', array(
			  'required'  => true,
		      'allowEmpty'=> false,
		      'label' => 'Category',
	    ));
	    
	  	// Add subforms
	    if( !$this->_item ) {
			$customFields = new Ynmultilisting_Form_Custom_Fields($this -> _formArgs);
	    } 
	    else {
			$customFields = new Ynmultilisting_Form_Custom_Fields(array(
				'item' => $this->getItem()
			));
	    }
	    if( get_class($this) == 'Ynmultilisting_Form_Create' ) {
			$customFields->setIsCreation(true);
	    }
		
	    $this->addSubForms(array(
			'fields' => $customFields
	    ));
		
		// Add subformsParent
		if(!empty($this -> _formArgsParent))
		{
		    if( !$this->_item ) {
		      $customFieldsParent = new Ynmultilisting_Form_Custom_FieldsParent($this -> _formArgsParent);
		    } else {
		      $customFieldsParent = new Ynmultilisting_Form_Custom_FieldsParent(array(
		        'item' => $this->getItem()
		      ));
		    }
		    if( get_class($this) == 'Ynmultilisting_Form_Create' ) {
		      $customFieldsParent->setIsCreation(true);
		    }
		    $this->addSubForms(array(
		      'fieldsParent' => $customFieldsParent
		    ));
		}
	    
		// Add theme element
  		if($this -> _package){
			$this -> addElement('dummy', 'theme', array(
			        'required'  => true,
			        'allowEmpty'=> false,
					'decorators' => array( array(
						'ViewScript',
						array(
							'viewScript' => '_post_listings_themes.tpl',
							'category' =>  $this -> _category,
							'class' => 'form element',
							'package' => $this -> _package,
						)
					)), 
			));  
	    }
	    
	    $this->addElement('Text', 'title', array(
		      'label' => 'Listing Title',
		      'allowEmpty' => false,
		      'required' => true,
		      'validators' => array(
			        array('NotEmpty', true),
			        array('StringLength', false, array(1, 128)),
		      ),
		      'filters' => array(
			        'StripTags',
			        new Engine_Filter_Censor(),
		      ),
	    ));
	
	    $this->addElement('Text', 'tags',array(
	          'label'=>'Tags (Keywords)',
	          'autocomplete' => 'off',
	          'description' => 'Separate tags with commas.',
	          'filters' => array(
	            new Engine_Filter_Censor(),
	          ),
	        ));
	    $this->tags->getDecorator("Description")->setOption("placement", "append");
		
		$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
		
		$this->addElement('Textarea', 'short_description', array(
	      'label' => 'Short Description',
	      'allowEmpty' => false,
		      'required' => true,
		      'maxlength' => 400,
		      'validators' => array(
			        array('NotEmpty', true),
			        array('StringLength', false, array(1, 400)),
		      ),
		      'filters' => array(
			        'StripTags',
			        new Engine_Filter_Censor(),
		      ),
	    ));
		
	    $this->addElement('TinyMce', 'description', array(
	      'label' => 'Description',
	      'editorOptions' => array(
	         'mode'=> 'exact',
	      	 'elements'=>"description,about_us",
	          'html'   => 1,
	          'theme_advanced_buttons1' => array(
	              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
	              'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
	          ),
	          'theme_advanced_buttons2' => array(
	              'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
	              'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
	              'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
	          ),
	        ),
	      'required'   => true,
	      'allowEmpty' => false,
	      'filters' => array(
	        new Engine_Filter_Censor(),
	        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
	    ));
		
	   $this->addElement('TinyMce', 'about_us', array(
	      'label' => 'About Us',
	      'editorOptions' => array(
	         'mode'=> 'exact',
	      	 'elements'=>"description,about_us",
	          'html'   => 1,
	          'theme_advanced_buttons1' => array(
	              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
	              'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
	          ),
	          'theme_advanced_buttons2' => array(
	              'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
	              'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
	              'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
	          ),
	        ),
	      'required'   => true,
	      'allowEmpty' => false,
	      'filters' => array(
	        new Engine_Filter_Censor(),
	        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
	    ));
	
	    $this->addElement('File', 'photo', array(
	      'label' => 'Main Photo'
	    ));
		$this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		
		$this -> addElement('Text', 'to', array(
		'label'=>'Main Video', 
		'description' => 'Please choose one video.',
		'autocomplete' => 'off'));
		Engine_Form::addDefaultDecorators($this -> to);
	
		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
		    'label' => 'Main Video',
			'style' => 'margin-top:-5px',
			'order' => 8,
			'validators' => array('NotEmpty'),
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);
		
		$this->addElement('Float', 'price', array(
	      'label' => 'Price',
	      'required' => true,
	      'allowEmpty' => false,
	    ));
		
	    $this->addElement('Select', 'currency', array(
	      'label' => 'Currency',
	      'value' => 'USD',
	    ));
	    $this->getElement('currency')->getDecorator('Description')->setOption('placement', 'APPEND');
		
		$this -> addElement('Dummy', 'location_map', array(
				'label' => 'Location',
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_location_search.tpl',
						'class' => 'form element',
					)
				)), 
			));
			
		$this -> addElement('hidden', 'location_address', array(
			'value' => '0',
			'order' => '97'
		));

		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));
			
	    $this->addElement('Radio', 'search', array(
	      'label' => 'Include in search results?',
	      'multiOptions' => array(
	        '1' => 'Yes, include in search results.',
	        '0' => 'No, hide from search results.',
	      ),
	      'value' => '1',
	    ));
	    
		$currency = $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
	    $settings = Engine_Api::_()->getApi('settings', 'core');
	    $this->addElement('Checkbox', 'set_featured', array(
	      'label' => $view -> translate('Feature this listing with %s per day', $view->locale()->toCurrency($settings->getSetting('ynmultilisting_feature_fee', 0), $currency)),
	      'value' => 1,
	      'onchange' => 'showHideDayNumber(this);'
	    ));
	
	    $this->addElement('Text', 'feature_day_number', array(
	    	'label' => 'Number of featured days',
		      'validators' => array(
		          new Engine_Validate_AtLeast(1),
		       ),
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
        $id = Engine_Api::_() -> user() -> getViewer() -> level_id;
		$currentListingType = Engine_Api::_() -> ynmultilisting() -> getCurrentListingType();
        foreach ($auths as $auth) {
            $options = (array) $currentListingType->getPermission(null, 'ynmultilisting_listing', $auth);
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
		
	    // Buttons
	    $this->addElement('Button', 'submit_button', array(
	      'value' => 'submit_button',
	      'label' => 'Publish Listing',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
		
		 // Buttons
	    $this->addElement('Button', 'save_draft', array(
	      'value' => 'save_draft',
	      'label' => 'Save as Draft',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
	
	    $this->addElement('Cancel', 'cancel', array(
	      'label' => 'Cancel',
	      'link' => true,
	      'prependText' => ' or ',
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
	
	    $this->addDisplayGroup(array('submit_button', 'save_draft', 'cancel'), 'buttons', array(
	      'decorators' => array(
	        'FormElements',
	        'DivDivDivWrapper',
	      ),
	    ));
	}
}
