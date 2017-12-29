<?php
class Advalbum_Widget_PhotosOfAlbumController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_() -> core() -> hasSubject('advalbum_album')) {
			return $this -> setNoRender();
		}

		$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$params = $this->_getAllParams();
		$params = array_merge($params, $p);

		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> playlist = $playlist = $this -> _getParam('playlist', 0);
		if($this -> _getParam('slideshow', 0))
		{
			$this -> view -> body_class = 'slideshow-active';
		}
		$this -> view -> slideshow = $slideshow = $this -> _getParam('slideshow', 0);
		$this -> view -> rating_count = Engine_Api::_() -> advalbum() -> countRating($album -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		$this -> view -> is_rated = Engine_Api::_() -> advalbum() -> checkRated($album -> getIdentity(), $viewer -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);

		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$tableName = $table -> info('name');
		if ($album->virtual)
		{
			$virtualPhotoTbl = Engine_Api::_() -> getDbtable('virtualphotos', 'advalbum');
			$virtualPhotoTblName = $virtualPhotoTbl -> info('name');
			$db = $virtualPhotoTbl->getAdapter();
			$virtualPhotoIds = $db
				-> select()
				-> from($virtualPhotoTblName)
				-> where("album_id = ? ", $album -> getIdentity())
				-> query()
				-> fetchAll(Zend_Db::FETCH_COLUMN, 1);
			$virtualPhotoIds = array_unique($virtualPhotoIds);
			if (!count($virtualPhotoIds))
			{
				$virtualPhotoIds = "";
			}
			$select = $table -> select() -> from($tableName) -> where("photo_id IN (?)", $virtualPhotoIds) -> order("order");
			$photo_list = $table -> fetchAll($select);
		}
		else
		{
			$select = $table -> select() -> from($tableName) -> where("album_id = ?", $album -> album_id) -> order("order");
			$photo_list = $table -> fetchAll($select);
		}

		$session = new Zend_Session_Namespace('mobile');

		if ($slideshow || $playlist)
		{
			// get the photos (all)
			if ($playlist)
			{
				$this -> _helper -> layout -> disableLayout();
				$this -> view -> html_full = $this -> view -> partial('_playlist.tpl', array(
					'album' => $album,
					'photo_list' => $photo_list
				));
				$response = Zend_Controller_Front::getInstance() -> getResponse();
				$response -> setHeader('Content-Type', 'text/xml', TRUE);
			}
			else
			{
				$this -> _helper -> layout -> disableLayout();
				$this -> view -> html_full = $this -> view -> partial('_slideshow.tpl', array(
					'album' => $album,
					'photo_list' => $photo_list,
					'effect' => $this -> _getParam('effect')
				));
			}
		}
		else
		{
			// Prepare params
			$page = isset($params['page']) ? $params['page'] : 1;
			$number = isset($params['number']) ? $params['number'] : 12;

			// Prepare data
			if ($album->virtual)
			{
				$this -> view -> paginator = $paginator = Zend_Paginator::factory($photo_list);
			}
			else
			{
				$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
			}
			$paginator -> setItemCountPerPage($number);
			$paginator -> setCurrentPageNumber($page);

			// Do other stuff

			$sortable = 0;
			$photo_listing_id = 'photo_listing_in_album_' . $album -> getIdentity();
			$no_photos_message = $this -> view -> translate('There is no photo in this album.');

			// view modes
			$mode_grid = $mode_pinterest = 1;
			$mode_enabled = array();
			$view_mode = 'grid';

			if(isset($params['mode_grid']))
			{
				$mode_grid = $params['mode_grid'];
			}
			if($mode_grid)
			{
				$mode_enabled[] = 'grid';
			}
			if(isset($params['mode_pinterest']))
			{
				$mode_pinterest = $params['mode_pinterest'];
			}
			if($mode_pinterest)
			{
				$mode_enabled[] = 'pinterest';
			}
			if(isset($params['view_mode']))
			{
				$view_mode = $params['view_mode'];
			}
			if($mode_enabled && !in_array($view_mode, $mode_enabled))
			{
				$view_mode = $mode_enabled[0];
			}

			$class_mode = "ynalbum-grid-view";
			switch ($view_mode)
			{
				case 'pinterest':
					$class_mode = "ynalbum-pinterest-view";
					break;
				default:
					$class_mode = "ynalbum-grid-view";
					break;
			}
			$this -> view -> html_photo_list = $this -> view -> partial('_photolist.tpl', array(
				'paginator' => $paginator,
				'photo_listing_id' => $photo_listing_id,
				'sortable' => $sortable,
				'no_photos_message' => $no_photos_message,
				'untitled' => 1,
				'no_author_info' => 1,
				'show_title_info' => 1,
				'no_bottom_space' => 1,
				'album' => $album,
				'class_mode' => $class_mode,
				'view_mode' => $view_mode,
				'mode_enabled' => $mode_enabled,
				'manage' => 1,
			));
			if ($session -> mobile)
			{
				$this -> view -> html_mobile_slideshow = $this -> view -> partial('_m_slideshow.tpl', 'advalbum', array('photo_list' => $paginator));
			}
		}
	}
}
