<?php
class Advalbum_Widget_PhotosListingController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		//admin widget setting
		$params = $this -> _getAllParams();
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
		$mode_grid = $mode_pinterest = 1;
		$mode_enabled = array();
		$view_mode = 'list';
		if (isset($params['mode_grid']))
		{
			$mode_grid = $params['mode_grid'];
		}
		if ($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		if (isset($params['mode_pinterest']))
		{
			$mode_pinterest = $params['mode_pinterest'];
		}
		if ($mode_pinterest)
		{
			$mode_enabled[] = 'pinterest';
		}
		if (isset($params['view_mode']))
		{
			$view_mode = $params['view_mode'];
		}
		if ($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}

		$this -> view -> mode_enabled = $mode_enabled;

		$class_mode = "ynalbum-grid-view";
		switch ($view_mode)
		{
			case 'pinterest' :
				$class_mode = "ynalbum-pinterest-view";
				break;
			default :
				$class_mode = "ynalbum-grid-view";
				break;
		}
		if (isset($params['number']))
		{
			$itemPerPage = $params['number'];
		}
		else
		{
			$itemPerPage = 10;
		}
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1");
		$get = $request -> getParams();
		// query database
		switch($get['sort'])
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
		$select -> order("{$Name}.{$order} DESC");
		if (!empty($get['search']))
		{
			$select -> where("$Name.title LIKE ? OR $Name.description LIKE ?", '%' . $get['search'] . '%');
		}
		if (!empty($get['category_id']))
		{
			$select -> where("$aName.category_id = ?", $get['category_id']);
		}
		if ($get['color'] != "")
		{
			$cTable = Engine_Api::_() -> getDbtable('photocolors', 'advalbum');
			$cName = $cTable -> info('name');
			$select -> joinLeft($cName, "$cName.photo_id = $Name.photo_id", '') -> where("$cName.color_title = ?", $get['color']);
		}
		unset($get['action']);
		unset($get['module']);
		unset($get['controller']);
		unset($get['rewrite']);
		$this -> view -> formValues = $get;
		$arr_photos = $table -> getAllowedPhotos($select);
		$paginator = Zend_Paginator::factory($arr_photos);
		$paginator -> setItemCountPerPage($itemPerPage);
		$paginator -> setCurrentPageNumber($request -> getParam('page', 1));
		$this -> view -> paginator = $paginator;
		$photo_listing_id = 'advalbum_photos_listing';
		$no_photos_message = $this -> view -> translate('There is no photo.');
		$this -> view -> html_full = $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $paginator,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'short_title' => 1,
			'no_bottom_space' => 1,
			'class_mode' => $class_mode,
			'view_mode' => $view_mode,
			'css' => (!empty($params['title'])) ? "" : 'no_title',
			'mode_enabled' => $mode_enabled,
		));
	}

}
