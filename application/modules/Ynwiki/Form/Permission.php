<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Wiki
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Permission.php
 * @author     Minh Nguyen
 */
class Ynwiki_Form_Permission extends Engine_Form
{
  public $_error = array();
  protected $_item;
  protected $_parent_type;
  protected $_parent_id;

  public function setParent_type($value) {
      $this->_parent_type = $value;
  }

  public function setParent_id($value) {
      $this->_parent_id = $value;
  }

  public function getParent_type() {
      return $this->_parent_type;
  }

  public function getParent_id() {
      return $this->_parent_id;
  }
  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }  
  public function init()
  {       
        $this->setTitle('Restrict people')
          ->setAttrib('name', 'ynwiki_permission');
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;
        $translate = Zend_Registry::get('Zend_Translate');
        
        //get role in model page
        $model = new Ynwiki_Model_Page(array());
        
  		if($this->_parent_type == 'group')
        {
            $availableLabels = array(
              'everyone'      => 'Everyone',
              'registered'    => 'All Registered Members',             
              'parent_member' => 'Group Members (group wikis only)',       
              'member'   => 'Wiki members',
              'ynwiki_list' 	  => 'Owner and Officer'
            );
        }
        else
        {
        	
/*			old permission
			'everyone'            => 'Everyone',
			'registered'          => 'All Registered Members',
			'owner_network'       => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member'        => 'Friends Only',
			'owner'               => 'Just Me'*/

            $availableLabels = array(
            		'everyone'      => 'Everyone',
            		'registered'    => 'All Registered Members',
            		'member'   => 'Wiki members',
            		'ynwiki_list' 	  => 'Owner and Officer'
            );
        }
        
        $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynwiki_page', $user, 'auth_restrict');
        $options = array_intersect_key($availableLabels, array_flip($options));
                       
        $this->addElement('Radio', 'auth_restrict', array(
          'label' => 'Allow to restrict',
          'description' => 'Who may restrict this page?',
          'multiOptions' => $options,
          'value' => key($options),
          //'onclick' => 'changeOption(this)',
        ));
      
        $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynwiki_page', $user, 'auth_view');
        $options = array_intersect_key($availableLabels, array_flip($options));
          
        $this->addElement('Radio', 'auth_view', array(
          'label' => 'Allow to view',
          'description' => 'Who may view this page?',
          'multiOptions' => $options,
          'value' => key($options),
        ));
        
        $this->addElement('Dummy', 'text_view', array(
          'content' => '',//Inherited permission from a parent page         
        ));
        
        $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynwiki_page', $user, 'auth_edit');
        $options = array_intersect_key($availableLabels, array_flip($options));

        $this->addElement('Radio', 'auth_edit', array(
          'label' => 'Allow to edit',
          'description' => 'Who may edit this page?',
          'multiOptions' => $options,
          'value' => key($options),
        ));
            
        $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynwiki_page', $user, 'auth_delete');
        $options = array_intersect_key($availableLabels, array_flip($options));

        $this->addElement('Radio', 'auth_delete', array(
          'label' => 'Allow to delete',
          'description' => 'Who may delete this page?',
          'multiOptions' => $options,
          'value' => key($options),
        ));
        
        $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynwiki_page', $user, 'auth_comment');
        $options = array_intersect_key($availableLabels, array_flip($options));

        // Comment
        $this->addElement('Radio', 'auth_comment', array(
          'label' => 'Allow to comment',
          'description' => 'Who may post comments on this page?',
          'multiOptions' => $options,
          'value' => key($options),
        ));
        
        $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
        'ViewHelper',
        ),
        ));      
        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view','pageId' => Zend_Controller_Front::getInstance()->getRequest()->getParam('pageId')), 'ynwiki_general', true),
          'onclick' => '',
          'decorators' => array(
            'ViewHelper',
          ),
        ));
         // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
  }
}
