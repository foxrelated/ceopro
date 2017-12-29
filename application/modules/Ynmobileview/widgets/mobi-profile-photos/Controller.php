<?php
class Ynmobileview_Widget_MobiProfilePhotosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> setNoRender();
		}

		$limit = $this -> _getParam('itemCountPerPage', 8);
		// Get subject and check auth
		$subject = Engine_Api::_() -> core() -> getSubject('user');
		$album_privacy = 1;
		$table = NULL;
		if (Engine_Api::_() -> hasModuleBootstrap("advalbum"))
		{
			$album_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('album.privacy', 0);
			$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
			$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
			$Name = $table -> info('name');
			$aName = $atable -> info('name');
			$select = $table -> select() -> from($Name) -> joinLeft($aName, "$aName.album_id = $Name.album_id", '') -> where("$aName.owner_id  = ?", $subject -> getIdentity()) -> where("search = ?", "1") -> order("$Name.comment_count DESC");
		}
		else
		if (Engine_Api::_() -> hasModuleBootstrap("album"))
		{
			$table = Engine_Api::_() -> getDbtable('photos', 'album');
			$atable = Engine_Api::_() -> getDbtable('albums', 'album');
			$Name = $table -> info('name');
			$aName = $atable -> info('name');
			$select = $table -> select() -> from($Name) -> joinLeft($aName, "$aName.album_id = $Name.album_id", '') -> where("$aName.owner_id  = ?", $subject -> getIdentity()) -> order("$Name.comment_count DESC");
		}
		else
		{
			$this -> setNoRender();
			return;
		}
		$photos = $table -> fetchAll($select);
		$temp = array();
		if ($album_privacy)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			foreach ($photos as $photo)
			{
				$album = $photo -> getParent();
				if (!$album)
				{
					continue;
				}
				if ($album -> authorization() -> isAllowed($viewer, 'view') && $album -> search = 1)
				{
					$temp[] = $photo;
				}
				if ($limit)
				{
					if (count($temp) >= $limit)
					{
						break;
					}
				}
			}
			$photos = $temp;
		}
		$this -> view -> arr_photos = $photos;
		// Add count to title if configured
		if ($this -> _getParam('titleCount', false) && count($photos) > 0)
		{
			$this -> _childCount = count($photos);
		}
	}

	public function getChildCount()
	{
		return $this -> _childCount;
	}

}
