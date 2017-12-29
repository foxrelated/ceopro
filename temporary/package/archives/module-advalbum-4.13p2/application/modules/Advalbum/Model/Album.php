<?php
class Advalbum_Model_Album extends Core_Model_Item_Collection
{
	protected $_parent_type = 'user';

	protected $_owner_type = 'user';

	protected $_parent_is_owner = true;

	protected $_searchColumns = array(
		'title',
		'description',
		'search'
	);

	protected $_collectible_type = "advalbum_photo";

	protected $_collection_column_name = "album_id";

	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'album_specific',
			'reset' => true,
			'album_id' => $this -> getIdentity(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getPhotoUrl($type = null)
	{
		if ($this -> count() <= 0)
		{
			return 'application/modules/Advalbum/externals/images/photoalbum.jpg';
		}
		if (empty($this -> photo_id))
		{
			// This should probaby be done on delete
			if (!$this->virtual)
			{
				$photo = $this -> getFirstCollectible();
			}
			else
			{
				$photo = $this -> getFirstVirtualPhoto();
			}
			
			if ($photo)
			{
				$this -> photo_id = $photo -> photo_id;
				$this -> save();
				$file_id = $photo -> file_id;
			}
			else
			{
				return;
			}
		}
		else
		{
			$photo = Engine_Api::_() -> getItem('advalbum_photo', $this -> photo_id);
			if (!$photo)
			{
				$this -> photo_id = 0;
				$this -> save();
				return;
			}
			else
			{
				$file_id = $photo -> file_id;
			}
		}

		if (!$file_id)
		{
			return;
		}

		$file = Engine_Api::_() -> getApi('storage', 'storage') -> get($file_id, $type);
		if (!$file)
		{
			return;
		}

		return $file -> map();
	}

	public function incrementViews()
	{
		$this -> views++;
		$this -> save();
	}

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the subscribe handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function subscribes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('subscribes', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function count()
	{
		if ($this->virtual)
		{
			$table = Engine_Api::_() -> getDbTable('virtualphotos', 'advalbum');
		}
		else
		{
			$table = Engine_Api::_() -> getDbTable('photos', 'advalbum');
		}
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count') -> where('album_id =?', $this -> album_id);
		return $select -> query() -> fetchColumn(0);
	}

	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	public function getShortTitle($lengthLimit = 24, $suffix = "...")
	{
		$title = $this -> getTitle();
		return Advalbum_Api_Core::shortenText($title, $lengthLimit, $suffix);
	}
	
	public function getAlbumPhotos()
	{
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$tableName = $table -> info('name');
		$select = $table -> select() -> from($tableName) -> where("album_id = ?", $this -> album_id) -> order("order");
		$photo_list = $table -> fetchAll($select);
		return $photo_list;
		
	}
	
	public function getVirtualPhotos()
	{
		if (!$this->virtual)
		{
			return array();
		}
		$photoTbl = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$photoTblName = $photoTbl -> info('name');
		$virtualPhotoTbl = Engine_Api::_() -> getDbtable('virtualphotos', 'advalbum');
		$virtualPhotoTblName = $virtualPhotoTbl -> info('name');
		
		$db = $virtualPhotoTbl->getAdapter();
		$virtualPhotoIds = $db
		-> select()
		-> from($virtualPhotoTblName)
		-> where("album_id = ? ", $this -> getIdentity())
		-> query()
		-> fetchAll(Zend_Db::FETCH_COLUMN, 1);
		$str_virtualPhotoIds ="";
		if($virtualPhotoIds)
		{
			$str_virtualPhotoIds = array_unique($virtualPhotoIds);
		}
		$select = $photoTbl -> select() -> from($photoTblName) -> where("photo_id IN (?)", $str_virtualPhotoIds) -> order("order");
		$photos = $photoTbl -> fetchAll($select);
		return Zend_Paginator::factory($photos);
	}

	public function getFirstVirtualPhoto()
	{
		$photoTbl = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$photoTblName = $photoTbl -> info('name');
		$virtualPhotoTbl = Engine_Api::_() -> getDbtable('virtualphotos', 'advalbum');
		$virtualPhotoTblName = $virtualPhotoTbl -> info('name');
		$virtualPhoto = $virtualPhotoTbl->fetchRow($virtualPhotoTbl->select()->where("album_id = ?", $this->getIdentity())->limit(1));
		$photo = Engine_Api::_()->getItem("advalbum_photo", $virtualPhoto->photo_id);
		return $photo;
	}	
	
}
