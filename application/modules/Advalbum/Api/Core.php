<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
class Advalbum_Api_Core extends Core_Api_Abstract
{
	const IMAGE_WIDTH = 720;
	const IMAGE_HEIGHT = 720;

	// const FEATURE_WIDTH = 455;
	// const FEATURE_HEIGHT = 312;
//	const THUMB_WIDTH = 140;
//	const THUMB_HEIGHT = 105;
	const THUMB_WIDTH = 560;
	const THUMB_HEIGHT = 420;
	const SHORTEN_LENGTH_DEFAULT = 15;
	protected $_collectible_type = "advalbum_photo";
	public function createAlbum($params)
	{
		return $this -> _createItem("advalbum_album", $params);
	}

	public function createPhoto($params, $photo_file, $photo = array())
	{
		if ($photo_file instanceof Zend_Form_Element_File)
		{
			$file = $photo_file;
			$fileName = $photo_file -> getFileName();
		}
		else
		if ($photo_file instanceof Storage_Model_File)
		{
			$file = $photo_file -> temporary();
			$fileName = $photo_file -> name;
		}
		else
		if ($photo_file instanceof Core_Model_Item_Abstract && !empty($photo_file -> file_id))
		{
			$tmpRow = Engine_Api::_() -> getItem('storage_file', $photo_file -> file_id);
			$file = $tmpRow -> temporary();
			$fileName = $tmpRow -> name;
		}
		else
		if (is_array($photo_file) && !empty($photo_file['tmp_name']))
		{
			$file = $photo_file['tmp_name'];
			$fileName = $photo_file['name'];
		}
		else
		if (is_string($photo_file) && file_exists($photo_file))
		{
			$file = $photo_file;
			$fileName = $photo_file;
		}
		else
		{
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		if (!$fileName)
		{
			$fileName = $file;
		}

		if (!$photo)
		{
			$photo = Engine_Api::_() -> getDbtable('photos', 'advalbum') -> createRow();
			$photo -> setFromArray($params);
			$photo -> save();
		}

		if ($file instanceof Storage_Model_File)
		{
			$photo -> file_id = $file -> getIdentity();
		}

		else
		{
			// Get image info and resize
			$name = basename($file);
			$path = dirname($file);
			$extension = ltrim(strrchr($fileName, '.'), '.');
			$mainName = $path . '/m_' . $name . '.' . $extension;
			$thumbName = $path . '/t_' . $name . '.' . $extension;

			$image = Engine_Image::factory();
			$image -> open($file);
			$angle = 0;
			if(function_exists('exif_read_data'))
			{
				$exif = exif_read_data($file);

				if (!empty($exif['Orientation']))
				{
					switch($exif['Orientation'])
					{
						case 8 :
							$angle = 90;
							break;
						case 3 :
							$angle = 180;
							break;
						case 6 :
							$angle = -90;
							break;
					}
				}
			}
			if ($angle != 0)
				$image -> rotate($angle);
//			$image -> resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT) -> write($mainName) -> destroy();
			$image ->write($mainName) -> destroy();

			$settings = Engine_Api::_() -> getApi('settings', 'core');
			$thumbnailstyle = $settings -> getSetting('album_thumbnailstyle', 'crop');

			$image = new Advalbum_Api_Image();
			$image -> open($file);
			if ($angle != 0)
				$image -> rotate($angle);
			if ($thumbnailstyle == 'resize')
			{
				$image -> resize(self::THUMB_WIDTH, self::THUMB_HEIGHT);
			}
			else
			{
				$image -> resize_crop(self::THUMB_WIDTH, self::THUMB_HEIGHT);
			}

			$image -> write($thumbName);
			$image -> destroy();

			// Store photos
			$photo_params = array(
				'parent_type' => $photo -> getType(),
				'parent_id' => $photo -> getIdentity(),
				'user_id' => $photo -> owner_id,
				'name' => $fileName,
			);
			try
			{
				$photoFile = Engine_Api::_() -> storage() -> create($mainName, $photo_params);
				$thumbFile = Engine_Api::_() -> storage() -> create($thumbName, $photo_params);

			}
			catch ( Exception $e )
			{
				if ($e -> getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE)
				{
					echo $e -> getMessage();
					exit();
				}
			}

			$photoFile -> bridge($thumbFile, 'thumb.normal');
			// Remove temp files
			@unlink($mainName);
			@unlink($thumbName);

			$photo -> file_id = $photoFile -> file_id;
		}
		$photo -> save();
		return $photo;
	}

	public function getUserAlbums($user)
	{
		$table = Engine_Api::_() -> getItemTable('advalbum_album');
		return $table -> fetchAll(
				$table 	-> select() 
						-> where("`owner_type` = ?", "user")
						-> where("`owner_id` = ?", $user -> user_id)
						-> where("`virtual` = ?", 0)
		);
	}

	public function getAlbumSelect($options = array())
	{
		$albumTbl = Engine_Api::_() -> getItemTable('advalbum_album');
		$albumTblName = $albumTbl -> info('name');

		$userTbl = Engine_Api::_() -> getItemTable('user');
		$userTblName = $userTbl -> info('name');

		$select = $albumTbl -> select() -> from($albumTbl) -> setIntegrityCheck(false);
		$select -> joinLeft($userTblName, "$userTblName.user_id = $albumTblName.owner_id", "$userTblName.displayname as owner_title");

		// order by direction
		if (!empty($options['orderby']) && !empty($options['direction']))
		{
			$select -> order($options['orderby'] . ' ' . $options['direction']);
		}
		// featured
		if (isset($options['featured']) && is_numeric($options['featured']))
		{
			$select -> where("$albumTblName.featured = ?", $options['featured']);
		}
		// title
		if (!empty($options['title']))
		{
			$select -> where("$albumTblName.title LIKE ?", '%' . $options['title'] . '%');
		}
		// owner name
		if (!empty($options['owner_name']))
		{
			$select -> where("$userTblName.username LIKE ? OR $userTblName.displayname LIKE ?", '%' . $options['owner_name'] . '%');
		}
		if (!empty($options['owner']) && $options['owner'] instanceof Core_Model_Item_Abstract)
		{
			$select -> where("$albumTblName.owner_type = ?", $options['owner'] -> getType()) -> where("$albumTblName.owner_id = ?", $options['owner'] -> getIdentity()) -> order("$albumTblName.modified_date DESC");
		}

		if (!empty($options['search']) && is_numeric($options['search']))
		{
			$select -> where("$albumTblName.search = ?", $options['search']);
		}
		return $select;
	}

	public function getPhotoSelect($options = array())
	{
		$photoTbl = Engine_Api::_() -> getItemTable('advalbum_photo');
		$photoTblName = $photoTbl -> info('name');

		$userTbl = Engine_Api::_() -> getItemTable('user');
		$userTblName = $userTbl -> info('name');

		$albumTbl = Engine_Api::_() -> getItemTable('advalbum_album');
		$albumTblName = $albumTbl -> info('name');

		$featureTbl = Engine_Api::_() -> getItemTable('advalbum_feature');
		$featureTblName = $featureTbl -> info('name');

		$select = $photoTbl -> select() -> from($photoTbl) -> setIntegrityCheck(false);
		$select -> joinLeft($userTblName, "$userTblName.user_id = $photoTblName.owner_id", "$userTblName.displayname as owner_title") -> joinLeft($albumTblName, "$albumTblName.album_id = $photoTblName.album_id", "$albumTblName.title as album_title") -> joinLeft($featureTblName, "$featureTblName.photo_id = $photoTblName.photo_id", "photo_good");
		// order by direction
		if (!empty($options['orderby']) && !empty($options['direction']))
		{
			$select -> order($options['orderby'] . ' ' . $options['direction']);
		}
		// featured
		if (isset($options['featured']) && is_numeric($options['featured']))
		{
			$select -> where("$photoTblName.photo_id IN (SELECT photo_id FROM $featureTblName WHERE photo_good = 1)");
		}
		// title
		if (!empty($options['title']))
		{
			$select -> where("$photoTblName.title LIKE ?", '%' . $options['title'] . '%');
		}
		// owner name
		if (!empty($options['owner_name']))
		{
			$select -> where("$userTblName.username LIKE ? OR $userTblName.displayname LIKE ?", '%' . $options['owner_name'] . '%');
		}
		// title
		if (!empty($options['album_title']))
		{
			$select -> where("$albumTblName.title LIKE ?", '%' . $options['album_title'] . '%');
		}

		// echo $select;
		return $select;
	}

	public function getAlbumPaginator($options = array())
	{
		return Zend_Paginator::factory($this -> getAlbumSelect($options));
	}

	public function getPhotoPaginator($options = array())
	{
		return Zend_Paginator::factory($this -> getphotoSelect($options));
	}

	/**
	 * Returns a collection of all the categories in the album plugin
	 *
	 * @return Zend_Db_Table_Select
	 */
	public function getCategories($showAllCategories = 1)
	{
		$table = Engine_Api::_()->getDbTable('categories', 'advalbum');
		$select = $table->select()->order('category_name ASC');
		
		if (!$showAllCategories)
			$select->where('category_id != 0');

		$categories = $table->fetchAll($select);
		return $categories;
	}

	/**
	 * Returns a category item
	 *
	 * @param
	 *        	Int category_id
	 * @return Zend_Db_Table_Select
	 */
	public function getCategory($category_id)
	{
		return Engine_Api::_() -> getDbtable('categories', 'advalbum') -> find($category_id) -> current();
	}

	public function checkVersionSE()
	{
		$c_table = Engine_Api::_() -> getDbTable('modules', 'core');
		$c_name = $c_table -> info('name');
		$select = $c_table -> select() -> where("$c_name.name LIKE ?", 'core') -> limit(1);

		$row = $c_table -> fetchRow($select) -> toArray();
		$strVersion = $row['version'];
		$intVersion = ( int ) str_replace('.', '', $strVersion);
		return $intVersion >= 410 ? true : false;
	}

	public static function partialViewFullPath($partialTemplateFile)
	{
		$ds = DIRECTORY_SEPARATOR;
		return "application{$ds}modules{$ds}Advalbum{$ds}views{$ds}scripts{$ds}{$partialTemplateFile}";
	}

	public static function shortenText($text, $lengthLimit, $suffix = "...")
	{
		$view = $view = Zend_Registry::get('Zend_View');
		return $view -> string() -> truncate($text, $lengthLimit);
	}

	public static function defaultTooltipText($text)
	{
		$text = str_replace('"', '&#34;', $text);
		$text = str_replace("'", '&#39;', $text);
		return $text;
	}

	public function setRating($subject_id, $user_id, $rating, $type)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'advalbum');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.subject_id = ?', $subject_id) -> where($rName . '.user_id = ?', $user_id) -> where($rName . '.type = ?', $type);
		$rating_row = $table -> fetchRow($select);
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			if (!$rating_row) {
				// create rating
				$rating_row = $table->createRow();
				$rating_row->subject_id = $subject_id;
				$rating_row->user_id = $user_id;
				$rating_row->type = $type;
			}
			$rating_row->setFromArray(array(
				'rating' => $rating
			));
			$rating_row->save();
			
			$db->commit();
		}
		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function countRating($subject_id, $type)
	{
		$ratingTbl = Engine_Api::_() -> getDbTable('ratings', 'advalbum');
		$select = $ratingTbl -> select() -> where('subject_id = ?', $subject_id) -> where('type = ?', $type);
		$rows = $ratingTbl -> fetchAll($select);
		$total = count($rows);
		return $total;
	}

	public function getRating($subject_id, $type)
	{
		$ratingTbl = Engine_Api::_() -> getDbTable('ratings', 'advalbum');
		$rating_sum = $ratingTbl -> select() -> from($ratingTbl -> info('name'), new Zend_Db_Expr('SUM(rating)')) -> group('subject_id') -> where('subject_id = ?', $subject_id) -> where('type = ?', $type) -> query() -> fetchColumn(0);

		$total = $this -> countRating($subject_id, $type);

		if ($total)
		{
			$rating = $rating_sum / $total;
		}
		else
		{
			$rating = 0;
		}
		return $rating;
	}

	public function checkRated($subject_id, $user_id, $type)
	{
		$ratingTbl = Engine_Api::_() -> getDbTable('ratings', 'advalbum');
		$ratingName = $ratingTbl -> info('name');
		$select = $ratingTbl -> select() -> where('subject_id = ?', $subject_id) -> where('user_id = ?', $user_id) -> where('type = ?', $type);

		$row = $ratingTbl -> fetchRow($select);

		if ($row)
		{
			return true;
		}
		return false;
	}

	/**
	 * Support Bootstap3 theme
	 * @return array()
	 */
	public function getSupportedSliderContent()
	{
		$translate = new Zend_View_Helper_Translate;
		$return = array(
			'advalbum_featured_photos' => $translate -> translate('Featured Photos'),
			'advalbum_recent_photos' => $translate -> translate('Recent Photos'),
			'advalbum_most_view_photos' => $translate -> translate('Most Viewed Photos'),
			'advalbum_most_comment_photos' => $translate -> translate('Most Commented Photos'),
			'advalbum_most_liked_photos' => $translate -> translate('Most Liked Photos'),
			'advalbum_random_photos' => $translate -> translate('Random Photos'),
			'advalbum_month_photos' => $translate -> translate('This Month Photos'),
			'advalbum_week_photos' => $translate -> translate('This Week Photos'),
			'advalbum_day_photos' => $translate -> translate("Today's Photos"),
			'advalbum_featured_albums' => $translate -> translate('Featured Albums'),
			'advalbum_top_albums' => $translate -> translate('Top Albums'),
			'advalbum_recent_albums' => $translate -> translate('Recent Albums'),
			'advalbum_random_albums' => $translate -> translate('Random Albums'),
			'advalbum_profile_albums' => $translate -> translate('Profile Albums'),
			'advalbum_most_view_albums' => $translate -> translate('Most Viewed Albums'),
			'advalbum_most_like_albums' => $translate -> translate('Most Liked Albums'),
			'advalbum_most_comment_albums' => $translate -> translate('Most Commented Albums')
		);
		return $return;
	}

	/**
	 * @return Array/ Rowset of $type
	 */
	function getSliderContent($type, $params = null)
	{
		$method = '_getSlideContent_' . $type;
		if (method_exists($this, $method))
		{
			return $this -> {$method}($params);
		}
		return null;
	}

	/**
	 * Define all data providers
	 *
	 */
	function _getSlideContent_featured_photos($params)
	{
		$table = Engine_Api::_() -> getDbtable('features', 'advalbum');
		$Name = $table -> info('name');
		$tableP = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$NameP = $tableP -> info('name');
		$select = $tableP -> select() -> from($NameP);
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$featured_photos_max = $settings -> getSetting('album_featured_photos_max', 10);
		$featured_photos_min = $settings -> getSetting('album_featured_photos_min', 10);
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'])
		{
			$featured_photos_max = $params['itemCountPerPage'];
			$featured_photos_min = $params['itemCountPerPage'];
		}
		$select -> join($Name, "$NameP.photo_id = $Name.photo_id", '') -> where("photo_good  = ?", "1") -> order("Rand()");
		
		$arr_photos = $tableP -> getAllowedPhotos($select, $featured_photos_max);
		$photo_count = count($arr_photos);

		if ($photo_count < $featured_photos_min)
		{
			$select = $tableP -> select() -> from($NameP);
			$select -> order("Rand()") -> where("album_id > 0") -> where("$NameP.photo_id NOT IN (?)", $arr_photos);
			$photos_list = $tableP -> getAllowedPhotos($select, $featured_photos_min - $photo_count);
			$arr_photos = array_merge($arr_photos, $photos_list);
		}
		return $arr_photos;
	}

	/**
	 * _getContentItems_albums
	 */
	function _getContentItems_albums($order_by = 'creation_date', $params)
	{
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'] > 0)
		{
			$limit = $params['itemCountPerPage'];
		}
		else
			$limit = 6;
		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> order("$order_by DESC");

		return $table -> getAllowedAlbums($select, $limit);
	}

	function _getSlideContent_featured_albums($params)
	{
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'] > 0)
		{
			$limit = $params['itemCountPerPage'];
		}
		else
			$limit = 4;
		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> where('featured = ?', 1);

		return $table -> getAllowedAlbums($select, $limit);
	}

	function _getSlideContent_top_albums($params)
	{
		return $this -> _getContentItems_albums('like_count', $params);
	}

	function _getSlideContent_recent_albums($params)
	{
		return $this -> _getContentItems_albums('creation_date', $params);
	}

	function _getSlideContent_most_like_albums($params)
	{
		return $this -> _getContentItems_albums('like_count', $params);
	}

	function _getSlideContent_most_comment_albums($params)
	{
		return $this -> _getContentItems_albums('comment_count', $params);
	}

	function _getSlideContent_most_view_albums($params)
	{
		return $this -> _getContentItems_albums('view_count', $params);
	}

	function _getSlideContent_random_albums($params)
	{
		return $this -> _getContentItems_albums('RAND()', $params);
	}

	function _getSlideContent_profile_albums($params)
	{
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'] > 0)
		{
			$limit = $params['itemCountPerPage'];
		}
		else
			$limit = 16;
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return array();
		}
		// Get subject and check auth
		$subject = Engine_Api::_() -> core() -> getSubject();
		if (!$subject -> authorization() -> isAllowed($viewer, 'view'))
		{
			return array();
		}
		$select = $this -> getAlbumSelect(array(
			'owner' => $subject,
			'search' => 1,
			'limit' => $limit
		));
		return $table -> getAllowedPhotos($select, $limit);
	}

	/**
	 * _getContentItems_photos
	 */
	function _getContentItems_photos($order_by = 'creation_date', $params)
	{
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'] > 0)
		{
			$limit = $params['itemCountPerPage'];
		}
		else
			$limit = 8;
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1");
		if ($order_by != "RAND()")
			$select -> order("$Name." . $order_by . " DESC");
		else
			$select -> order($order_by . " DESC");
		return $table -> getAllowedPhotos($select, $limit);
	}

	function _getSlideContent_recent_photos($params)
	{
		return $this -> _getContentItems_photos('creation_date', $params);
	}

	function _getSlideContent_most_comment_photos($params)
	{
		return $this -> _getContentItems_photos('comment_count', $params);
	}

	function _getSlideContent_most_view_photos($params)
	{
		return $this -> _getContentItems_photos('view_count', $params);
	}

	function _getSlideContent_most_like_photos($params)
	{
		return $this -> _getContentItems_photos('like_count', $params);
	}

	function _getSlideContent_random_photos($params)
	{
		return $this -> _getContentItems_photos('RAND()', $params);
	}

	function _getSlideContent_month_photos($params)
	{
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'] > 0)
		{
			$limit = $params['itemCountPerPage'];
		}
		else
			$limit = 8;
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> where("YEAR($Name.creation_date) = YEAR(NOW())") -> where("MONTH($Name.creation_date) = MONTH(NOW())") -> order("$Name.view_count DESC");
		return $table -> getAllowedPhotos($select, $limit);
	}

	function _getSlideContent_day_photos($params)
	{
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'] > 0)
		{
			$limit = $params['itemCountPerPage'];
		}
		else
			$limit = 8;
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> where("YEAR($Name.creation_date) = YEAR(NOW())") -> where("MONTH($Name.creation_date) = MONTH(NOW())") -> where("DAY($Name.creation_date) = DAY(NOW())") -> order("$Name.view_count DESC");
		return $table -> getAllowedPhotos($select, $limit);
	}

	function _getSlideContent_week_photos($params)
	{
		if (isset($params['itemCountPerPage']) && $params['itemCountPerPage'] > 0)
		{
			$limit = $params['itemCountPerPage'];
		}
		else
			$limit = 8;
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$atable = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> where("YEAR($Name.creation_date) = YEAR(NOW())") -> where("WEEKOFYEAR($Name.creation_date) = WEEKOFYEAR(NOW())") -> order("$Name.view_count DESC");
		return $table -> getAllowedPhotos($select, $limit);
	}

}
