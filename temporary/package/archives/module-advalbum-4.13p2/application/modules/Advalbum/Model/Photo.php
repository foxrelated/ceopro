<?php
$path = APPLICATION_PATH . "/application/modules/Advalbum/Libs/ColorCompare/ColorCompare.php";
require_once($path);

class Advalbum_Model_Photo extends Core_Model_Item_Collectible
{
    // protected $_parent_type = 'album';
    // protected $_owner_type = 'album';
    protected $_searchColumns = array(
            'title',
            'description'
    );

    protected $_collection_type = "advalbum_album";

    public function getHref ($params = array())
    {
    	if(isset($params['virtual_album']) && $params['virtual_album'] == $this->album_id)
		{
			unset($params['virtual_album']);
		}
        $params = array_merge(
                array(
                        'route' => 'album_extended',
                        'reset' => true,
                        'controller' => 'photo',
                        'action' => 'view',
                        'album_id' => $this->album_id,
                        'photo_id' => $this->getIdentity()
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble(
                $params, $route, $reset);
    }
	
    public function getLightBoxHref ($params = array())
    {
        $params = array_merge(
                array(
                        'route' => 'album_extended',
                        'reset' => true,
                        'controller' => 'album',
                        'action' => 'view',
                        'album_id' => $this->album_id,
                        'id' => $this->getIdentity()
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble(
                $params, $route, $reset);
    }

    public function getCollection ()
    {
        if (! isset($this->album_id)) {
            throw new Core_Model_Item_Exception(
                    'If column with collection_id not defined, must override getCollection()');
        }

        return Engine_Api::_()->getItem('advalbum_album', $this->album_id);
    }

    public function getNextPhoto ($album_virtual = 0)
    {
    	if($album_virtual && $album_virtual != $this->album_id)
		{
			$table = Engine_Api::_() -> getDbTable('virtualphotos', 'advalbum');
	        $select = $table->select()
	            ->where('album_id = ?', $album_virtual)
	            ->where('`photo_id` > ?', $this->photo_id)
	            ->order('photo_id ASC')
	            ->limit(1);
	        $photo = $table->fetchRow($select);
	
	        if (!$photo) 
	        {
	            // Get first photo instead
	            $select = $table->select()
	                ->where('album_id = ?', $album_virtual)
	                ->order('photo_id ASC')
	                ->limit(1);
	            $photo = $table->fetchRow($select);
	        }
			if($photo)
			{
				$photo = Engine_Api::_() -> getItem('advalbum_photo', $photo -> photo_id);
			}
		}
		else 
		{
			$table = $this->getTable();
	        $select = $table->select()
	            ->where('album_id = ?', $this->album_id)
//                ->where('`photo_id` > ?', $this->photo_id)
//	            ->order('photo_id ASC')
	            ->where('`order` > ?', $this->order)
	            ->order('order ASC')
	            ->limit(1);
	        $photo = $table->fetchRow($select);
	
	        if (! $photo) {
	            // Get first photo instead
	            $select = $table->select()
	                ->where('album_id = ?', $this->album_id)
	                ->order('order ASC')
	                ->limit(1);
	            $photo = $table->fetchRow($select);
	        }
		}
        return $photo;
    }

    public function getPreviousPhoto ($album_virtual = 0)
    {
    	if($album_virtual && $album_virtual != $this->album_id)
		{
			$table = Engine_Api::_() -> getDbTable('virtualphotos', 'advalbum');
	        $select = $table->select()
	            ->where('album_id = ?', $album_virtual)
	            ->where('`photo_id` > ?', $this->photo_id)
	            ->order('photo_id ASC')
	            ->limit(1);
	        $photo = $table->fetchRow($select);
	
	        if (!$photo) 
	        {
	            // Get first photo instead
	            $select = $table->select()
	                ->where('album_id = ?', $album_virtual)
	                ->order('virtualphoto_id ASC')
	                ->limit(1);
	            $photo = $table->fetchRow($select);
	        }
			if($photo)
			{
				$photo = Engine_Api::_() -> getItem('advalbum_photo', $photo -> photo_id);
			}
		}
		else 
		{
	        $table = $this->getTable();
	        $select = $table->select()
	            ->where('album_id = ?', $this->album_id)
//                ->where('`photo_id` < ?', $this->photo_id)
//                ->order('photo_id ASC')
	            ->where('`order` < ?', $this->order)
	            ->order('order DESC')
	            ->limit(1);
	        $photo = $table->fetchRow($select);
	
	        if (! $photo) {
	            // Get last photo instead
	            $select = $table->select()
	                ->where('album_id = ?', $this->album_id)
	                ->order('order DESC')
	                ->limit(1);
	            $photo = $table->fetchRow($select);
	        }
		}

        return $photo;
    }

    // get first/last photo
    public function getNavigationPhoto ($last = null)
    {
        $table = $this->getTable();
        $select = $table->select()->where('album_id = ?', $this->album_id);
        if ($last) {
            $select->order('order DESC')->limit(1);
        } else {
            $select->order('order')->limit(1);
        }
        $photo = $table->fetchRow($select);

        return $photo;
    }

    public function getParent ($type = null)
    {
        if (null === $type) {
            return $this->getCollection();
        } else {
            return $this->getCollection()->getParent($type);
        }
    }

    public function getPhotoUrl ($type = null)
    {
        $photo_id = $this->file_id;
        if (! $photo_id) {
            return null;
        }

        $file = Engine_Api::_()->getApi('storage', 'storage')->get($photo_id,
                $type);
        if (! $file) {
            return null;
        }

        return $file->map();
    }

    public function featuredThumbnailUrl ()
    {
        $photo_id = $this->file_id;
        if (! $photo_id) {
            return null;
        }
        $file = Engine_Api::_()->getApi('storage', 'storage')->get($photo_id,
                "thumb.normal");
        if (! $file) {
            return null;
        }
        return $file->map();

        return null;
    }

    public function getPhotoSize ()
    {
        $photo_id = $this->file_id;
        if (! $photo_id) {
            return null;
        }
        $file = Engine_Api::_()->getApi('storage', 'storage')->get($photo_id,
                null);
        if (! $file) {
            return null;
        }
        $arr_size = getimagesize($file->storage_path);
        if (! $arr_size) {
            return null;
        }
        return array(
                'w' => $arr_size[0],
                'h' => $arr_size[1]
        );
    }

    public function fitPhotoSize ($arr_size_compare = array('w'=>640,'h'=>480))
    {
        if (! $arr_size_compare || ! is_array($arr_size_compare)) {
            return null;
        }
        $photo_id = $this->file_id;
        if (! $photo_id) {
            return null;
        }

        $file = Engine_Api::_()->getApi('storage', 'storage')->get($photo_id,
                null);

        if (! $file) {
            return null;
        }
        $arr_size = getimagesize($file->storage_path);
        if (! $arr_size) {
            return null;
        }
        $w = $arr_size[0];
        $h = $arr_size[1];
        $max_w = 0;
        if (isset($arr_size_compare['w'])) {
            $max_w = $arr_size_compare['w'];
        }
        $max_h = 0;
        if (isset($arr_size_compare['h'])) {
            $max_h = $arr_size_compare['h'];
        }
        $b_change = FALSE;
        if ($max_w && $w > $max_w) {
            $h = $h * $max_w / $w;
            $w = $max_w;
            $b_change = TRUE;
        }
        if ($max_h && $h > $max_h) {
            $w = $w * $max_h / $h;
            $h = $max_h;
            $b_change = TRUE;
        }
        if ($b_change) {
            return array(
                    'w' => (int) ($w),
                    'h' => (int) ($h)
            );
        }
        return FALSE;
    }

    public function isSearchable ()
    {
        $collection = $this->getCollection();
        if (! $collection instanceof Core_Model_Item_Abstract) {
            return false;
        }
        return $collection->isSearchable();
    }

    public function getAuthorizationItem ()
    {
        return Engine_Api::_()->getItem('advalbum_album', $this->album_id);
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     *
     */
    public function comments ()
    {
        return new Engine_ProxyObject($this,
                Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the subscribe handler
     *
     * @return Engine_ProxyObject
     *
     */
    public function subscribes ()
    {
        return new Engine_ProxyObject($this,
                Engine_Api::_()->getDbtable('subscribes', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     *
     */
    public function likes ()
    {
        return new Engine_ProxyObject($this,
                Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     *
     */
    public function tags ()
    {
        return new Engine_ProxyObject($this,
                Engine_Api::_()->getDbtable('tags', 'core'));
    }

    public function isOwner ($user)
    {
        if (empty($this->album_id)) {
            return (($this->owner_id == $user->getIdentity()) &&
                     ($this->owner_type == $user->getType()));
        }
        return parent::isOwner($user);
    }

    function isDeletable() {
        $user = Engine_Api::_()->user()->getViewer();
        return $this->isOwner($user);
    }
    function isVirtual() {
        $album = $this->getParent();
        return isset($album->virtual) ? $album->virtual : 0;
    }
    protected function _postDelete ()
    {
        // This is dangerous, what if something throws an exception in
        // postDelete
        // after the files are deleted?
        try {
            $file = Engine_Api::_()->getApi('storage', 'storage')->get(
                    $this->file_id, null);
            if ($file) {
                $file->remove();
            }

            $file = Engine_Api::_()->getApi('storage', 'storage')->get(
                    $this->file_id, 'thumb.normal');
            if ($file) {
                $file->remove();
            }
            // $file = Engine_Api::_()->getApi('storage',
            // 'storage')->get($this->file_id, 'croppable');
            // $file->remove();

            $album = $this->getCollection();
            $nextPhoto = $this->getNextCollectible();

            if (($album instanceof Core_Model_Item_Collection) &&
                     ($nextPhoto instanceof Core_Model_Item_Collectible) &&
                     (int) $album->photo_id == (int) $this->getIdentity()) {
                $album->photo_id = $nextPhoto->getIdentity();
                $album->save();
            }
            
            $photoColorTbl = Engine_Api::_()->getDbTable("photocolors", "advalbum");
	    	$where = $photoColorTbl->getAdapter()->quoteInto('photo_id = ?', $this->getIdentity());
	    	$photoColorTbl->delete($where);
        } catch (Exception $e) {
            // @todo should we completely silence the errors?
            // throw $e;
        }

        parent::_postDelete();
    }

    public function getFeatured ()
    {
        $ftable = Engine_Api::_()->getDbtable('features', 'advalbum');
        $fName = $ftable->info('name');
        $select = $ftable->select()
            ->from($fName)
            ->where("photo_id = ?", $this->photo_id);
        $features = $ftable->fetchAll($select);
        if (count($features) <= 0)
            return false;
        else {
            if ($features[0]->photo_good == '1')
                return true;
            else
                return false;
        }
        return false;
    }

    public function getTitle ()
    {
        $title = trim(parent::getTitle());
        if (! $title) {
            $view = Zend_Registry::get('Zend_View');
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $default_photo_title = $settings->getSetting(
                    'album_default_photo_title',
                    $view->translate('[Untitled]'));
            $title = $default_photo_title;
        }
        return $title;
    }

    public function getShortTitle ($lengthLimit = 24, $suffix = "...")
    {
        $title = $this->getTitle();
        return Advalbum_Api_Core::shortenText($title, $lengthLimit, $suffix);
    }

    public function frameviewCode ()
    {
        return ($this->getIdentity() + 2) * 17;
    }
    
    public function parseColor()
    {
    	$viewer = Engine_Api::_()->user()->getViewer();
    	if ($viewer->getIdentity())
    	{
    		$timezone = $viewer->timezone;
    	}
    	else
    	{
    		$timezone = date_default_timezone_get();
    	}
    	date_default_timezone_set($timezone);
    	
    	$colorTbl = Engine_Api::_()->getDbTable("colors", "advalbum");
    	Advalbum_Libs_ColorCompare::$swatches = $colorTbl->getColorArray();
    	$filename = $this->getPhotoUrl();
    	/*
    	$file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, null);
    	$filepath = $file->storage_path;
		*/
    	$tmpRow = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
        $filepath = $tmpRow -> temporary();	
    	
    	$max_colors = Engine_Api::_()->getApi('settings', 'core')->getSetting('advalbum.maxcolor', 1);
    	$result = Advalbum_Libs_ColorCompare::compare($max_colors, $filepath);
    	if ($result == false)
    	{
    		return false;
    	}
    	else
    	{
    		$colorIds = $colorTbl->getColorIds();
    		$photoColorTbl = Engine_Api::_()->getDbTable("photocolors", "advalbum");
    		foreach ($result as $color => $count)
    		{
    			$photocolor = $photoColorTbl->createRow();
    			$photocolor -> photo_id = $this->getIdentity();
    			$photocolor -> color_title= $color;
    			$photocolor -> pixel_count = $count;
    			$photocolor -> save();
    		}
    	}
    	@unlink($filepath);
    }
    
    public function getColors()
    {
    	$photoColorTbl = Engine_Api::_()->getDbTable("photocolors", "advalbum");
    	$select = $photoColorTbl->select()->where("photo_id = ?", $this->getIdentity());
        $max_color = Engine_Api::_()->getDbTable("settings", "core")->getSetting("advalbum.maxcolor", 1);
    	$result = array();
    	$colors = $photoColorTbl->fetchAll($select);
    	if (count($colors))
    	{
    		foreach ($colors as $index => $color)
    		{
    			$result[] = $color->color_title;
                if ($index >= $max_color - 1)
                    break;
    		}
    	}
    	return $result;
    }
    
    public function getColorValue()
    {
        $colorValue = '';
        $photoColors = $this->getColors();
        if ($photoColors) {
            $colorTbl = Engine_Api::_()->getDbTable("colors", "advalbum");
            $select = $colorTbl->select()->where("title = ?", $photoColors[0]);
            $color = $colorTbl->fetchRow($select);
            $colorValue = $color->hex_value;
        }
        return $colorValue;
    }
    public function saveColors($colors)
    {
    	$photoColorTbl = Engine_Api::_()->getDbTable("photocolors", "advalbum");
    	$where = $photoColorTbl->getAdapter()->quoteInto('photo_id = ?', $this->getIdentity());
    	$photoColorTbl->delete($where);
    	foreach ($colors as $color)
    	{
    		$row = $photoColorTbl->createRow();
    		$row->photo_id = $this->getIdentity();
    		$row->color_title = $color;
    		$row->pixel_count = 0;
    		$row->save();
    	}
    }
    
    public function getLocation()
    {
        $location = '';
        if (!empty($this->location))
            $location = $this->location;
        return $location;
    }
}
