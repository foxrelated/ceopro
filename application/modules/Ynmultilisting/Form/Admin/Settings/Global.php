<?php
class Ynmultilisting_Form_Admin_Settings_Global extends Engine_Form {
    protected $_params = array();
    
    public function getParams() {
        return $this -> _params;
    }
    
    public function setParams($params) {
        $this -> _params = $params;
    }
     
    public function init() {
        $this
            ->setTitle('Global Settings')
            ->setDescription('YNMULTILISTING_SETTINGS_GLOBAL_DESCRIPTION');
        
        $translate = Zend_Registry::get('Zend_Translate');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $params = $this->getParams(); 
        
        $this->addElement('Integer', 'ynmultilisting_max_import', array(
            'label' => 'Maximum listings can be imported each time',
            'description' => 'Set 0 is unlimited', 
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => $settings->getSetting('ynmultilisting_max_import', 100),
        ));
        
        $this->addElement('Integer', 'ynmultilisting_feature_fee', array(
            'label' => 'Fee to Feature Listing',
            'description' => 'USD for 1 day',
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => $settings->getSetting('ynmultilisting_feature_fee', 0),
        ));
        
        $this->addElement('Integer', 'ynmultilisting_photo_maxsize', array(
            'label' => 'Maximum Upload Size of Photo',
            'description' => 'Maximum Upload Size of Photo (KB)',
            'value' => $settings->getSetting('ynmultilisting_photo_maxsize', 500),
            'required' => true,
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
		$this -> addElement('dummy', 'image', array(
	        'required'  => false,
	        'allowEmpty'=> true,
	        'ignored' => true,
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_global_image.tpl',
				)
			)), 
		));
		
        $colorSettings = array(
            'menu_backgroundcolor' => 'Background color of menu', 
            'menu_hovercolor' => 'Hover color of menu', 
            'menu_textcolor' => 'Text color of menu',
            'menu_backgroundbar' => 'Background color of menu bar'
        );
        
		$defaultColor = array(
			'menu_backgroundcolor' => '#54a9d8', 
            'menu_hovercolor' => '#FFFFFF', 
            'menu_textcolor' => '#FFFFFF',
            'menu_backgroundbar' => '#54a9d8'
		);

        $this->addElement('Radio', 'ynmultilisting_use_custom_background_color', array(
            'label' => 'Custom background color of menu',
            'multiOptions' => array(
                0 => 'No, use theme default color',
                1 => 'Yes, use custom color',
            ),
            'value' => $settings->getSetting('ynmultilisting_use_custom_background_color', 1),
        ));
		
        foreach ($colorSettings as $key => $value) {
            $color = $settings->getSetting('ynmultilisting_'.$key, $defaultColor[$key]);
            if (isset($params[$key])) {
                $color = $params[$key];
            }
            $this->addElement('Heading', 'ynmultilisting_'.$key, array(
                'label' => $value,
                'value' => '<input value="'.$color.'" type="color" id="'.$key.'" name="'.$key.'"/>'
            ));
        }
		
		$this->addElement('Select', 'ynmultilisting_menu_linetype', array(
			'label' => 'Type of separated line',
			'multiOptions' => array(
				'solid' => 'Solid',
				'dotted' => 'Dot',
				'dashed' => 'Dash',
				'none' => 'None'
			),
			'value' => $settings->getSetting('ynmultilisting_menu_linetype', 'solid'),		
		));
		
		$this->addElement('Integer', 'ynmultilisting_max_listingtype', array(
            'label' => 'How many listing type items do you want to show on menu?',
            'description' => 'Set 0 is unlimited', 
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => $settings->getSetting('ynmultilisting_max_listingtype', 8),
        ));
		
		$this->addElement('Radio', 'ynmultilisting_menu_showmore', array(
			'label' => 'Do you want to show "More" link on listing type menu',
			'multiOptions' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'value' => $settings->getSetting('ynmultilisting_menu_showmore', 1),		
		));
		
		$this->addElement('Integer', 'ynmultilisting_max_subscribeemail', array(
            'label' => 'Maximum Subscribe Email',
            'description' => 'Maximum alert emails that one IP can use to subscribe listings (Enter a number between 1 and 5)',
            'value' => $settings->getSetting('ynmultilisting_max_subscribeemail', 1),
            'validators' => array(
                array('Between',true,array(1,5)),
            ),
        ));
		
		$this->addElement('Integer', 'ynmultilisting_max_getsubscribeperemail', array(
            'label' => 'Maximum Subscribers For One Email',
            'description' => 'Maximum subscribers that one email can use to subscribe listings (Enter a number between 1 and 5)',
            'value' => $settings->getSetting('ynmultilisting_max_getsubscribeperemail', 1),
            'validators' => array(
                array('Between',true,array(1,5)),
            ),
        ));

        $videoTypeOptions = Engine_Api::_()->ynmultilisting()->getAllVideoTypes();
        if (count($videoTypeOptions) > 1) {
            $this->addElement('Radio', 'ynmultilisting_video_type', array(
                'label'        => 'Which video module will be used for multi listing',
                'description'  => 'This selection will be effected to all listings in the Multi Listing module. Users can have the auto-suggested videos while adding / editing listing based on the selected video module. This is also applied to the listing detail in case your users want to add more videos as listing\'s materials',
                'multiOptions' => $videoTypeOptions,
                'value'        => $settings->getSetting('ynmultilisting_video_type', 'video'),
            ));
        }
		
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}