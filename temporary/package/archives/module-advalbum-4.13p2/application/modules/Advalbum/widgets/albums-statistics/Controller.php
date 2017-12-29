<?php
class Advalbum_Widget_AlbumsStatisticsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
  		$params = $this -> _getAllParams();
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
       $table  = Engine_Api::_()->getDbTable('photos', 'advalbum');
       $select = new Zend_Db_Select($table->getAdapter());
       $select->from($table->info('name'), 'COUNT(*) AS count')
		->where('album_id>0');
       $this->view->count_photos =  $select->query()->fetchColumn(0);
       
       $table  = Engine_Api::_()->getDbTable('albums', 'advalbum');
       $select = new Zend_Db_Select($table->getAdapter());
       $select->from($table->info('name'), 'COUNT(*) AS count');
       $this->view->count_albums =  $select->query()->fetchColumn(0);
  }
}