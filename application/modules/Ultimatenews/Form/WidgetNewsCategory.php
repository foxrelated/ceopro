<?php
class Ultimatenews_Form_WidgetNewsCategory extends Engine_Form
{
	public function init()
	{
		$title = new Engine_Form_Element_Text("title");
		$title->setLabel('Title')
		->setValue('News');
		
		$categories = Engine_Api::_()->ultimatenews()->getAllCategoryparents(array('category_active' => 1));
		$category_ele = new Engine_Form_Element_Select("category_id");
		$category_ele->setLabel('Category');
		foreach ($categories as $category)
        {
            $category_ele->addMultiOption($category['category_id'], $category['category_name']);
        }
		$wide = new Engine_Form_Element_Text("wide");
		$wide->setLabel('How many articles for wide?')
		->setValue('3');
		
		$narrow = new Engine_Form_Element_Text("narrow");
		$narrow->setLabel('How many articles for narrow?')
		->setValue('7');
		
		$this->addElement($title);
		$this->addElement($category_ele);
		$this->addElement($wide);
		$this->addElement($narrow);
	}
}
