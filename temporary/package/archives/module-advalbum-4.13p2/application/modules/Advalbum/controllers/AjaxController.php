<?php
class Advalbum_AjaxController extends Core_Controller_Action_Standard
{
	public function mostCommentedAlbumAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;

		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> order("comment_count DESC");

		$this -> view -> comments = $arr_albums = $table -> getAllowedAlbums($select, $limit);
		$css = "global_form_box";
		$album_listing_id = 'album_listing_most_commented';
		$no_albums_message = $this -> view -> translate('There has been no album in this category yet.');
		$html = $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_albumlist.tpl'), array(
			'arr_albums' => $arr_albums,
			'album_listing_id' => $album_listing_id,
			'no_albums_message' => $no_albums_message,
			'short_title' => 1,
			'css' => $css
		));
		echo $html;
	}

	public function recentPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 8;
		$rand = $this -> _getParam('rand');
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> order("$Name.creation_date DESC");

		$css = "global_form_box";
		$this -> view -> newests = $arr_photos = $table -> getAllowedPhotos($select, $limit);
		$photo_listing_id = 'photo_listing_newest';
		$no_photos_message = $this -> view -> translate('There has been no photo in this category yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function mostCommentedPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 8;
		$rand = $this -> _getParam('rand');
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$aName.album_id = $Name.album_id", '') -> where("search = ?", "1") -> order("$Name.comment_count DESC");

		$this -> view -> comments = $arr_photos = $table -> getAllowedPhotos($select, $limit);
		$css = "global_form_box";
		$photo_listing_id = 'photo_listing_most_commented';
		$no_photos_message = $this -> view -> translate('There has been no photo in this category yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function mostLikedAlbumsAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;

		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> order('like_count DESC');

		$arr_albums = $table -> getAllowedAlbums($select, $limit);
		$css = "global_form_box";
		$album_listing_id = 'album_listing_most_liked';
		$no_albums_message = $this -> view -> translate('There has been no album in this category yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_albumlist.tpl'), array(
			'arr_albums' => $arr_albums,
			'album_listing_id' => $album_listing_id,
			'no_albums_message' => $no_albums_message,
			'short_title' => 1,
			'css' => $css
		));
	}

	public function mostLikedPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$rand = $this -> _getParam('rand');
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 8;

		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$aName.album_id = $Name.album_id", '') -> where("$aName.search = ?", "1") -> where("$Name.like_count >= ?", 0) -> order("$Name.like_count DESC");
		$arr_photos = $table -> getAllowedPhotos($select, $limit);
		$css = "global_form_box";
		$photo_listing_id = 'photo_listing_most_liked';
		$no_photos_message = $this -> view -> translate('There has been no photo in this category yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function mostViewedAlbumsAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;

		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> order("view_count DESC");

		$this -> view -> views = $arr_albums = $table -> getAllowedAlbums($select, $limit);
		$css = "global_form_box";
		$album_listing_id = 'album_listing_most_viewed';
		$no_albums_message = $this -> view -> translate('There has been no album in this category yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_albumlist.tpl'), array(
			'arr_albums' => $arr_albums,
			'album_listing_id' => $album_listing_id,
			'no_albums_message' => $no_albums_message,
			'short_title' => 1,
			'css' => $css
		));
	}

	public function recentAlbumsAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;

		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> order("creation_date DESC");

		$this -> view -> newests = $arr_albums = $table -> getAllowedAlbums($select, $limit);
		$css = "global_form_box";
		$album_listing_id = 'album_listing_recent';
		$no_albums_message = $this -> view -> translate('Nobody has uploaded an album yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_albumlist.tpl'), array(
			'arr_albums' => $arr_albums,
			'album_listing_id' => $album_listing_id,
			'no_albums_message' => $no_albums_message,
			'short_title' => 1,
			'css' => $css
		));
	}

	public function randomPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;
		$rand = $this -> _getParam('rand');
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> order("RAND()");

		$css = "global_form_box";
		$this -> view -> newests = $arr_photos = $table -> getAllowedPhotos($select, $limit);
		$photo_listing_id = 'photo_listing_random';
		$no_photos_message = $this -> view -> translate('Nobody has uploaded a photo yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function randomAlbumsAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;

		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> order("RAND()");

		$this -> view -> newests = $arr_albums = $table -> getAllowedAlbums($select, $limit);
		$css = "global_form_box";
		$album_listing_id = 'album_listing_random';
		$no_albums_message = $this -> view -> translate('There has been no album in this category yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_albumlist.tpl'), array(
			'arr_albums' => $arr_albums,
			'album_listing_id' => $album_listing_id,
			'no_albums_message' => $no_albums_message,
			'short_title' => 1,
			'css' => $css
		));
	}

	public function mostViewedPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 8;
		$rand = $this -> _getParam('rand');
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$aName.album_id = $Name.album_id", '') -> where("$aName.search = ?", "1") -> order("$Name.view_count DESC");
		$this -> view -> views = $arr_photos = $table -> getAllowedPhotos($select, $limit);
		$css = "global_form_box";
		$photo_listing_id = 'photo_listing_most_viewed';
		$no_photos_message = $this -> view -> translate('There has been no photo in this category yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function thisMonthPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 8;
		$rand = $this -> _getParam('rand');
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> order("$Name.view_count DESC") -> limit($limit) -> where("YEAR($Name.creation_date) = YEAR(NOW())") -> where("MONTH($Name.creation_date) = MONTH(NOW())");
		$this -> view -> month_pics = $arr_photos = $table -> getAllowedPhotos($select, $limit);

		$css = "global_form_box";
		$photo_listing_id = 'photo_listing_this_month';
		$no_photos_message = $this -> view -> translate('Nobody has uploaded new photos in this month yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function thisWeekPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 8;
		$rand = $this -> _getParam('rand');
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> where("YEAR($Name.creation_date) = YEAR(NOW())") -> order("$Name.view_count DESC") -> where("WEEKOFYEAR($Name.creation_date) = WEEKOFYEAR(NOW())");
		$this -> view -> week_pics = $arr_photos = $table -> getAllowedPhotos($select, $limit);

		$css = "global_form_box";
		$photo_listing_id = 'photo_listing_this_week';
		$no_photos_message = $this -> view -> translate('Nobody has uploaded new photos in this week yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function todayPhotosAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 8;
		$rand = $this -> _getParam('rand');
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> order("$Name.view_count DESC") -> where("YEAR($Name.creation_date) = YEAR(NOW())") -> where("MONTH($Name.creation_date) = MONTH(NOW())") -> where("DAY($Name.creation_date) = DAY(NOW())");
		$this -> view -> today_pics = $arr_photos = $table -> getAllowedPhotos($select, $limit);

		$css = "global_form_box";
		$photo_listing_id = 'photo_listing_to_day';
		$no_photos_message = $this -> view -> translate('Nobody has uploaded new photos today yet.');
		echo $this -> view -> partial(Advalbum_Api_Core::partialViewFullPath('_photolist.tpl'), array(
			'arr_photos' => $arr_photos,
			'photo_listing_id' => $photo_listing_id,
			'no_photos_message' => $no_photos_message,
			'css' => $css,
			'rand' => $rand
		));
	}

	public function prepareSpecialAlbumAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$type = $this -> _getParam('type', 'wall');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$album = $table -> getSpecialAlbum($viewer, $type);
		if ($type != 'message') {
			$auth = Engine_Api::_() -> authorization() -> context;
			$auth -> setAllowed($album, 'everyone', 'view', true);
			$auth -> setAllowed($album, 'everyone', 'comment', true);
		}
		$status = $album->getIdentity() ? true : false;
		$this->view->status = $status;
		$this->view->album_id = $album->getIdentity();
	}
}
