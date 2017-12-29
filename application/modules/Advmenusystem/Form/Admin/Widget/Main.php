<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ItemCreate.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Advmenusystem_Form_Admin_Widget_Main extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {
  	$view = Zend_Registry::get('Zend_View');
     $this
      ->setTitle('Global Settings')
      ->setDescription('General settings of Advanced Menu plugins. These settings affect all members in your community.');

	
	$this->addElement('Radio', 'style_menu',array(
		'label' => 'Main Menu Bar',
		'description' => 'Select style for main menu bar',
		'multiOptions' => array(
					'flat' => 'Flat',
					'metro' => 'Metro',
					'simple' => 'Simple',
					'special' => 'Special',
					'white' => 'White',
		),
		'value' => 'flat',
	));

	$this->addElement('Radio', 'fix_menu_position',array(
		'label' => 'Fixed menu position?',
		'description' => 'Do you want to fix menu position when users scroll the browser?',
		'multiOptions' => array(
				1 => 'Yes.',
				0 => 'No.',
		),
		'value' => 1,
	));

	$this->addElement('Radio', 'show_non_logged_user',array(
		'label' => 'Show to non-logged in users',
		'description' => 'Do you want to show this menu to non-logged in users?',
		'multiOptions' => array(
				1 => 'Yes.',
				0 => 'No.',
		),
		'value' => 1,
	));
    
	$this->addElement('Text', 'number_tabs', array(
		'label' => 'Number of Tabs',
		'description' => 'How many tabs do you want to show?',
		'value' => '7',
		'required' => true,
		'validators' => array(
				array('Between',true,array(1,12)),
		),
	));
	
	$this->addElement('Text', 'title_truncation', array(
		'label' =>  'Title truncation for the content',
		'description' => 'Enter the title truncation for the content.',
		'value' => '20',
		'required' => true,
		'validators' => array(
			array('Between',true,array(1,50)),
		),
	));
	
	
	$this->addElement('Radio', 'show_more_link',array(
			'label' => '"More" link',
			'description' => 'Do you want to show this display `More` link in this menu?',
			'multiOptions' => array(
					1 => 'Yes.',
					0 => 'No.',
			),
			'value' => 1,
	));		
	
	$this->addElement('Radio', 'arrow_sign',array(
			'label' => 'Show Arrow with Menu Name?',
			'description' => 'Do you want to show arrow with menu name?
			   			Note: The arrow sign will come only if the menu contains submenus.',
			'multiOptions' => array(
					1 => 'Yes.',
					0 => 'No.',
			),
			'value' => 1,
	));	

	// Add submit button
	$this->addElement('Button', 'submit', array(
	  'label' => 'Save Changes',
	  'type' => 'submit',
	  'ignore' => true
	));		
    
  }
}