<?php

class Advalbum_Model_DbTable_Photos extends Engine_Db_Table
{

	protected $_name = 'album_photos';

	protected $_rowClass = 'Advalbum_Model_Photo';

	public function getAllowedPhotos($select, $limit = null)
	{
		$album_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('album.privacy', 0);
		if ($limit && !$album_privacy)
		{
			$select -> limit($limit);
		}
		$photos = $this -> fetchAll($select);
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
				if ($album -> authorization() -> isAllowed($viewer, 'view'))
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
			return $temp;
		}
		return $photos;
	}

}
