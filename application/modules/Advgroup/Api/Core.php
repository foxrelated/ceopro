<?php
class Advgroup_Api_Core extends Core_Api_Abstract {
	
	public function getItemTable($type) {
		if ($type == 'group') {
			return Engine_Loader::getInstance ()->load ( 'Advgroup_Model_DbTable_Groups' );
		} else {
			$class = Engine_Api::_ ()->getItemTableClass ( $type );
			return Engine_Api::_ ()->loadClass ( $class );
		}
	}
	public function countGroupFolder($group){
		
		$table = Engine_Api::_() -> getDbTable('folders', 'ynfilesharing');
		
		
		$select = $table -> select()
		-> where("parent_type = ?", 'group') 
		-> where("parent_id = ?", $group->getIdentity()) 
		;
		$count = 0;
		$folders = $table -> fetchAll($select);
		foreach ($folders as $folder)
		{
			
			$count = $count + 1;
		}
		
		return $count;
	}
	
	 public function getCurrentSiteUrl()
    {
    	// init page url
    	$pageURL = 'http';
    	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
    	{
    		$pageURL .= "s";
    	}
    	$pageURL .= "://";
    	if ($_SERVER["SERVER_PORT"] != "80")
    	{
    		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["PHP_SELF"];
    	}
    	else
    	{
    		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
    	}
    	
    	return $pageURL;
    }
	
	public function countFileFolder($folder){
		
		$table = Engine_Api::_() -> getDbTable('files', 'ynfilesharing');
		
		
		$select = $table -> select()
		-> where("folder_id = ?", $folder->getIdentity()) 
		;
		$count = 0;
		$files = $table -> fetchAll($select);
		foreach ($files as $file)
		{
			
			$count = $count + 1;
		}
		
		return $count;
	}
	
	public function getSingers($album_id)
	{
		
		$as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
		$as_name = $as_table -> info('name');
		$s_table = Engine_Api::_() -> getDbTable('singers', 'mp3music');
		$s_name = $s_table -> info('name');
		
		$select = $as_table -> select()  -> where("$as_name.album_id = ?", $album_id) -> order('order ASC') ->limit(1) ;
		
		$albumSongs = $as_table -> fetchAll($select);
		foreach ($albumSongs as $albumSong)
		{
		    $singer_id =  $albumSong ->singer_id;
			$other_singer = $albumSong -> other_singer;	
		}
		
		if($singer_id == 0)
		{
			if($other_singer != null)
			  return $other_singer;
			else {
				return false;
			}
		}
		
		$select1 = $s_table ->  select() -> where("$s_name.singer_id = ?", $singer_id) ;
		
		$singers = $s_table -> fetchAll($select1);
		
		foreach ($singers as $singer)
		{
		    return $title =  $singer ->title;		
		}
		
		
	}
	
	public function getNumberValue($type, $level_id, $name) {
		$table = Engine_Api::_ ()->getDbtable ( 'permissions', 'authorization' );
		$select = $table->select ()->where ( "type = ?", $type )->where ( "level_id = ?", $level_id )->where ( "name = ?", $name );
		$allow = $table->fetchRow ( $select );
		if (! $allow) {
			return null;
		}
		switch ($allow->value) {
			case 3 :
			case 5 :
				if (! empty ( $allow->params )) {
					return $allow->params;
				} else {
					return $allow->value;
				}
			default :
				return $allow->value;
		}
	}
	public function checkYouNetPlugin($name) {
		$table = Engine_Api::_ ()->getDbTable ( 'modules', 'core' );
		$select = $table->select ()->where ( 'name = ?', $name )->where ( 'enabled  = 1' );
		$result = $table->fetchRow ( $select );
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	public function getParent($group_id = 0) {
		return Engine_Api::_ ()->getItem ( 'group', $group_id );
	}
	public function getPrivateGroups() {
		$table = Engine_Api::_ ()->getItemTable ( 'group' );
		$select = $table->select ()->where ( 'search = 0 and is_subgroup = 0' );
		$groups = $table->fetchAll ( $select );
		$result = array ();
		foreach ( $groups as $group ) {
			$result [] = $group->group_id;
		}
		return $result;
	}
	public function subPhrase($string, $length = 0) {
		if (strlen ( $string ) <= $length)
			return $string;
		$pos = $length;
		for($i = $length - 1; $i >= 0; $i --) {
			if ($string [$i] == " ") {
				$pos = $i + 1;
				break;
			}
		}
		return substr ( $string, 0, $pos ) . "...";
	}
	/**
	 * Used to upload CSV/VCF file
	 *
	 * @param mixed $user
	 */
	function uploadContactFile($user) {
		// Get the library file
		include_once 'VcardReader.php';
		include_once 'vcard.php';
		$settings = Engine_Api::_ ()->getApi ( 'settings', 'core' );
		$contacts = array ();
		$friends = array ();

		$is_error = 0;
		$message = '';
		$ci_contacts = array ();

		// list the permitted file type
		$permit_file_types = array (
				'text/csv' => 'csv',
				'text/comma-separated-values' => 'csv',
				'application/csv' => 'csv',
				'application/excel' => 'csv',
				'application/vnd.ms-excel' => 'csv',
				'application/vnd.msexcel' => 'csv',
				'text/anytext' => 'csv',
				'text/x-vcard' => 'vcf',
				'application/vcard' => 'vcf',
				'text/anytext' => 'vcf',
				'text/directory' => 'vcf',
				'text/x-vcalendar' => 'vcf',
				'application/x-versit' => 'vcf',
				'text/x-versit' => 'vcf',
				'application/octet-stream' => 'ldif'
		);

		for(;;) {
			$uploaded_file = $_FILES ['csvfile'] ['tmp_name'];
			$filetype = $_FILES ['csvfile'] ["type"];
			$filename = $_FILES ['csvfile'] ['name'];
			// Check file types
			$v = strpos ( $filename, '.ldif' );

			if (! array_key_exists ( $filetype, $permit_file_types ) && $v < 0) {
				$is_error = 1;
				$message = "Invalid file type!";
				break;
			}

			if (is_uploaded_file ( $uploaded_file )) {
				$fh = fopen ( $uploaded_file, "r" );
				// die('0');
				if ($this->EndsWith ( mb_strtolower ( $filename ), 'csv' )) {

					// Process CSV file type
					// die('1');
					$i = 0;
					$row = fgetcsv ( $fh, 1024, ',' );

					$first_name_pos = - 1;
					$email_pos = - 1;
					$first_display_name = - 1;
					$count = count ( $row );

					for($i = 0; $i < $count; $i = $i + 1) {

						if ($row [$i] == "E-mail Display Name" || $row [$i] == "First" || $row [$i] == "First Name") {

							$first_name_pos = $i;
						} elseif ($row [$i] == "E-mail Address" || $row [$i] == "Email" || $row [$i] == "E-mail Address") {
							$email_pos = $i;
						} elseif ($row [$i] == "First Name" || $row [$i] == "First") { // yahoo
						                                                             // format
						                                                             // oulook
							$first_display_name = $i;
						} else {
							// do nothing
						}
					}

					if (($email_pos == - 1) || ($first_name_pos == - 1)) {
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					} else {
						if ($first_display_name == - 1)
							$first_display_name = $first_name_pos;
					}

					while ( ($row = fgetcsv ( $fh, 1024, ',' )) != false ) {
						if (isset ( $row [$email_pos] ) && $row [$email_pos] != "")
							$contacts [] = array (
									'email' => $row [$email_pos],
									'name' => empty ( $row [$first_name_pos] ) ? @$row [$first_display_name] : @$row [$first_name_pos]
							);
					}

					fclose ( $fh );
				} elseif ($this->EndsWith ( mb_strtolower ( $filename ), 'vcf' )) {
					// Process VCF file type
					$file_size = filesize ( $uploaded_file );

					if ($file_size == 0) {
						$is_error = 1;
						$message = 'Empty file!';
						break;
					}
					$lines = file ( $uploaded_file );
					$cards = @$this->parse_vcards ( $lines );
					$names = array (
							'EMAIL'
					);
					foreach ( $cards as $card_name => $card ) {

						// echo $card_name;
						$contact ['first_name'] = $card_name;
						$contact ['name'] = $contact ['first_name'];

						$properties = $card->getProperties ( 'EMAIL' );
						if ($properties) {
							// echo "<pre>".print_r($properties,true)."</pre>";
							$contact ['email'] = $properties [0]->value;
							$contacts [] = array (
									'email' => $contact ['email'],
									'name' => $contact ['name']
							);
							// echo ;
						}
					}
					if ((! isset ( $contact ['email'] )) || (! isset ( $contact ['name'] ))) {
						// die('3');
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					}

					if (isset ( $contact ['email'] )) {
						// die('4');
						if ($this->validateEmail ( $contact ['email'] )) {
							$contacts [] = array (
									'email' => $contact ['email'],
									'name' => $contact ['name']
							);
						} else {
							// error 1 contact, but omit it
							$is_error = 0;
							$message = "There's some error in your contact file";
						}
					}
				} elseif ($this->EndsWith ( mb_strtolower ( $filename ), 'ldif' )) { // thunderbirth
					$thunder_data = fread ( $fh, filesize ( $uploaded_file ) );
					$rows = explode ( PHP_EOL, $thunder_data );
					$name = "";
					$email = "";
					$contacts = array ();

					foreach ( $rows as $index => $row ) {
						try {
							@list ( $key, $data ) = @explode ( ':', $row );
							if ($key == 'cn')
								$name = $data;
							if ($key == 'mail')
								$email = trim ( $data );

							if ($name != "" && $email != "") {

								$contacts [] = array (
										'email' => $email,
										'name' => $name
								);

								$name = "";
								$email = "";
							}
						} catch ( Exception $ex ) {
						}
					}
				} else {
					// not support format
					$is_error = 1;
					$message = "Unknown file type!";
				}
			}

			if (empty ( $contacts )) {

				$is_error = 1;
				$message = "There is no contact in your address book";
				break;
			}

			foreach ( $contacts as $value ) {

				$ci_contacts ["{$value["email"]}"] = $value ["name"];
			}
			break;
		}

		$returns ['contacts'] = $ci_contacts;
		$returns ['is_error'] = $is_error;
		$returns ['error_message'] = $message;

		return $returns;
	}

	/**
	 * Check if a string ends with a specified substring
	 *
	 * @param mixed $FullStr
	 * @param mixed $EndStr
	 */
	function endsWith($FullStr, $EndStr) {
		// Get the length of the end string
		$StrLen = strlen ( $EndStr );

		// Look at the end of FullStr for the substring the size of EndStr

		$FullStrEnd = substr ( $FullStr, strlen ( $FullStr ) - $StrLen );
		// If it matches, it does end with EndStr
		return $FullStrEnd == $EndStr;
	}
	public function getFolderHref($p = array(), $folder, $group) {
		$params = array_merge(array(
			'route' => 'group_viewsubfolder',
			'reset' => true,
			'controller' => 'file',
			'action' => 'view-folder',
			'slug' => $folder->getSlug(),
			'folder_id' => $folder->getIdentity()
		), $p);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
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
}
