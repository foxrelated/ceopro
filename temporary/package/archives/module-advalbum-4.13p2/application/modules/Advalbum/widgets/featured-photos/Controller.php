<?php
class Advalbum_Widget_FeaturedPhotosController extends Engine_Content_Widget_Abstract
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
		if ($this -> _getParam('max') != '' && $this -> _getParam('max') >= 0)
		{
			$max = $this -> _getParam('max');
		}
		else {
			$max = 8;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_() -> getDbtable('features', 'advalbum');
		$Name = $table -> info('name');
		$tableP = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$NameP = $tableP -> info('name');
		$select = $table -> select() -> from($Name);
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$featured_photos_max = $max;
		$select -> join($NameP, "$NameP.photo_id = $Name.photo_id", '') -> join("engine4_users", "engine4_users.user_id = $NameP.owner_id", '') -> where("photo_good  = ?", "1") -> order("Rand()") -> limit($featured_photos_max * 3);

		$photos_list = $table -> fetchAll($select);
		$session = new Zend_Session_Namespace('mobile');
		$arr_photos = array();
	
		$photo_ids = ",";
		$photo_count = 0;
		foreach ($photos_list as $item)
		{
			$photo = Engine_Api::_() -> getItem('advalbum_photo', $item -> photo_id);
			if (!$photo)
			{
				continue;
			}
			$album = $photo -> getParent();
			if ($album -> authorization() -> isAllowed($viewer, 'view') && $album -> type != "profile" && $album -> search = 1)
			{
				$arr_photos[] = $photo;
				$photo_count++;
				$photo_ids .= "{$photo->getIdentity()},";
				if ($photo_count >= $featured_photos_max)
					break;
			}
		}

        if (count($arr_photos) <= 0)
        {
            $this -> setNoRender();
        }

		$this -> view -> items = $arr_photos;
	}
}
