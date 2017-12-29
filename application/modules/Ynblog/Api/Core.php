<?php
class Ynblog_Api_Core extends Core_Api_Abstract {

	public function getYnBlog() {
		$table = Engine_Api::_() -> getDbTable('modules', 'core');
		$select = $table -> select() -> where('name = ?', 'ynblog') -> where('enabled = 1');
		$result = $table -> fetchAll($select);
		if (count($result) > 0)
			return true;
		else
			return false;
	}

	/*----- Checking Existing Blog URL Function-----*/
	public function checkURL($link = '', $user_id = 0) {
		$table = Engine_Api::_() -> getDbTable('links', 'ynblog');
		$select = $table -> select() -> where('link_url like ?', '%' . $link . '%') -> where('user_id <> ?', $user_id);
		$result = $table -> fetchAll($select);
		if (count($result) > 0)
			return false;
		else
			return true;
	}

	public function getLink($id) {
		$table = Engine_Api::_() -> getDbTable('links', 'ynblog');
		$select = $table -> select() -> where('link_id = ?', $id);
		$result = $table -> fetchRow($select);
		return $result;
	}

	/*----- Get Sub-word Phrase Function -----*/
	public function subPhrase($string, $length = 0) {
		if (strlen($string) <= $length)
			return $string;
		$pos = $length;
		for ($i = $length - 1; $i >= 0; $i--) {
			if ($string[$i] == " ") {
				$pos = $i + 1;
				break;
			}
		}
		return substr($string, 0, $pos) . "...";
	}

	/*----- Get Collection Of Dates Where A Given User Created A Blog Entry Function -----*/
	public function getArchiveList($user_id) {
		$table = Engine_Api::_() -> getDbtable('blogs', 'ynblog');
		$rName = $table -> info('name');

		$select = $table -> select() -> from($rName) -> where($rName . '.owner_id = ?', $user_id) -> where($rName . '.draft = ?', "0") -> where($rName . '.search = ?', "1") -> where($rName . '.is_approved = ?', "1");
		return $table -> fetchAll($select);
	}

	/*----- Check User Become Function -----*/
	public function checkUserBecome($user_id, $blog_id) {
		$table = Engine_Api::_() -> getDbTable('becomes', 'ynblog');
		$name = $table -> info('name');
		$select = $table -> select() -> where("$name.user_id = ?", $user_id) -> where("$name.blog_id = ?", $blog_id);

		$rows = $table -> fetchAll($select);
		return Count($rows) > 0 ? false : true;
	}

	/*----- Get User Tags Function -----*/
	public function getUserTags($user_id) {
		$t_table = Engine_Api::_() -> getDbtable('tags', 'core');
		$tName = $t_table -> info('name');
		$select = $t_table -> select() -> from($tName, array("$tName.*", "Count($tName.tag_id) as count")) -> joinLeft("engine4_core_tagmaps", "engine4_core_tagmaps.tag_id = $tName.tag_id", '') -> order("$tName.text") -> group("$tName.text") -> where("engine4_core_tagmaps.tagger_id = ?", $user_id) -> where("engine4_core_tagmaps.resource_type = ?", "blog");
		$this -> view -> userTags = $t_table -> fetchAll($select);
	}

	/*----- Override getItemTable Function-----*/
	public function getItemTable($type) {
		if ($type == 'blog_category') {
			return Engine_Loader::getInstance() -> load('Ynblog_Model_DbTable_Categories');
		} else if ($type == 'blog') {
			return Engine_Loader::getInstance() -> load('Ynblog_Model_DbTable_Blogs');
		} else {
			$class = Engine_Api::_() -> getItemTableClass($type);
			return Engine_Api::_() -> loadClass($class);
		}
	}

	/*----- Get Blog Paginater Function -----*/
	public function getBlogsPaginator($params = array()) {
		//Get blogs paginator
		$blog_select = $this -> getBlogsSelect($params);
		$paginator = Zend_Paginator::factory($blog_select);

		//Set current page
		if (!empty($params['page'])) {
			$paginator -> setCurrentPageNumber($params['page'], 1);
		}
		//Item per page
		$itemPerPage = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('blog.page', 10);
		$paginator -> setItemCountPerPage($itemPerPage);
		return $paginator;
	}

	/*----- Get Blog Selection Query Function -----*/
	public function getBlogsSelect($params = array()) {
		// Get blog table
		$blog_table = Engine_Api::_() -> getItemTable('blog');
		$blog_name = $blog_table -> info('name');

		// Get Tagmaps table
		$tags_table = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tags_name = $tags_table -> info('name');

		//Select blog
		if (!isset($params['direction']))
			$params['direction'] = "DESC";

		//Order by filter
		if (isset($params['orderby']) && $params['orderby'] == 'displayname') {
			$select = $blog_table -> select() -> from($blog_name) -> setIntegrityCheck(false) -> join('engine4_users as u', "u.user_id = $blog_name.owner_id", '') -> order("u.displayname " . $params['direction']);
		} else {
			$select = $blog_table -> select() -> from($blog_name) -> order(!empty($params['orderby']) ? $blog_name . "." . $params['orderby'] . ' ' . $params['direction'] : $blog_name . '.blog_id ' . $params['direction']);
		}
		//User id filter
		if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
			$select -> where($blog_name . '.owner_id = ?', $params['user_id']);
		}

		// Show type filter
		if (!empty($params['show']) && $params['show'] == 2) {
			$str = (string)(is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users']);
			$select -> where($blog_name . '.owner_id in (?)', new Zend_Db_Expr($str));
		}

		//Tag filter
		if (!empty($params['tag'])) {
			$select -> setIntegrityCheck(false) -> joinLeft($tags_name, "$tags_name.resource_id = $blog_name.blog_id", "") -> where($tags_name . '.resource_type = ?', 'blog') -> where($tags_name . '.tag_id = ?', $params['tag']);
		}

		//Category filter
		if (!empty($params['category'])) {
			$select -> where($blog_name . '.category_id = ?', $params['category']);
		}

		//Rss filter
		if (!empty($params['blogRss'])) {
			$select -> where($blog_name . '.blog_id = ?', $params['blogRss']);
		}

		//Blog mode filter
		if (isset($params['draft'])) {
			$select -> where($blog_name . '.draft = ?', $params['draft']);
		}

		//Blog moderaton filer
		if (isset($params['is_approved'])) {
			$select -> where($blog_name . '.is_approved = ?', $params['is_approved']);
		}
		//Search filter
		if (!empty($params['search'])) {
			$select -> where($blog_name . ".title LIKE ? OR " . $blog_name . ".body LIKE ?", '%' . $params['search'] . '%');
		}

		//Title filter
		if (!empty($params['title'])) {
			$select -> where($blog_name . ".title LIKE ?", '%' . $params['title'] . '%');
		}

		//Start date filter
		if (!empty($params['start_date'])) {
			$select -> where($blog_name . ".creation_date > ?", date('Y-m-d', $params['start_date']));
		}
		if (!empty($params['date'])) {
			$date = $params['date'];
			$temp = explode(" ", $date);
			$date = explode("-", $temp[0]);
			$y = $date[0];
			$m = $date[1];
			$d = $date[2];
			$select -> where("YEAR(" . $blog_name . ".creation_date) = ?", $y);
			$select -> where("MONTH(" . $blog_name . ".creation_date) = ?", $m);
			$select -> where("DAY(" . $blog_name . ".creation_date) = ?", $d);
		}

		//End date filter
		if (!empty($params['end_date'])) {
			$select -> where($blog_name . ".creation_date < ?", date('Y-m-d', $params['end_date']));
		}

		//Search privacy filter
		if (!empty($params['visible'])) {
			$select -> where($blog_name . ".search = ?", $params['visible']);
		}

		//Feature blog filter
		if (isset($params['featured'])) {
			$select -> where("$blog_name.is_featured = ?", $params['featured']);
		}

		//Owner in Admin Search
		if (!empty($params['owner'])) {
			$key = stripslashes($params['owner']);
			$select -> setIntegrityCheck(false) -> join('engine4_users as u1', "u1.user_id = $blog_name.owner_id", '') -> where("u1.displayname LIKE ?", "%{$key}%");
		}

		//Limit option
		if (!empty($params['limit'])) {
			$select -> limit($params['limit']);
		}
		//Return query
		return $select;
	}

	public function setPhoto($parent, $photo) {
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
			$fileName = $file;
		} else if ($photo instanceof Storage_Model_File) {
			$file = $photo -> temporary();
			$fileName = $photo -> name;
		} else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo -> file_id)) {
			$tmpRow = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
			$file = $tmpRow -> temporary();
			$fileName = $tmpRow -> name;
		} else if (is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
			$fileName = $photo['name'];
		} else if (is_string($photo) && file_exists($photo)) {
			$file = $photo;
			$fileName = $photo;
		} else {
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		if (!$fileName) {
			$fileName = $file;
		}

		$name = basename($file);
		$extension = ltrim(strrchr($fileName, '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => $parent -> getType(), 
						'parent_id' => $parent -> getIdentity(), 
						'user_id' => $parent -> owner_id, 
						'name' => $fileName, );

		// Save
		$filesTable = Engine_Api::_() -> getDbtable('files', 'storage');

		// Resize image (main)
		$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($mainPath) -> destroy();

		// Resize image (normal)
		$normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(140, 160) -> write($normalPath) -> destroy();

		// Store
		try {
			$iMain = $filesTable -> createFile($mainPath, $params);
			$iIconNormal = $filesTable -> createFile($normalPath, $params);

			$iMain -> bridge($iIconNormal, 'thumb.normal');
		} catch( Exception $e ) {
			// Remove temp files
			@unlink($mainPath);
			@unlink($normalPath);
			// Throw
			if ($e -> getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
				throw new Album_Model_Exception($e -> getMessage(), $e -> getCode());
			} else {
				throw $e;
			}
		}

		// Remove temp files
		@unlink($mainPath);
		@unlink($normalPath);

		// Update row
		$parent -> modified_date = date('Y-m-d H:i:s');
		$parent -> file_id = $iMain -> file_id;
		$parent -> save();

		// Delete the old file?
		if (!empty($tmpRow)) {
			$tmpRow -> delete();
		}

		return $parent;
	}

	public function getSpecialAlbum(User_Model_User $user, $type)
	{
		if (!in_array($type, array(
			'wall',
			'profile',
			'message',
			'blog'
		)))
		{
			throw new Advalbum_Model_Exception('Unknown special album type');
		}
		$album_Table = NULL;
		if(Engine_Api::_() -> hasModuleBootstrap('advalbum'))
		{
			$album_Table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		}
		else {
			$album_Table = Engine_Api::_() -> getDbtable('albums', 'album');
		}

		$select = $album_Table -> select() 
			-> where('owner_type = ?', $user -> getType()) 
			-> where('owner_id = ?', $user -> getIdentity()) 
			-> where('type = ?', $type) 
			-> order('album_id ASC') -> limit(1);

		$album = $album_Table -> fetchRow($select);

		// Create wall photos album if it doesn't exist yet
		if (null === $album)
		{
			$translate = Zend_Registry::get('Zend_Translate');
			$album = $album_Table -> createRow();
			$album -> owner_type = 'user';
			$album -> owner_id = $user -> getIdentity();
			$album -> title = $translate -> _(ucfirst($type) . ' Photos');
			$album -> type = $type;
			$album -> save();
		}
		return $album;
	}

	function finalizeUrl($url){
        if ($url && strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE){
            if (strpos($url, DIRECTORY_SEPARATOR) == 0) {
                return $this->getSchema() . $_SERVER["SERVER_NAME"]. '/' . ltrim( $url, DIRECTORY_SEPARATOR);
            } else {
                return $this->getSchema() . $_SERVER["SERVER_NAME"]. '/' . DIRECTORY_SEPARATOR . $url;
            }
        }
        return $url;
    }
    function getSchema(){
        $schema = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
            $schema = "https";
        }
        return $schema. '://';
    }

}
?>
