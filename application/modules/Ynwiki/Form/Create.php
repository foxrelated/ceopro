<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Wiki
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Create.php
 * @author     Minh Nguyen
 */
class Ynwiki_Form_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
        $this
          ->setDescription("Compose your new page below, then click 'Save Page' to publish page.")
          ->setAttrib('name', 'ynwiki_create');
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;
        $translate = Zend_Registry::get('Zend_Translate');
        $parent = Zend_Controller_Front::getInstance()->getRequest()->getParam('fromPageId');
        // prepare page
        if(!$parent)
        {
             $this->setTitle('Add A New Space'); 
             $this->addElement('hidden','level', array(
                'value' => 0,
                'order' =>100
            ));  
        }
        else
        {
            $this->setTitle('Add A Child Page');
            $this->addElement('hidden','parent_page_id', array(
                'value' => $parent,
                'order' => 101
            ));
            $objParent = Engine_Api::_()->getItem('ynwiki_page',$parent);
            $this->addElement('hidden','level', array(
                'value' => $objParent->level + 1,
                'order' => 102
            ));
        }
        $this->addElement('Text', 'title', array(
          'label' => 'Title',
          'required' => true,
          'title' => $translate->translate('Title of page'),             
          'autofocus' => 'autofocus',  
          'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags',
            new Engine_Filter_StringLength(array('max' => '255'))
        )));
        
        // init to
        $this->addElement('Text', 'tags',array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => 'Separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
       	$this->tags->getDecorator("Description")->setOption("placement", "append");
       
        $this->addElement('File', 'thumbnail', array(
	        'label' => 'Thumbnail',
	        'title' => $translate->translate('Main image of page'),    
	        'description' => 'Main image of page (jpg, png, gif, jpeg)',
        ));
        $this->thumbnail->getDecorator("Description")->setOption("placement", "append");
        $this->thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');
  		
  		
  		$upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'ynwiki_general', true);
		$allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'ynwiki_page', 'auth_html');
		$this->addElement('TinyMce', 'body', array(
          'label' => 'Content',
          'required' => true,
          'allowEmpty' => false,
          'decorators' => array(
            'ViewHelper'
          ),
          'editorOptions' => array(
            'upload_url' => $upload_url,
            'menubar' => true,
            'plugins' => array(
		   		'table', 'fullscreen', 'media', 'preview', 'paste',
		   		'code', 'image', 'textcolor', 'jbimages', 'link'
	 		 ),
		    'toolbar1' => "insertfile undo redo | styleselect | fontselect, fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
		    'toolbar2' => "print preview media | forecolor backcolor emoticons",
          ),
          'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags'=> $allowed_html))),
        ));
        $this->addElement('Button', 'submit', array(
	        'label' => 'Save',
	        'type' => 'submit',
	        'onclick' => 'removeSubmit()',
	        'ignore' => true,
	        'style' => 'margin-top:10px;',
	        'decorators' => array(
	        'ViewHelper',
	        ),
        ));
        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'browse'), 'ynwiki_general', true),
          'onclick' => '',
          'style' => 'margin-top:20px;',
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
