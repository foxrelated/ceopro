<?php

class Ultimatenews_Form_WidgetCategory extends Engine_Form
{
	public function init()
	{
		$title = new Engine_Form_Element_Text("title");
		$title->setLabel('Title')
		->setValue('');
		
		//$feeds_per_page
		$categories_per_page = new Engine_Form_Element_Text("categories_per_page");
		$categories_per_page->setLabel('How many feeds for a page?')
		->setValue('3');
		$categories_per_page->setValidators(array(
			array('NotEmpty', true),
        	array('Int', true),
        	array('GreaterThan',true,array(0))
		));
		
		$wide = new Engine_Form_Element_Text("wide");
		$wide->setLabel('How many articles with wide layout on every feed?')
		->setValue('3');
		$wide->setValidators(array(
			array('NotEmpty', true),
        	array('Int', true),
        	array('GreaterThan',true,array(0))
		));
		
		$narrow = new Engine_Form_Element_Text("narrow");
		$narrow->setLabel('How many articles with narrow layout on every feed?')
		->setValue('7');
		$narrow->setValidators(array(
			array('NotEmpty', true),
        	array('Int', true),
        	array('GreaterThan',true,array(0))
		));
			
		$this->addElement($title);
		$this->addElement($categories_per_page);
		$this->addElement($wide);
		$this->addElement($narrow);
	}
}

