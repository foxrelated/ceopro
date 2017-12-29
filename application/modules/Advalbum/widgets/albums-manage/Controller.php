<?php
class Advalbum_Widget_AlbumsManageController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		//admin widget setting
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity()) {
			return $this->setNoRender();
		}

		// Prepare data
		$user = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getItemTable('advalbum_album');

		$search = "";
		$sort = "";
		$category_id = "";
		$color = "";

		$itemPerPage = $this->_getParam('number');

		// search by color
		$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$albumCover = array();
		$albumIds = array();
		if(isset($p['color']))
		{
			$color = $p['color'];
		}
		if ($color != "")
		{
			$photoColorTbl = Engine_Api::_()->getDbTable("photocolors", "advalbum");
			$photos = $photoColorTbl->getPhotoByColor($color);
			if (count($photos))
			{
				foreach ($photos as $photo)
				{
					if (!in_array($photo->album_id, $albumIds))
					{
						$albumIds[] = $photo->album_id;
					}
					$albumCover["$photo->album_id"] = $photo->photo_id;
				}
			}

			$virtualPhotos = $photoColorTbl->getVirtualPhotoByColor($color);
			if (count($virtualPhotos))
			{
				foreach ($virtualPhotos as $photo)
				{
					if (!in_array($photo->album_id, $albumIds))
					{
						$albumIds[] = $photo->album_id;
					}
					$albumCover["$photo->album_id"] = $photo->photo_id;
				}
			}
		}

		// get the value from query string
		$new_category_id = "";
		if (isset($_GET['category_id']))
		{
			$new_category_id = $_GET['category_id'];
		}
		$new_sort = "";
		if (isset($_GET['sort']))
		{
			$new_sort = $_GET['sort'];
		}

		$pos = strpos($_SERVER['REQUEST_URI'], '/albums/listing/');
		if ($pos !== FALSE)
		{
			$params = substr($_SERVER['REQUEST_URI'], $pos + 1);
			$arr = explode('/', $params);
			for ($i = 0; $i < count($arr) - 1; $i++)
			{
				if ($arr[$i] == 'category_id')
				{
					$new_category_id = $arr[$i + 1];
				}
				if ($arr[$i] == 'sort')
				{
					$new_sort = $arr[$i + 1];
				}
				if ($arr[$i] == 'page')
				{
					$new_page = $arr[$i + 1];
				}
			}
		}
		if ($new_category_id)
		{
			$category_id = $new_category_id;
		}
		if ($new_sort)
		{
			$sort = $new_sort;
		}
		if ($_POST)
		{
			$params = $_POST;
			if(isset($_POST['sort']))
				$sort = $_POST['sort'];
			if(isset($_POST['search']))
				$search = $_POST['search'];
			if(isset($_POST['category_id']))
				$category_id = $_POST['category_id'];
			if(isset($_POST['color']))
				$color = $_POST['color'];
		}

		// query database
		switch($sort)
		{
			case 'popular' :
				$order = 'view_count';
				break;
			case 'most_commented' :
				$order = 'comment_count';
				break;
			case 'top' :
				$order = 'like_count';
				break;
			case 'recent' :
			default :
				$order = 'modified_date';
				break;
		}
		// Prepare data
		$table = Engine_Api::_() -> getItemTable('advalbum_album');
		if (!in_array($order, $table -> info('cols')))
		{
			$order = 'modified_date';
		}

		$select = $table -> select() -> order($order . ' DESC');

		if ($category_id)
			$select -> where("category_id = ?", $category_id);

		if ($search)
		{
			$select -> where('title LIKE ? OR description LIKE ?', '%' . $search . '%');
		}

		if ($p['color'] != "" || $color != '')
		{
			if (count($albumIds))
			{
				$select -> where("album_id IN (?)", $albumIds);
			}
			else
			{
				$select -> where("album_id IN (?)", "");
			}
		}

		$select -> where('owner_id = ?', $user -> getIdentity()) -> order('modified_date DESC');
		$paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($itemPerPage);
		$paginator -> setCurrentPageNumber($p['page']);
		foreach ($paginator as $album)
		{
			$albumId = $album->getIdentity();
			if (!empty($albumCover) && isset($albumCover["$albumId"]))
			{
				$album->photo_id = $albumCover["$albumId"];
			}
		}

		$this -> view -> canCreate = Engine_Api::_() -> authorization() -> isAllowed('advalbum_album', null, 'create');
		$this -> view -> paginator = $paginator;
	}
}
