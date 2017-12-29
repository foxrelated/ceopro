<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advgroup
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Create.php 7659 2010-10-19 02:24:28Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Advgroup
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Advgroup_Form_Sponsor_Edit extends Advgroup_Form_Sponsor_Create
{
	public function init()
	{
		parent::init();
		$this
			->setTitle('Edit Sponsor')
			->setAttrib('id', 'advgroup_sponsor_edit');
		
		$this->submit->setLabel('Save Changes');
		
	    $this->addElement('Cancel', 'cancel', array(
	      'prependText' => ' or ',
	      'label' => 'Cancel',
	      'link' => true,
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close();',
	      'decorators' => array(
	        'ViewHelper'
	      ),
	    ));
	    
	    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	}
}