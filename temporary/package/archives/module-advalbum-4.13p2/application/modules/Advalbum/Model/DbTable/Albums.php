<?php
class Advalbum_Model_DbTable_Albums extends Engine_Db_Table
{
	protected $_name = 'album_albums';
	protected $_rowClass = 'Advalbum_Model_Album';

	public function getSpecialAlbum(User_Model_User $user, $type)
	{
		if( !in_array($type, array('wall', 'profile', 'message', 'blog','forum','group','event')) ) {
			throw new Advalbum_Model_Exception('Unknown special album type');
		}

		$select = $this -> select() -> where('owner_type = ?', $user -> getType()) -> where('owner_id = ?', $user -> getIdentity()) -> where('type = ?', $type) -> order('album_id ASC') -> limit(1);

		$album = $this -> fetchRow($select);

		// Create wall photos album if it doesn't exist yet
		if (null === $album)
		{
			$translate = Zend_Registry::get('Zend_Translate');

			$album = $this -> createRow();
			$album -> owner_type = 'user';
			$album -> owner_id = $user -> getIdentity();
			$album -> title = $translate -> _(ucfirst($type) . ' Photos');
			$album -> type = $type;
			$album -> save();
		}

		return $album;
	}

	public function getAllowedAlbums($select, $limit = null)
	{
		$album_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('album.privacy', 0);
		if ($limit && !$album_privacy)
		{
			$select -> limit($limit);
		}
		$albums = $this -> fetchAll($select);
		$temp = array();
		if ($album_privacy)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			foreach ($albums as $album)
			{
				if (!$album)
				{
					continue;
				}
				if ($album -> authorization() -> isAllowed($viewer, 'view'))
				{
					$temp[] = $album;
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
		return $albums;
	}

	public function getVirtualAlbumsAssoc($user)
	{
		$select = $this->select()
			->where("`virtual` = ?", 1)
			->where("`owner_type` = ?","user")
			->where("`owner_id` = ?", $user->getIdentity());
		$albums = $this->fetchAll($select);
		$result = array();
		foreach ($albums as $album)
		{
			$result[$album->getIdentity()] = $album->getTitle();
		}
		return $result;
	}
}
