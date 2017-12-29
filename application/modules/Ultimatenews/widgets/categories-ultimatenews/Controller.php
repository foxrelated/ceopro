<?php
class Ultimatenews_Widget_CategoriesUltimatenewsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
   {
       $select = Engine_Api::_()->ultimatenews()->getCategoryparentsSelect(array('category_active'=> 1));
       $table = Engine_Api::_()->getItemTable('ultimatenews_categoryparent');
       $this->view->categories = $categories = $table->fetchAll($select); 
   }
}