<?php
class Advalbum_Widget_FeaturedAlbumsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$params = $this -> _getAllParams();
		
		if(empty($params['title']))
		{
			$this -> view-> no_title = "no_title";
		}
		
		$limit = $this -> _getParam('number', 8);

		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}

		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where('featured = ?', 1);

		$arr_albums = $table -> getAllowedAlbums($select, $limit);
		if (count($arr_albums) <= 0)
		{
			$this -> setNoRender();
		}
		$this -> view -> arr_albums = $arr_albums;
	}

}
