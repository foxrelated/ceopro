<?php
class Advalbum_Plugin_Menus
{
	public function canCreateAlbums()
	{
		// Must be logged in
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity())
		{
			return false;
		}

		// Must be able to create albums
		if (!Engine_Api::_() -> authorization() -> isAllowed('advalbum_album', $viewer, 'create'))
		{
			return false;
		}

		return true;
	}

	public function canViewAlbums()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Must be able to view albums
		if (!Engine_Api::_() -> authorization() -> isAllowed('advalbum_album', $viewer, 'view'))
		{
			return false;
		}

		return true;
	}

	public function onMenuInitialize_UserProfileAdvalbum($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$label = "My Photos";
		if (!$viewer -> isSelf($subject))
		{
			$label = "Photos";
		}
		if ($subject -> authorization() -> isAllowed($viewer, 'view'))
		{
			return array(
				'label' => $label,
				'icon' => 'application/modules/Advalbum/externals/images/album_manage.png',
				'route' => 'album_general',
				'params' => array(
					'controller' => 'index',
					'action' => 'browsebyuser',
					'id' => ($viewer -> getGuid(false) == $subject -> getGuid(false) ? null : $subject -> getIdentity()),
				)
			);
		}

		return false;
	}

}
