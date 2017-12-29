<?php
class Advalbum_Widget_AlbumsListingController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		//admin widget setting
		$widget_params = $this -> _getAllParams();
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if($widget_params['nomobile'] == 1)
			{
				return $this->setNoRender();
			}
		}
		
		$mode_list = $mode_grid = $mode_pinterest = 1;
		$mode_enabled = array();
		$view_mode = 'list';
		
		if(isset($widget_params['mode_list']))
		{
			$mode_list = $widget_params['mode_list'];
		}
		if($mode_list)
		{
			$mode_enabled[] = 'list';
		}
		if(isset($widget_params['mode_grid']))
		{
			$mode_grid = $widget_params['mode_grid'];
		}
		if($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		if(isset($widget_params['mode_pinterest']))
		{
			$mode_pinterest = $widget_params['mode_pinterest'];
		}
		if($mode_pinterest)
		{
			$mode_enabled[] = 'pinterest';
		}
		if(isset($widget_params['view_mode']))
		{
			$view_mode = $widget_params['view_mode'];
		}			
		if($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}		
			
		$this -> view -> mode_enabled = $mode_enabled;
	
		$class_mode = "ynalbum-list-view";
		switch ($view_mode) 
		{
			case 'grid':
				$class_mode = "ynalbum-grid-view";
				break;
			case 'pinterest':
				$class_mode = "ynalbum-pinterest-view";
				break;
			default:
				$class_mode = "ynalbum-list-view";
				break;
		}
								
		$search = "";
		$sort = "";
		$category_id = "";
		$color = "";

		$session_AdvAlbumSearch = new Zend_Session_Namespace('AdvAlbumSearch');

		$pos = strpos($_SERVER['HTTP_REFERER'], '/albums/listing');
		$from_outside = ($pos === FALSE);
		$listing_home = 1;
		$pos2 = isset($_SERVER['REQUEST_URI']) ? strpos($_SERVER['REQUEST_URI'], '/albums/listing') : FALSE;
		if ($pos2 !== FALSE)
		{
			$tmp = substr($_SERVER['REQUEST_URI'], $pos2 + strlen('/albums/listing'));
			$tmp = trim($tmp, " /");
			if ($tmp)
			{
				$listing_home = 0;
			}
		}

		if ($from_outside || $listing_home)// outside or menu home
		{
			$session_AdvAlbumSearch -> search = "";
			$session_AdvAlbumSearch -> sort = "";
			$session_AdvAlbumSearch -> category_id = "";
			$session_AdvAlbumSearch -> color = "";
		}
		else
		{
			// in the listing, get value from session
			if ($session_AdvAlbumSearch)
			{
				if (isset($session_AdvAlbumSearch -> search))
				{
					$search = trim($session_AdvAlbumSearch -> search);
				}
				if (isset($session_AdvAlbumSearch -> sort))
				{
					$sort = trim($session_AdvAlbumSearch -> sort);
				}
				if (isset($session_AdvAlbumSearch -> category_id))
				{
					$category_id = (int)($session_AdvAlbumSearch -> category_id);
				}
				if (isset($session_AdvAlbumSearch -> category_id))
				{
					$color = trim($session_AdvAlbumSearch -> color);
				}
			}
		}
		if(isset($widget_params['number']))
		{
			$itemPerPage = $widget_params['number'];
		}
		else {
			$itemPerPage = 10;
		}
		
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

		$new_page = 0;
		if (isset($_GET['page']))
		{
			$new_page = (int)($_GET['page']);
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
		$page = $new_page;
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

		// save to session
		$session_AdvAlbumSearch -> search = $search;
		$session_AdvAlbumSearch -> sort = $sort;
		$session_AdvAlbumSearch -> category_id = $category_id;
		$session_AdvAlbumSearch -> color = $color;
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

		$select = $table -> select() -> where("search = 1") -> order($order . ' DESC');

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
		
		if ($page <= 0)
		{
			$page = 1;
		}
		$this -> view -> canCreate = Engine_Api::_() -> authorization() -> isAllowed('advalbum', null, 'create');
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		$album_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('album.privacy', 0);
		if ($album_privacy)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$albums = $table -> fetchAll($select);
			$allowedAlbums = array();
			foreach ($albums as $a)
			{
				if ($a -> authorization() -> isAllowed($viewer, 'view'))
				{
					array_push($allowedAlbums, $a);
				}
			}
			$paginator = Zend_Paginator::factory($allowedAlbums);
		}
		else
		{
			$paginator = Zend_Paginator::factory($select);
		}
		$paginator -> setItemCountPerPage($itemPerPage);
		$paginator -> setCurrentPageNumber($page);
		foreach ($paginator as $album)
		{
			$albumId = $album->getIdentity();
			if (!empty($albumCover) && isset($albumCover["$albumId"]))
			{
				$album->photo_id = $albumCover["$albumId"];
			}
		}
		$this -> view -> paginator = $paginator;
		$album_listing_id = 'advalbum_albums_listing';
		$no_albums_message = $this -> view -> translate('There is no album.');
		$this -> view -> html_full = $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_albumlist.tpl'), array(
			'paginator' => $paginator,
			'album_listing_id' => $album_listing_id,
			'no_albums_message' => $no_albums_message,
			'short_title' => 1,
			'no_bottom_space' => 1,
			'css' => (!empty($widget_params['title']))?"":'no_title',
			'class_mode' => $class_mode,
			'view_mode' => $view_mode,
			'mode_enabled' => $mode_enabled,	
		));
	}

}
