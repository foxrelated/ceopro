<?php
class Ynblog_Model_Blog extends Core_Model_Item_Abstract {
	/*----- Properties -----*/
	protected $_parent_type = 'user';
	protected $_parent_is_owner = true;
	protected $_searchColumns = array(
		'title',
		'body'
	);
	protected $_type = "blog";

	/*----- Get Link of Blog Function -----*/
	public function getHref($params = array()) {
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'blog_entry_view',
			'reset' => true,
			'user_id' => $this -> owner_id,
			'blog_id' => $this -> blog_id,
			'slug' => $slug
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	/*----- Get Blog Description Function ----*/
	public function getDescription() {
		$tmp_body = strip_tags($this -> body);
		$description = Engine_Api::_() -> ynblog() -> subPhrase($tmp_body, 150);
		return $description;
	}

	/*----- Get Tag Key Words -----*/
	public function getKeywords($seperator = ' ') {
		$keywords = array();
		// Get tags
		foreach ($this->tags()->getTagMaps() as $tagmap) {
			$tag = $tagmap -> getTag();
			$keywords[] = $tag -> getTitle();
		}

		// Return result
		if (null === $seperator) {
			return $keywords;
		}
		return join($seperator, $keywords);
	}

	/*----- Comment Objects Function -----*/
	public function comments() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/*----- Like Objects Function -----*/
	public function likes() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	/*----- Tag Objects Function -----*/
	public function tags() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}

	/*----- Count Number Of Blog's Like  Function -----*/
	public function getCountLikes() {
		$table = Engine_Api::_() -> getDbtable('likes', 'core');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name);
		$select -> where("resource_id = ?", $this -> blog_id) -> where("resource_type  LIKE 'blog'");
		$blogs = $table -> fetchAll($select);
		return count($blogs);
	}

	/*----- Get Featured Blogs Function -----*/
	public function getFeatured() {
		$ftable = Engine_Api::_() -> getDbtable('features', 'ynblog');
		$fName = $ftable -> info('name');
		$select = $ftable -> select() -> from($fName) -> where("blog_id = ?", $this -> blog_id);
		$features = $ftable -> fetchAll($select);
		if (count($features) <= 0)
			return false;
		else {
			if ($features[0] -> blog_good == '1')
				return true;
			else
				return false;
		}
		return false;
	}

	/*----- Get Blog Status Function ---*/
	public function getStatus() {
		$status = array();
		if ($this -> draft) {
			$status['type'] = 0;
			$status['condition'] = 'draft';
			return $status;
		}
		if ($this -> is_approved) {
			$status['type'] = 2;
			$status['condition'] = 'approved';
		}
		else {
			$status['type'] = 1;
			$status['condition'] = 'pending';
		}
		return $status;
	}

	/*----- Delete Information Related To Blog Function -----*/
	protected function _delete() {
		if ($this -> _disableHooks)
			return;

		//Delete Blog Member
		$become_table = Engine_Api::_() -> getDbTable('becomes', 'ynblog');
		$become_selected = $become_table -> select() -> where('blog_id = ?', $this -> getIdentity());
		foreach ($become_table->fetchAll($become_selected) as $blog_become) {
			$blog_become -> delete();
		}

		parent::_delete();
	}

	public function checkPermission() 
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth') -> setAuthParams($this, $viewer, 'view') -> checkRequire()) 
		{
			return false;
		}
		return true;
	}
	
	/**
	 *
	 * @see Core_Model_Item_Abstract::getPhotoUrl()
	 */
	public function getPhotoUrl($type = null)
	{
		if( empty($this->photo_id) ) {
			return "application/modules/Ynblog/externals/images/nophoto_blog_thumb_main.png";
		}
	
		$file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
		if( !$file ) {
			return "application/modules/Ynblog/externals/images/nophoto_blog_thumb_main.png";
		}
	
		return $file->map();
	}
	public function setPhotoFromUrl($url){
		$name = basename($url);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynblog',
			'parent_id' => $this -> getIdentity()
		);

		if(function_exists('curl_version')) {
			$ch = curl_init();
		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

		    $data = curl_exec($ch);
		    curl_close($ch);
		} else {
			$data = file_get_contents($url);
		}


		if($data !== false) {
			file_put_contents($path . '/l_' . $name, $data);

			if(file_exists($path . '/l_' . $name)){
				try {
					$this->setPhoto($path . '/l_' . $name);
					@unlink($path . '/l_' . $name);	
				} catch (Exception $e) {
					echo $e->getMessage();
					echo $url;
					die;
				}
				
			}	
		}
		
		return $this;
	}
	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Group_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynblog',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> write($path . '/m_' . $name) -> destroy();

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
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> file_id;
		$this -> save();
		return $this;
	}

}
?>