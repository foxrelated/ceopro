<?php
class Advalbum_Widget_AlbumsTopMembersController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
  		$params = $this -> _getAllParams();
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 9;
		
  		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
	   
       $table = Engine_Api::_()->getDbtable('albums', 'advalbum');
       $Name = $table->info('name');
       $select = $table->select()->from($Name)
        	->group("$Name.owner_id")
       		->order("Count($Name.owner_id) DESC")-> limit($limit);
      $this->view->members =  $table->fetchAll($select);
  }
}