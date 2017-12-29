<?php
class Advalbum_Form_Admin_Color extends Engine_Form
{
  public function init()
  {
  	    $view = Zend_Registry::get('Zend_View');
	    $view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Advalbum/externals/scripts/jscolor.js');
    	$this
		      ->setTitle('Color Settings')
		      ->setDescription('You can set any color settings for searching photos by dominant color.')
			  ->setAttrib('style',"width: 600px");
	  
		$this->addElement('dummy', 'head_color_settings',array(
	      'label'=>'Main Settings',
	     ));
		
		$this->addElement('Text', 'advalbum_maxcolor', array(
	      	'label' => 'Maximum dominant colors',      
	      	'allowEmpty' => false,
	      	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advalbum.maxcolor', 1),
			'validators'  => array(
					array('Int', true),
					new Engine_Validate_AtLeast(1),
			),
	    )); 
	
		$colorTbl = Engine_Api::_()->getDbTable("colors", "advalbum");
		$colors = $colorTbl -> fetchAll($colorTbl->select());
		
		if (count($colors))
		{
			$this->addElement('dummy', 'head_color_items',array(
					'label'=>'Dominant Color',
			));
			
			foreach ($colors as $color)
			{
				$this->addElement('Text', 'color__' . $color->getIdentity() , array(
						'label' => $color->getTitle(),
						'class' => 'color',
						'allowEmpty' => false,
						'value' => $color->hex_value,
				));
			}
		}
	
	    // Add submit button
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save Changes',
	      'type' => 'submit',
	      'value' => 'save',	
	      'ignore' => true
	    ));
		// Clear submit button
	    $this->addElement('Button', 'clear', array(
	      'label' => 'Set Default',
	      'type' => 'submit',
	      'value' => 'clear',
	      'ignore' => true
	    ));
	}
}