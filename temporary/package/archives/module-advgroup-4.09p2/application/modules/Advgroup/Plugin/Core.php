<?php
class Advgroup_Plugin_Core
{
	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getItemTable('group');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'group');
	}

	public function onUserDeleteBefore($group)
	{
		$payload = $group -> getPayload();
		if ($payload instanceof User_Model_User)
		{

			// Delete memberships
			$membershipApi = Engine_Api::_() -> getDbtable('membership', 'advgroup');
			foreach ($membershipApi->getMembershipsOf($payload) as $group)
			{
				$membershipApi -> removeMember($group, $payload);
			}

			// Delete officers
			$listItemTable = Engine_Api::_() -> getDbtable('ListItems', 'advgroup');
			$listItemSelect = $listItemTable -> select() -> where('child_id = ?', $payload -> getIdentity());
			foreach ($listItemTable->fetchAll($listItemSelect) as $listitem)
			{
				$list = Engine_Api::_() -> getItem('advgroup_list', $listitem -> list_id);
				if (!$list)
				{
					$listitem -> delete();
					continue;
				}
				if ($list -> has($payload))
				{
					$list -> remove($payload);
				}
			}
			// Delete albums
			$albumTable = Engine_Api::_() -> getItemTable('advgroup_album');
			$albumSelect = $albumTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($albumTable->fetchAll($albumSelect) as $groupAlbum)
			{
				$groupAlbum -> delete();
			}

			// Delete topics
			$topicTable = Engine_Api::_() -> getDbtable('topics', 'advgroup');
			$topicSelect = $topicTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($topicTable->fetchAll($topicSelect) as $topic)
			{
				$topic -> delete();
			}

			// Delete posts
			$postTable = Engine_Api::_() -> getDbtable('posts', 'advgroup');
			$postSelect = $postTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($postTable->fetchAll($postSelect) as $post)
			{
				$post -> delete();
			}

			//Delete polls
			$pollTable = Engine_Api::_() -> getDbTable('polls', 'advgroup');
			$pollSelect = $pollTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($pollTable->fetchAll($pollSelect) as $groupPoll)
			{
				$groupPoll -> delete();
			}

			//Delete reports
			$reportTable = Engine_Api::_() -> getDbTable('reports', 'advgroup');
			$reportSelect = $reportTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($reportTable->fetchAll($reportSelect) as $groupReport)
			{
				$groupReport -> delete();
			}

			//Delete all events
			if (Engine_Api::_() -> hasItemType('event'))
			{
				$eventTable = Engine_Api::_() -> getItemTable('event');
				$eventSelect = $eventTable -> select() -> where("parent_type = 'group' and user_id = ?", $payload -> getIdentity());
				foreach ($eventTable->fetchAll($eventSelect) as $groupEvent)
				{
					$groupEvent -> delete();
				}
			}

			//Delete all videos
			if (Engine_Api::_() -> hasItemType('video'))
			{
				$videoTable = Engine_Api::_() -> getItemTable('video');
				$videoSelect = $videoTable -> select() -> where("parent_type = 'group' and owner_id = ?", $payload -> getIdentity());
				foreach ($videoTable->fetchAll($videoSelect) as $groupVideo)
				{
					$groupVideo -> delete();
				}
			}

			// Delete invites
			$inviteTable = Engine_Api::_() -> getDbtable('invites', 'advgroup');
			$inviteSelect = $inviteTable -> select() -> where('recipient = ?', $payload -> email);
			foreach ($inviteTable->fetchAll($inviteSelect) as $invite)
			{
				$invite -> delete();
			}

			// Delete groups
			$groupTable = Engine_Api::_() -> getDbtable('groups', 'advgroup');
			$groupSelect = $groupTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($groupTable->fetchAll($groupSelect) as $group)
			{
				$group -> delete();
			}
		}
	}

	public function addActivity($event)
	{
		$payload = $event -> getPayload();
		$subject = $payload['subject'];
		$object = $payload['object'];

		// Only for object=event
		if ($object instanceof Advgroup_Model_Group && Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view'))
		{
			$event -> addResponse(array(
				'type' => 'group',
				'identity' => $object -> getIdentity()
			));
		}

	}

	public function getActivity($event)
	{
		// Detect viewer and subject
		$payload = $event -> getPayload();
		$user = null;
		$subject = null;
		if ($payload instanceof User_Model_User)
		{
			$user = $payload;
		}
		else
		if (is_array($payload))
		{
			if (isset($payload['for']) && $payload['for'] instanceof User_Model_User)
			{
				$user = $payload['for'];
			}
			if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract)
			{
				$subject = $payload['about'];
			}
		}
		if (null === $user)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if ($viewer -> getIdentity())
			{
				$user = $viewer;
			}
		}
		if (null === $subject && Engine_Api::_() -> core() -> hasSubject())
		{
			$subject = Engine_Api::_() -> core() -> getSubject();
		}

		// Get feed settings
		$content = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity.content', 'everyone');

		// Get event memberships
		if ($user)
		{
			$data = Engine_Api::_() -> getDbtable('membership', 'advgroup') -> getMembershipsOfIds($user);
			if (!empty($data) && is_array($data))
			{
				$event -> addResponse(array(
					'type' => 'group',
					'data' => $data,
				));
			}
		}
	}

	public function onItemCreateAfter($event)
	{
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$subject_id = $request -> getParam("subject_id", null);
			$subject_id = strrev($subject_id);
			$group_id = substr($subject_id, 0, -6);
			$group_id = strrev($group_id);
			$type = $request -> getParam("parent_type", null);

			if ($type == 'group')
			{
				if ($subject_id)
				{
					$type = $payload -> getType();

					switch ($type) {

						case 'music_playlist':
							$table = Engine_Api::_() -> getDbTable('mappings', 'advgroup');
							try {
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'group_id' => $group_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> owner_id,
							       'type' => 'music_playlist',
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();
							}
							catch (Exception $e) {

							}

							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.playlist.view';

							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'group_id' => $group_id,
								'type' => 'music',
							), 'group_extended', true);
							$_SESSION[$key] = $value;
							break;

						case 'mp3music_album':

							$table = Engine_Api::_() -> getDbTable('mappings', 'advgroup');
							try {

								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'group_id' => $group_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> user_id,
							       'type' => 'mp3music_album',
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();

							}
							catch (Exception $e) {
								die($e);
							}

							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.album.edit';

							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'group_id' => $group_id,
								'type' => 'mp3music',
							), 'group_extended', true);
							$_SESSION[$key] = $value;

							break;

						case 'ynmusic_album':

							$table = Engine_Api::_() -> getDbTable('mappings', 'advgroup');
							try {

								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'group_id' => $group_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> user_id,
							       'type' => 'ynmusic_album',
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();

							}
							catch (Exception $e) {
								die($e);
							}

							$group = Engine_Api::_() -> getItem('group', $group_id);
							$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							$viewer = Engine_Api::_() -> user() -> getViewer();
							$song = Engine_Api::_()->getItem('ynmusic_album', $payload -> getIdentity());
							$action = $activityApi->addActivity($viewer, $group, 'advgroup_ynmusic_album_create');
							if($action) {
								$activityApi->attachActivity($action, $song);
							}
							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.albums.edit';

							$value = $view -> url(array(
								'controller' => 'social-music',
								'action' => 'list',
								'group_id' => $group_id,
								'type' => 'album',
							), 'group_extended', true);
							$_SESSION[$key] = $value;

							break;

						case 'ynmusic_song':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'advgroup');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'group_id' => $group_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> user_id,
                                   'type' => 'ynmusic_song',
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();

                            }
                            catch (Exception $e) {
                                die($e);
                            }
                            break;

						case 'ynmusic_alonesong':
							$group = Engine_Api::_() -> getItem('group', $group_id);
							$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							$viewer = Engine_Api::_() -> user() -> getViewer();
							$song = Engine_Api::_()->getItem('ynmusic_alonesong', $payload -> getIdentity());
							$action = $activityApi->addActivity($viewer, $group, 'advgroup_ynmusic_song_create');
							if($action) {
								$activityApi->attachActivity($action, $song);
							}
						 	$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.songs.manage';
	                        $value = $view -> url(array(
	                            'controller' => 'social-music',
	                            'action' => 'list',
	                            'type' => 'song',
	                            'group_id' => $group_id,
	                        ), 'group_extended', true);
	                        $_SESSION[$key] = $value;
                            break;

                        case 'ynlistings_listing':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'advgroup');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'group_id' => $group_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'user_id' => $payload -> user_id,
                                   'type' => 'ynlistings_listing',
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();

								$listing = Engine_Api::_()->getItem('ynlistings_listing', $payload -> getIdentity());
								$viewer = Engine_Api::_() -> user() -> getViewer();
								$group = Engine_Api::_() -> getItem('group', $group_id);
								$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
								$action = $activityApi->addActivity($viewer, $group, 'advgroup_listing_create');
								if($action) {
									$activityApi->attachActivity($action, $listing);
								}
                            }
                            catch (Exception $e) {

                            }

                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.view';

                            $value = $view -> url(array(
                                'controller' => 'listings',
                                'action' => 'list',
                                'group_id' => $group_id,
                            ),  'group_extended', true);
                            $_SESSION[$key] = $value;
                            break;

						case 'folder':

							if($request -> getParam("view_folder"))
							{
								$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.folder.view';
								$value = $view -> url(array(
								'controller' => 'file',
								'action' => 'view-folder',
								'slug' => $request -> getParam("slug"),
								'folder_id' => $request -> getParam("parent_folder_id"),
								'parent_type' => 'group',
								'parent_id' => $group_id,
								),'group_viewsubfolder', true);
								$_SESSION[$key] = $value;
							}
							else
							{
								$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.manage';
								$value = $view -> url(array(
								'controller' => 'file',
								'action' => 'list',
								'group_id' => $group_id,
								), 'group_extended', true);
								$_SESSION[$key] = $value;
							}
							break;

						case 'video':
							$ynvideo_enabled = Engine_Api::_()->advgroup()->checkYouNetPlugin('ynvideo');
							if(!$ynvideo_enabled)
							{
								if(!is_numeric($group_id))
								{
									$group_id = $request -> getParam("subject_id");
								}
								$table = Engine_Api::_() -> getDbTable('mappings', 'advgroup');
								$viewer = Engine_Api::_() -> user() -> getViewer();
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'group_id' => $group_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $viewer -> getIdentity(),
							       'type' => 'video',
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
								    $row -> save();
							}
							if($ynvideo_enabled)
							{
								$group_id = $request -> getParam("subject_id");
							}

							$viewer = Engine_Api::_() -> user() -> getViewer();
							$video = Engine_Api::_()->getItem('video', $payload -> getIdentity());
							$video -> parent_type = 'group';
							$video -> parent_id = $group_id;
							$video -> save();

							if(!$ynvideo_enabled) //add activity for video default
							{
								$group = Engine_Api::_() -> getItem('group', $group_id);
								$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
								$action = $activityApi->addActivity($viewer, $group, 'advgroup_video_create');
								if($action) {
									$activityApi->attachActivity($action, $video);
								}
							}

							// Rebuild privacy
							$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
							foreach ($actionTable->getActionsByObject($video) as $action)
							{
								$actionTable -> resetActivityBindings($action);
							}

							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$key = 'advgroup_predispatch_url:' . $module_video . '.index.view';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'list',
								'group_id' => $group_id,
							), 'group_extended', true);
							$_SESSION[$key] = $value;
							break;
						case 'ynultimatevideo_video':
							$viewer = Engine_Api::_() -> user() -> getViewer();
							if(!is_numeric($group_id))
							{
								$group_id = $request -> getParam("subject_id");
							}
							$group = Engine_Api::_() -> getItem('group', $group_id);
							$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							$video = Engine_Api::_()->getItem('ynultimatevideo_video', $payload -> getIdentity());
							$action = $activityApi->addActivity($viewer, $group, 'advgroup_video_create');
							if($action) {
								$activityApi->attachActivity($action, $video);
							}
							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.view';
							$value = $view -> url(array(
								'controller' => 'ynultimatevideo',
								'action' => 'list',
								'group_id' => $group_id,
							), 'group_extended', true);
							$_SESSION[$key] = $value;
							break;
                        case 'ynvideochannel_video':
                            $viewer = Engine_Api::_() -> user() -> getViewer();
                            if(!is_numeric($group_id))
                            {
                                $group_id = $request -> getParam("subject_id");
                            }
                            $group = Engine_Api::_() -> getItem('group', $group_id);
                            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                            $video = Engine_Api::_()->getItem('ynvideochannel_video', $payload -> getIdentity());
                            $action = $activityApi->addActivity($viewer, $group, 'advgroup_video_create');
                            if($action) {
                                $activityApi->attachActivity($action, $video);
                            }
                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.video.detail';
                            $value = $view -> url(array(
                                'controller' => 'ynvideochannel',
                                'action' => 'list',
                                'group_id' => $group_id,
                            ), 'group_extended', true);
                            $_SESSION[$key] = $value;
                            break;
					}
				}
			}
		}
	}

	public function onItemUpdateAfter($event)
	{
		$payload = $event -> getPayload();

		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$view = Zend_Registry::get('Zend_View');
			$subject_id = $request -> getParam("subject_id", null);
			$subject_id = strrev($subject_id);
			$group_id = substr($subject_id, 0, -6);
			$group_id = strrev($group_id);
			if($request -> getParam("group_id"))
				$group_id = $request -> getParam("group_id");
			$type = $request -> getParam("parent_type", null);

			if ($type == 'group')
			{
				if ($subject_id || $group_id)
				{
					$type = $payload -> getType();

					switch ($type) {
						case 'music_playlist':
							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.playlist.view';
							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'group_id' => $group_id,
								'type' => 'music',
							), 'group_extended', true);
							$_SESSION[$key] = $value;
							break;

						case 'mp3music_album':

							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.album.manage';

							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'group_id' => $group_id,
								'type' => 'mp3music',
							), 'group_extended', true);
							$_SESSION[$key] = $value;

							break;

						case 'ynmusic_album':
                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.albums.view';
                            $value = $view -> url(array(
                                'controller' => 'social-music',
                                'action' => 'list',
                                'type' => 'album',
                                'group_id' => $group_id,
                            ), 'group_extended', true);
                            $_SESSION[$key] = $value;

                            break;

						case 'ynmusic_song':
                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.songs.view';
                            $value = $view -> url(array(
                                'controller' => 'social-music',
                                'action' => 'list',
                                'type' => 'song',
                                'group_id' => $group_id,
                            ), 'group_extended', true);
                            $_SESSION[$key] = $value;

                            break;

						case 'folder':

							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.folder.view';

							$value = $view -> url(array(
								'controller' => 'file',
								'action' => 'list',
								'group_id' => $group_id,
							), 'group_extended', true);
							$_SESSION[$key] = $value;

							break;

					case 'video':
							$ynvideo_enabled = Engine_Api::_()->advgroup()->checkYouNetPlugin('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
								$group_id = $request -> getParam("subject_id", null);
							}
							else {
								$module_video = "video";
							}
							$key = 'advgroup_predispatch_url:' . $module_video . '.index.manage';
								$value = $view -> url(array(
									'controller' => 'video',
									'action' => 'manage',
									'subject' => 'group_'.$group_id,
								), 'group_extended', true);
								$_SESSION[$key] = $value;
							break;

                    case 'ynlistings_listing':
                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'listings',
                                'action' => 'list',
                                'group_id' => $group_id,
                            ), 'group_extended', true);
                            $_SESSION[$key] = $value;
                            break;
					case 'event':
                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'event',
                                'action' => 'manage',
                                'group_id' => $group_id,
                            ), 'group_extended', true);
                            $_SESSION[$key] = $value;
                            break;
						case 'ynultimatevideo_video':
							if(!is_numeric($group_id))
							{
								$group_id = $request -> getParam("subject_id");
							}
							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.view';
							$value = $view -> url(array(
								'module' => 'advgroup',
								'controller' => 'ynultimatevideo',
								'action' => 'manage',
								'group_id' => $group_id,
							), 'group_extended', true);
							$_SESSION[$key] = $value;
							break;
                        case 'ynvideochannel_video':
                            if(!is_numeric($group_id))
                            {
                                $group_id = $request -> getParam("subject_id");
                            }
                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.manage-videos';
                            $value = $view -> url(array(
                                'module' => 'advgroup',
                                'controller' => 'ynvideochannel',
                                'action' => 'manage',
                                'group_id' => $group_id,
                            ), 'group_extended', true);
                            $_SESSION[$key] = $value;
                            break;
					}
				}
			}
		}
	}

	public function onItemDeleteAfter($event)
	{
		$payload = $event -> getPayload();

		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$subject_id = $request -> getParam("subject_id", null);
			$subject_id = strrev($subject_id);
			$group_id = substr($subject_id, 0, -6);
			$group_id = strrev($group_id);
			if($request -> getParam("group_id"))
				$group_id = $request -> getParam("group_id");
			$type = $request -> getParam("parent_type", null);
			$case = $request -> getParam("case", null);
			if ($type == 'group')
			{
				if ($subject_id || $group_id)
				{
					switch ($case) {
					case 'folder':
							if($request -> getParam("view_folder"))
							{
								$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.file.delete';
								$value = $view -> url(array(
								'controller' => 'file',
								'action' => 'view-folder',
								'slug' => $request -> getParam("slug"),
								'folder_id' => $request -> getParam("parent_folder_id"),
								'parent_type' => 'group',
								'parent_id' => $group_id,
								),'group_viewsubfolder', true);
								$_SESSION[$key] = $value;
							}
							else
							{
								$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.manage';

								$value = $view -> url(array(
									'controller' => 'file',
									'action' => 'list',
									'group_id' => $group_id,
								), 'group_extended', true);
								$_SESSION[$key] = $value;

							}
							break;

					case 'video':
								$ynvideo_enabled = Engine_Api::_()->advgroup()->checkYouNetPlugin('ynvideo');
								if($ynvideo_enabled)
								{
									$module_video = "ynvideo";
								}
								else {
									$module_video = "video";
								}
								$key = 'advgroup_predispatch_url:' . $module_video . '.index.manage';
								$value = $view -> url(array(
									'controller' => 'video',
									'action' => 'manage',
									'subject' => 'group_'.$group_id,
								), 'group_extended', true);
								$_SESSION[$key] = $value;
								break;

                        case 'ynlistings_listing':
                            $key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'listings',
                                'action' => 'list',
                                'subject' => 'group_'.$group_id,
                            ), 'group_extended', true);
                            $_SESSION[$key] = $value;
                            break;
						case 'ynultimatevideo_video':
							$key = 'advgroup_predispatch_url:' . $request -> getParam('module') . '.index.manage';
							$value = $view -> url(array(
								'module' => 'advgroup',
								'controller' => 'ynultimatevideo',
								'action' => 'manage',
								'group_id' => $group_id,
							), 'group_extended', true);
							Zend_Registry::get('Zend_Log')->log(print_r($value,true), 7);
							$_SESSION[$key] = $value;
							break;
						case 'ynvideochannel_video':
							$key = 'advgroup_predispatch_url:' . $request->getParam('module') . '.index.manage-videos';
							$value = $view->url(array(
								'controller' => 'ynvideochannel',
								'action' => 'manage',
								'group_id' => $group_id,
							), 'group_extended', true);
							$_SESSION[$key] = $value;
							break;
					}
				}
			}
		}
	}
}
