<?php

class Ultimatenews_Form_Create extends Engine_Form
{

	public function init()
	{
		// Init form
		$this -> setTitle('News Information');
		$this -> addElement('Hidden', 'content_id', array());

		// Init name
		$this -> addElement('Text', 'title', array(
			'label' => 'Title',
			'required' => true,
			'style' => "width:260px",
			'validators' => array('NotEmpty', ),
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags'
			)
		));

		$this -> addElement('File', 'photo', array(
			'label' => 'Thumbnail',
			'description' => 'News thumbnail (jpg, png, gif, jpeg)',
		));

		// Init name
		$this -> addElement('Text', 'link_detail', array(
			'label' => 'Original Link',
			'style' => "width:260px",
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags'
			)
		));
		
		// init to
	    $this->addElement('Text', 'tags',array(
	      'label'=>'Tags (Keywords)',
	      'autocomplete' => 'off',
	      'style' => "width:260px",
	      'description' => 'Separate tags with commas.',
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	    ));
	    $this->tags->getDecorator("Description")->setOption("placement", "append");
		
		$this -> addElement('Select', 'categoryparent', array(
			'label' => 'Category',
			'multiOptions' => array('-1' => 'All Categories', ),
			'onchange' => "loadFeed()",
			'style' => "width:160px;margin-bottom:5px;",
		));

		$this -> addElement('Select', 'category', array(
			'label' => 'Feed',
			'multiOptions' => array(
			),
			'required' => true,
			'style' => "width:160px;margin-bottom:5px;",
		));

		$this -> photo -> getDecorator("Description") -> setOption("placement", "append");
		$this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$upload_url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'upload-photo'), 'ultimatenews_general', true);
		
		$this -> addElement('TinyMce', 'description', array(
			'label' => 'Description',
			'required' => true,
			'allowEmpty' => true,
			'editorOptions' => array(
				'bbcode' => 1,
				'html' => 1,
				'plugins' => array(
				   		'table', 'fullscreen', 'media', 'preview', 'paste',
				   		'code', 'image', 'textcolor', 'jbimages'
				 ),
   		  
	      		'toolbar1' => array(
			      'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
			      'image', '|', 'link', '|', 'jbimages', '|', 'fullscreen', '|', 'preview'
			     ),         
				'upload_url' => $upload_url
			),
			'filters' => array(new Engine_Filter_Censor(), ),
		));
		
		$this -> addElement('TinyMce', 'content', array(
			'label' => 'Content',
			'required' => true,
			'allowEmpty' => true,
			'editorOptions' => array(
				'bbcode' => 1,
				'html' => 1,
				'plugins' => array(
				   		'table', 'fullscreen', 'media', 'preview', 'paste',
				   		'code', 'image', 'textcolor', 'jbimages'
				 ),
   		  
	      		'toolbar1' => array(
			      'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
			      'image', '|', 'link', '|', 'jbimages', '|', 'fullscreen', '|', 'preview'
			     ),         
				'upload_url' => $upload_url
			),
			'filters' => array(new Engine_Filter_Censor(), ),
		));

		// Init submit
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save',
			'type' => 'submit',
			'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_formButtonCancel.tpl',
						'class' => 'form element'
					)
				)),
		));
	}

	public function saveValues()
	{
		$values = $this -> getValues();
		$Ultimatenews = Engine_Api::_() -> getItem('ultimatenews_content', $values['content_id']);
		$Ultimatenews -> title = $values['title'];
		$Ultimatenews -> description = $values['description'];
		$Ultimatenews -> content = $values['content'];
		$Ultimatenews -> save();
	}

}
