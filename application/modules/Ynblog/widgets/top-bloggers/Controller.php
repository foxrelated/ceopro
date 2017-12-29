<?php
class Ynblog_Widget_TopBloggersController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
        //Get number of bloggers display
        if($this->_getParam('max') != '' && $this->_getParam('max') >= 0){
            $limitBloggers = $this->_getParam('max');
        }else{
            $limitBloggers = 12;
        }
        
       // Get bloggers
       $table = Engine_Api::_()->getItemTable('blog');
       $Name = $table->info('name');
       $select = $table->select()->from($Name)
        ->group("$Name.owner_id")
        ->order("Count($Name.owner_id) DESC")
        ->limit($limitBloggers);
      $this->view->bloggers =  $table->fetchAll($select);
  }
}