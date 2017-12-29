<?php
class Ynadvsearch_Widget_YnAlbumSearchController extends Engine_Content_Widget_Abstract
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
		$check_advalbum = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('advalbum');
		if(!$check_advalbum)
		{
			return $this -> setNoRender();
		}
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_main');

		// Get quick navigation
		$this -> view -> quickNavigation = $quickNavigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_quick');
		$search_form = $this -> view -> search_form = new Advalbum_Form_Search();
		$search_form -> setMethod('get');
		$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		if ($p['color'] != "")
		{
			$this -> view -> color = $p['color'];
		}
		
		$search_form -> isValid($p);
		$session_AdvAlbumSearch = new Zend_Session_Namespace('AdvAlbumSearch');
		$search = "";
		$sort = "";
		$category_id = "";
		$color = "";

		$pos = strpos($_SERVER['HTTP_REFERER'], '/albums/listing');
		$from_outside = ($pos === FALSE);
		$from_outside = FALSE;
		$listing_home = 1;
		$pos2 = strpos($_SERVER['REQUEST_URI'], '/albums/listing');
		if ($pos2 !== FALSE)
		{
			$tmp = substr($_SERVER['REQUEST_URI'], $pos2 + strlen('/albums/listing'));
			$tmp = trim($tmp, " /");
			if ($tmp)
			{
				$listing_home = 0;
			}
		}
		$listing_home = 0;
		$searching = FALSE;
		$pos = strpos($_SERVER['REQUEST_URI'], '/albumsearch/1');
		if ($pos !== FALSE)
		{
			$searching = TRUE;
		}

		if ($from_outside || $listing_home || !$searching)// outside or menu home
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
				if (isset($session_AdvAlbumSearch -> color))
				{
					$color = trim($session_AdvAlbumSearch -> color);
				}
			}
		}

		// get the value from query string
		$new_category_id = "";
		if (isset($p['category_id']))
		{
			$new_category_id = $p['category_id'];
		}
		$new_sort = "";
		if (isset($p['sort']))
		{
			$new_sort = $p['sort'];
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
		
		if ($color)
			$this -> view -> color = $color;
		if ($search)
			$search_form -> getElement('search') -> setValue($search);
		if ($sort)
			$search_form -> getElement('sort') -> setValue($sort);
		if ($category_id)
		{
			if ($search_form -> getElement('category_id'))
				$search_form -> getElement('category_id') -> setValue($category_id);
		}
		$colorTbl = Engine_Api::_() -> getDbTable("colors", "advalbum");
		$this -> view -> colors = $colors = $colorTbl -> fetchAll($colorTbl -> select());
	}

}
