<?php
class Advalbum_Widget_AlbumsCategoriesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
  	$params = $this -> _getAllParams();
  	$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
       $table = Engine_Api::_()->getDbtable('categories', 'advalbum');
       $Name = $table->info('name');
       $select = $table->select()->from($Name);
      $this->view->categories =  $table->fetchAll($select);
  }
}