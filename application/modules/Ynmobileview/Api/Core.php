<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_Api_Core extends Core_Api_Abstract
{
	public function isMobile()
	{
		// No UA defined?
		if (!isset($_SERVER['HTTP_USER_AGENT']))
		{
			return false;
		}

		// Windows is (generally) not a mobile OS
		if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') && false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone os'))
		{
			return false;
		}

		// Sends a WAP profile header
		if (isset($_SERVER['HTTP_PROFILE']) || isset($_SERVER['HTTP_X_WAP_PROFILE']))
		{
			return true;
		}

		// Accepts WAP as a valid type
		if (isset($_SERVER['HTTP_ACCEPT']) && false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml'))
		{
			return true;
		}

		// Is Opera Mini
		if (isset($_SERVER['ALL_HTTP']) && false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini'))
		{
			return true;
		}

		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', $_SERVER['HTTP_USER_AGENT']))
		{
			return true;
		}

		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ',
			'acs-',
			'alav',
			'alca',
			'amoi',
			'audi',
			'avan',
			'benq',
			'bird',
			'blac',
			'blaz',
			'brew',
			'cell',
			'cldc',
			'cmd-',
			'dang',
			'doco',
			'eric',
			'hipt',
			'inno',
			'ipaq',
			'java',
			'jigs',
			'kddi',
			'keji',
			'leno',
			'lg-c',
			'lg-d',
			'lg-g',
			'lge-',
			'maui',
			'maxo',
			'midp',
			'mits',
			'mmef',
			'mobi',
			'mot-',
			'moto',
			'mwbp',
			'nec-',
			'newt',
			'noki',
			'oper',
			'palm',
			'pana',
			'pant',
			'phil',
			'play',
			'port',
			'prox',
			'qwap',
			'sage',
			'sams',
			'sany',
			'sch-',
			'sec-',
			'send',
			'seri',
			'sgh-',
			'shar',
			'sie-',
			'siem',
			'smal',
			'smar',
			'sony',
			'sph-',
			'symb',
			't-mo',
			'teli',
			'tim-',
			'tosh',
			'tsm-',
			'upg1',
			'upsi',
			'vk-v',
			'voda',
			'wap-',
			'wapa',
			'wapi',
			'wapp',
			'wapr',
			'webc',
			'winw',
			'winw',
			'xda ',
			'xda-'
		);

		if (in_array($mobile_ua, $mobile_agents))
		{
			return true;
		}

		return false;
	}

	public function getActivity(User_Model_User $user, array $params = array(), $sort)
	{
		$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
		// Proc args
		extract($this -> _getInfo($params));
		// action_id, limit, min_id, max_id

		// Prepare main query
		$streamTable = Engine_Api::_() -> getDbtable('stream', 'activity');
		$db = $streamTable -> getAdapter();
		$union = new Zend_Db_Select($db);

		// Prepare action types
		$masterActionTypes = Engine_Api::_() -> getDbtable('actionTypes', 'activity') -> getActionTypes();
		$mainActionTypes = array();

		// Filter out types set as not displayable
		foreach ($masterActionTypes as $type)
		{
			if ($type -> displayable & 4)
			{
				$mainActionTypes[] = $type -> type;
			}
		}

		// Filter types based on user request
		if (isset($showTypes) && is_array($showTypes) && !empty($showTypes))
		{
			$mainActionTypes = array_intersect($mainActionTypes, $showTypes);
		}
		else
		if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes))
		{
			$mainActionTypes = array_diff($mainActionTypes, $hideTypes);
		}

		// Nothing to show
		if (empty($mainActionTypes))
		{
			return null;
		}
		// Show everything
		else
		if (count($mainActionTypes) == count($masterActionTypes))
		{
			$mainActionTypes = true;
		}
		// Build where clause
		else
		{
			$mainActionTypes = "'" . join("', '", $mainActionTypes) . "'";
		}

		// Prepare sub queries
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('getActivity', array('for' => $user, ));
		$responses = (array)$event -> getResponses();

		if (empty($responses))
		{
			return null;
		}

		foreach ($responses as $response)
		{
			if (empty($response))
				continue;

			$select = $streamTable -> select() -> from($streamTable -> info('name'), 'action_id') -> where('target_type = ?', $response['type']);

			if (empty($response['data']))
			{
				// Simple
				$select -> where('target_id = ?', 0);
			}
			else
			if (is_scalar($response['data']) || count($response['data']) === 1)
			{
				// Single
				if (is_array($response['data']))
				{
					list($response['data']) = $response['data'];
				}
				$select -> where('target_id = ?', $response['data']);
			}
			else
			if (is_array($response['data']))
			{
				// Array
				$select -> where('target_id IN(?)', (array)$response['data']);
			}
			else
			{
				// Unknown
				continue;
			}

			// Add action_id/max_id/min_id
			if (null !== $action_id)
			{
				$select -> where('action_id = ?', $action_id);
			}
			else
			{
				if (null !== $min_id)
				{
					$select -> where('action_id >= ?', $min_id);
				}
				else
				if (null !== $max_id)
				{
					$select -> where('action_id <= ?', $max_id);
				}
			}

			if ($mainActionTypes !== true)
			{
				$select -> where('type IN(' . $mainActionTypes . ')');
			}

			// Add order/limit
			$select -> order('action_id DESC') -> limit($limit);

			// Add to main query
			$union -> union(array('(' . $select -> __toString() . ')'));
			// (string) not work before PHP 5.2.0
		}

		// Finish main query
		$union -> order('action_id DESC') -> limit($limit);

		// Get actions
		$actions = $db -> fetchAll($union);

		// No visible actions
		if (empty($actions))
		{
			return null;
		}

		// Process ids
		$ids = array();
		foreach ($actions as $data)
		{
			$ids[] = $data['action_id'];
		}
		$ids = array_unique($ids);

		$select = $actionTable -> select() -> where('action_id IN(' . join(',', $ids) . ')');

		if ($sort == 'top')
		{
			$select -> order('like_count DESC');
			$select -> order('comment_count DESC');
		}
		else
		{
			$select -> order('action_id DESC');
		}
		// Finally get activity
		return $actionTable -> fetchAll($select -> limit($limit));
	}

	public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user, array $params = array(), $sort)
	{
		$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
		// Proc args
		extract($this -> _getInfo($params));
		// action_id, limit, min_id, max_id

		// Prepare main query
		$streamTable = Engine_Api::_() -> getDbtable('stream', 'activity');
		$db = $streamTable -> getAdapter();
		$union = new Zend_Db_Select($db);

		// Prepare action types
		$masterActionTypes = Engine_Api::_() -> getDbtable('actionTypes', 'activity') -> getActionTypes();
		$subjectActionTypes = array();
		$objectActionTypes = array();

		// Filter types based on displayable
		foreach ($masterActionTypes as $type)
		{
			if ($type -> displayable & 1)
			{
				$subjectActionTypes[] = $type -> type;
			}
			if ($type -> displayable & 2)
			{
				$objectActionTypes[] = $type -> type;
			}
		}

		// Filter types based on user request
		if (isset($showTypes) && is_array($showTypes) && !empty($showTypes))
		{
			$subjectActionTypes = array_intersect($subjectActionTypes, $showTypes);
			$objectActionTypes = array_intersect($objectActionTypes, $showTypes);
		}
		else
		if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes))
		{
			$subjectActionTypes = array_diff($subjectActionTypes, $hideTypes);
			$objectActionTypes = array_diff($objectActionTypes, $hideTypes);
		}

		// Nothing to show
		if (empty($subjectActionTypes) && empty($objectActionTypes))
		{
			return null;
		}

		if (empty($subjectActionTypes))
		{
			$subjectActionTypes = null;
		}
		else
		if (count($subjectActionTypes) == count($masterActionTypes))
		{
			$subjectActionTypes = true;
		}
		else
		{
			$subjectActionTypes = "'" . join("', '", $subjectActionTypes) . "'";
		}

		if (empty($objectActionTypes))
		{
			$objectActionTypes = null;
		}
		else
		if (count($objectActionTypes) == count($masterActionTypes))
		{
			$objectActionTypes = true;
		}
		else
		{
			$objectActionTypes = "'" . join("', '", $objectActionTypes) . "'";
		}

		// Prepare sub queries
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('getActivity', array(
			'for' => $user,
			'about' => $about,
		));
		$responses = (array)$event -> getResponses();

		if (empty($responses))
		{
			return null;
		}

		foreach ($responses as $response)
		{
			if (empty($response))
				continue;

			// Target info
			$select = $streamTable -> select() -> from($streamTable -> info('name'), 'action_id') -> where('target_type = ?', $response['type']);

			if (empty($response['data']))
			{
				// Simple
				$select -> where('target_id = ?', 0);
			}
			else
			if (is_scalar($response['data']) || count($response['data']) === 1)
			{
				// Single
				if (is_array($response['data']))
				{
					list($response['data']) = $response['data'];
				}
				$select -> where('target_id = ?', $response['data']);
			}
			else
			if (is_array($response['data']))
			{
				// Array
				$select -> where('target_id IN(?)', (array)$response['data']);
			}
			else
			{
				// Unknown
				continue;
			}

			// Add action_id/max_id/min_id
			if (null !== $action_id)
			{
				$select -> where('action_id = ?', $action_id);
			}
			else
			{
				if (null !== $min_id)
				{
					$select -> where('action_id >= ?', $min_id);
				}
				else
				if (null !== $max_id)
				{
					$select -> where('action_id <= ?', $max_id);
				}
			}

			// Add order/limit
			$select -> order('action_id DESC') -> limit($limit);

			// Add subject to main query
			$selectSubject = clone $select;
			if ($subjectActionTypes !== null)
			{
				if ($subjectActionTypes !== true)
				{
					$selectSubject -> where('type IN(' . $subjectActionTypes . ')');
				}
				$selectSubject -> where('subject_type = ?', $about -> getType()) -> where('subject_id = ?', $about -> getIdentity());
				$union -> union(array('(' . $selectSubject -> __toString() . ')'));
				// (string) not work before PHP 5.2.0
			}

			// Add object to main query
			$selectObject = clone $select;
			if ($objectActionTypes !== null)
			{
				if ($objectActionTypes !== true)
				{
					$selectObject -> where('type IN(' . $objectActionTypes . ')');
				}
				$selectObject -> where('object_type = ?', $about -> getType()) -> where('object_id = ?', $about -> getIdentity());
				$union -> union(array('(' . $selectObject -> __toString() . ')'));
				// (string) not work before PHP 5.2.0
			}
		}

		// Finish main query
		$union -> order('action_id DESC') -> limit($limit);

		// Get actions
		$actions = $db -> fetchAll($union);

		// No visible actions
		if (empty($actions))
		{
			return null;
		}

		// Process ids
		$ids = array();
		foreach ($actions as $data)
		{
			$ids[] = $data['action_id'];
		}
		$ids = array_unique($ids);

		$select = $actionTable -> select() -> where('action_id IN(' . join(',', $ids) . ')');

		if ($sort == 'top')
		{
			$select -> order('like_count DESC');
			$select -> order('comment_count DESC');
		}
		else
		{
			$select -> order('action_id DESC');
		}

		// Finally get activity
		return $actionTable -> fetchAll($select -> limit($limit));
	}

	protected function _getInfo(array $params)
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$args = array(
			'limit' => $settings -> getSetting('activity.length', 20),
			'action_id' => null,
			'max_id' => null,
			'min_id' => null,
			'showTypes' => null,
			'hideTypes' => null,
		);

		$newParams = array();
		foreach ($args as $arg => $default)
		{
			if (!empty($params[$arg]))
			{
				$newParams[$arg] = $params[$arg];
			}
			else
			{
				$newParams[$arg] = $default;
			}
		}

		return $newParams;
	}

	public function getMusicRichContect($item, $play = false)
	{
		if ($item -> getType() == 'music_playlist_song')
		{
			$album = $item -> getParent();
			$songs = $this -> getservicesongs($album, $item);
		}
		else
		{
			$album = $item;
			$songs = $this -> getservicesongs($album);
		}
		$videoEmbedded = '';
		$desc = strip_tags($album -> description);
		$desc = "<div class='music_desc'>" . (Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc) . "</div>";
		$zview = Zend_Registry::get('Zend_View');
		$zview -> album = $album;
		$zview -> songs = $songs;
		$zview -> play = $play;
		$videoEmbedded = $desc . $zview -> render('application/modules/Ynmobileview/views/scripts/_Player.tpl');

		return $videoEmbedded;
	}

	//get list song for player
	public function getServiceSongs($album = null, $song = null)
	{
		if ($song)
		{
			$musiclist = array($song);
		}
		else
		{
			$musiclist = $album -> getSongs();
		}
		$songs = array();
		foreach ($musiclist as $index => $music)
		{
			$songs[$index]['filepath'] = $music -> getFilePath();

			$user = Engine_Api::_() -> user() -> getViewer();
			$user_id = $user -> getIdentity();

			$string = $album -> getOwner() -> getTitle();

			$songs[$index]['artist'] = $string;
			$songs[$index]['albumname'] = $album -> title;
			$songs[$index]['order'] = $music -> order;
			$songs[$index]['song_id'] = $music -> song_id;
			$songs[$index]['title'] = $music -> title;
			$songs[$index]['play_count'] = $music -> play_count;
		}
		return $songs;
	}

	public function setUserPhoto($user, $photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
			$fileName = $file;
		}
		else
		if ($photo instanceof Storage_Model_File)
		{
			$file = $photo -> temporary();
			$fileName = $photo -> name;
		}
		else
		if ($photo instanceof Core_Model_Item_Abstract && !empty($photo -> file_id))
		{
			$tmpRow = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
			$file = $tmpRow -> temporary();
			$fileName = $tmpRow -> name;
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
			$fileName = $photo['name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
			$fileName = $photo;
		}
		else
		{
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		if (!$fileName)
		{
			$fileName = $file;
		}

		$name = basename($file);
		$extension = ltrim(strrchr(basename($fileName), '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => $user -> getType(),
			'parent_id' => $user -> getIdentity(),
			'user_id' => $user -> getIdentity(),
			'name' => basename($fileName),
		);

		// Save
		$filesTable = Engine_Api::_() -> getDbtable('files', 'storage');

		// Resize image (main)
		$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file) -> write($mainPath) -> destroy();
		// Store
		$iMain = $filesTable -> createFile($mainPath, $params);
		// Remove temp files
		@unlink($mainPath);

		// Update row
		$user -> modified_date = date('Y-m-d H:i:s');
		$user -> cover_id = $iMain -> file_id;
		$user -> save();

		return $user;
	}

}
