<?php
class Ultimatenews_Model_Content extends Core_Model_Item_Abstract {
	/**
	 * get href for object news
	 *
	 * @return string url
	 */
	public function getHref($params = array()) {
		$slug = $this -> getSlug($this -> title);
		$params = array_merge(array('route' => 'ultimatenews_specific', 'reset' => true, 'id' => $this -> content_id, 'slug' => $slug), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getObj($contentId) {
		$Ultimatenews_content = Engine_Api::_() -> getItem('contents', $contentId);
		return $Ultimatenews_content;
	}

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function comments() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	/**
	 * Gets a proxy object for the subscribe handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function subscribes() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('subscribes', 'core'));
	}

	public function setPhoto($photo) {
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
		} else if (is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
		} else if (is_string($photo) && file_exists($photo)) {
			$file = $photo;
		} else {
			throw new Book_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => 'book', 'parent_id' => $this -> getIdentity());

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(140, 160) -> write($path . '/in_' . $name) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;

		$image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/is_' . $name);

		// Update row
		$this -> image = $iMain -> storage_path;
		$this -> photo_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function tags() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}

	public function getKeywords($separator = ' ') {
		$keywords = array();
		foreach ($this->tags()->getTagMaps() as $tagmap) {
			$tag = $tagmap -> getTag();
			$keywords[] = $tag -> getTitle();
		}

		if (null === $separator) {
			return $keywords;
		}

		return join($separator, $keywords);
	}
	public function checkFavourite()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $favoriteTable = Engine_Api::_()->getDbtable('favorites', 'ultimatenews');
        $select = $favoriteTable->select()
        	->where('content_id = ?', $this->content_id)
        	->where('user_id = ?', $viewer->getIdentity())
			->limit(1);
        $row = $favoriteTable->fetchRow($select);
        if($row)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

	public function getMediaType()
	{
		return 'news';
	}

	protected function _postDelete()
	{
		if(!empty($this -> photo_id))
		{
			$mainPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id);
			$thumbProfilePhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.profile');
			$thumbNormalPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.normal');
			$thumbIconPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.icon');

			// Delete thumb
			if( $thumbProfilePhoto && $thumbProfilePhoto->getIdentity() ) {
				try {
					$thumbProfilePhoto->delete();
				} catch( Exception $e ) {}
			}

			// Delete thumb
			if( $thumbNormalPhoto && $thumbNormalPhoto->getIdentity() ) {
				try {
					$thumbNormalPhoto->delete();
				} catch( Exception $e ) {}
			}
			// Delete thumb
			if( $thumbIconPhoto && $thumbIconPhoto->getIdentity() ) {
				try {
					$thumbIconPhoto->delete();
				} catch( Exception $e ) {}
			}

			// Delete main
			if( $mainPhoto && $mainPhoto->getIdentity() ) {
				try {
					$mainPhoto->delete();
				} catch( Exception $e ) {}
			}
		}

		parent::_postDelete();
	}

    /**
     * Gets the description of content
     *
     * @return string The description stripped tags
     */
    public function getDescription()
    {
        if( isset($this->description) )
        {
            return strip_tags($this->description);
        }
        return '';
    }
}
?>