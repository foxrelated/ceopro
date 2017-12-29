<?php
class Advgroup_FileController extends Core_Controller_Action_Standard {
	protected $_parentType;
	protected $_parentId;
	protected $_viewer;

	public function init() {
		$this -> view -> viewer = $this -> _viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _parentType = "group";

		if (!Engine_Api::_() -> core() -> hasSubject()) 
		{
			if ((0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			|| (0 !== ($group_id = (int)$this -> _getParam('parent_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))) 
			{
				Engine_Api::_() -> core() -> setSubject($group);
				$this -> _parentId = $this -> _getParam('group_id');
			}

		} else {
			$group = Engine_Api::_() -> core() -> getSubject();
			$this -> _parentId = $group -> group_id;
		}

		$this -> view -> parentId = $this -> _parentId;	
		$this -> view -> parentType = $this -> _parentType;

		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
		}
	}

	public function listAction() {
		$file_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynfilesharing');
		if (!$file_enable) {
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();

		$viewer = Engine_Api::_() -> user() -> getViewer();

		//check auth create

		$canCreate = $group -> authorization() -> isAllowed(null, 'folder');

		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'folder');
		if ($canCreate && $levelCreate) {
			$this -> view -> canCreate = true;
		} else {

			$this -> view -> canCreate = false;
		}

		//check both auth remove folder and auth delete file

		$canDeleteRemove = $group -> authorization() -> isAllowed(null, 'file.edit');

		$levelcanDeleteRemove = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'file.edit');
		if ($canDeleteRemove && $levelcanDeleteRemove) {
			$this -> view -> canDeleteRemove = true;
		} else {
			$this -> view -> canDeleteRemove = false;
		}

		//Get Viewer, Group and Search Form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();

		

		if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer)) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return $this -> _helper -> requireAuth -> forward();
			} else if (!$group -> authorization() -> isAllowed($viewer, "view")) {
				return $this -> _helper -> requireAuth -> forward();
			}
		} else if (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		$messages = $this -> _helper -> flashMessenger -> getMessages();
		if (count($messages)) {
			$message = current($messages);
			$this -> view -> messages = array($message['message']);
			$this -> view -> error = $message['error'];
		}

		$parent = Engine_Api::_() -> getItem($this -> _parentType, $this -> _parentId);

		$filesharingApi = Engine_Api::_() -> ynfilesharing();

		// Get filesharing table
		$file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
		$file_name = $file_table -> info('name');
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');

		// Search Params
		$form = new Advgroup_Form_File_Search();
		$this -> view -> form = $form;
		$form -> setAction($this -> view -> baseUrl() . "/advgroup/file/list");
		$array = array("subject" => "group_" . $group -> group_id);
		$form -> populate($array);
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		if ($viewer -> getIdentity() == 0)
			$form -> removeElement('view');
		$params['parent_type'] = $this -> _parentType;
		$params['parent_id'] = $this -> _parentId;
		$files = array();
		$folders = array();
		if (isset($params['type'])) {
			switch ($params ['type']) {
				case 'file' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					break;
				case 'folder' :
					$folders = $filesharingApi -> selectFoldesByOptions($params);
					break;
				case 'all' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					$folders = $filesharingApi -> selectFoldesByOptions($params);
				default :
					break;
			}
		} else {
			$folders = $filesharingApi -> getSubFolders(NULL, $parent);
		}

		$this -> view -> files = $files;
		$this -> view -> subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders, $this -> _viewer);
		$totalUploaded = Engine_Api::_() -> ynfilesharing() -> getCurrentFolderSizeOfObject($parent);
		$totalUploaded = number_format($totalUploaded / 1048576, 2);
		$this -> view -> totalUploaded = $totalUploaded;
		$maxSizeKB = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'usertotal');
		$space_limit = 0;
		if($this -> _viewer -> getIdentity())
			$space_limit = (int)Engine_Api::_() -> authorization() -> getPermission($this -> _viewer -> level_id, 'user', 'quota');
		if ($space_limit && $space_limit < $maxSizeKB) {
			$maxSizeKB = $space_limit;
		}
		$maxSizeKB = number_format($maxSizeKB / 1024, 2);
		$this -> view -> maxSizeKB = $maxSizeKB;

	}

	public function viewFolderAction() 
	{
		$file_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynfilesharing');

		if (!$file_enable) {
			return $this -> _helper -> requireSubject -> forward();
		}

		$folderId = $this -> _getParam('folder_id', 0);

		if ($folderId != 0) {
			$this -> view -> folder = $folder = Engine_Api::_() -> getItem('folder', $folderId);
		}

		$this -> view -> group = $group = Engine_Api::_() -> getItem('group', $this -> _getParam('parent_id'));
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//check auth folder

		$canCreate = $group -> authorization() -> isAllowed(null, 'folder');

		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'folder');
		if ($canCreate && $levelCreate) {
			$this -> view -> canCreate = true;
		} else {
			$this -> view -> canCreate = false;
		}

		$canUpload = $group -> authorization() -> isAllowed(null, 'file_upload');

		$levelUpload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'file_upload');
		if ($canUpload && $levelUpload) {
			$this -> view -> canUpload = true;
		} else {
			$this -> view -> canUpload = false;
		}

		// check download
		$canDownload = $group -> authorization() -> isAllowed(null, 'file_down');

		$levelDownload = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'file_down');
		if ($canDownload && $levelDownload) {
			$this -> view -> canDownload = true;
		} else {
			$this -> view -> canDownload = false;
		}

		//check both auth remove folder and auth delete file

		$canDeleteRemove = $group -> authorization() -> isAllowed(null, 'file.edit');

		$levelcanDeleteRemove = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'file.edit');
		if ($canDeleteRemove && $levelcanDeleteRemove) {
			$this -> view -> canDeleteRemove = true;
		} else {
			$this -> view -> canDeleteRemove = false;
		}
		$this -> view -> canDelete = $canDelete = $this -> view -> canDeleteRemove;

		$fileTbl = new Ynfilesharing_Model_DbTable_Files();

		$this -> view -> parentType = $parentType = $this -> _getParam('parent_type');
		$this -> view -> parentId = $parentId = $this -> _getParam('parent_id');

		$parentObject = Engine_Api::_() -> getItem($parentType, $parentId);
		$this -> view -> fileTotal = $fileTotal = $fileTbl -> countAllFilesBy($parentObject);
		$this -> view -> maxFileTotal = $maxFileTotal = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'userfile');
		$folderName = $this -> _viewer -> getGuid();
		$this -> view -> totalSizePerUser = $totalSizePerUser = Ynfilesharing_Plugin_Utilities::getFolderSize(Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR);

		$quota = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'usertotal');

		$this -> view -> maxTotalSizePerUser = $maxTotalSizePerUser = $quota * 1024;

		$folderId = $this -> _getParam('folder_id', 0);

		if ($folderId != 0) {
			$this -> view -> folder = $folder = Engine_Api::_() -> getItem('folder', $folderId);
		}

		if ($folder) 
		{
			if(Engine_Api::_() -> core() -> hasSubject('group'))
				Engine_Api::_()->core()->clearSubject('group');
			Engine_Api::_() -> core() -> setSubject($folder);
		}

		if (!$this -> _helper -> requireSubject('folder') -> isValid()) {
			return;
		}

		if (!$folder -> isAllowed($this -> _viewer, 'view')) {
			return $this -> _helper -> requireAuth() -> forward();
		}

		// increase the view count
		$folder -> view_count = $folder -> view_count + 1;
		$this -> view -> folderTags = $folder -> tags() -> getTagMaps();
		$folder -> save();

		$filesharingApi = Engine_Api::_() -> ynfilesharing();
		$folders = $filesharingApi -> getSubFolders($folder);

		$this -> view -> subFolders = $subFolders = $filesharingApi -> getFolders($folders);
		$this -> view -> files = $filesharingApi -> getFilesInFolder($folder);
		$foldersArr = array();
		foreach ($folders as $f) {
			array_push($foldersArr, $f);
		}
		array_push($foldersArr, $folder);

		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($foldersArr);

		// Get filesharing table
		$file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
		$file_name = $file_table -> info('name');
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');
		// Search Params
		$form = new Ynfilesharing_Form_Search();
		$form -> setAction($this -> view -> baseUrl() . "/filesharing/folder/view/" . $folderId);
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$params['user_id'] = $this -> _viewer -> getIdentity();
		$params['folder_id'] = $folderId;
		$files = array();
		$folders = array();
		if (isset($params['type'])) {
			switch ($params ['type']) {
				case 'file' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					break;
				case 'folder' :
					$folders = $filesharingApi -> selectFoldesByOptions($params);
					break;
				case 'all' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					$folders = $filesharingApi -> selectFoldesByOptions($params);
					break;
				default :
					break;
			}
			$this -> view -> files = $filesharingApi -> getFiles($files, 'view', $this -> _viewer);
		} else {
			foreach ($filesharingApi->getSubFolders($folder) as $f) {
				array_push($folders, $f);
			}
			$this -> view -> files = $filesharingApi -> getFilesInFolder($folder);
		}

		//$folders = $filesharingApi->getSubFolders($folder);
		$this -> view -> subFolders = $subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);
		$folderPermissions = $filesharingApi -> getFoldersPermissions($folders, $this -> _viewer);

		$this -> view -> canEdit = $canEdit = $folder -> isAllowed($this -> _viewer, 'edit');
		$this -> view -> canEditPerm = $folder -> isAllowed($this -> _viewer, 'edit_perm');

		if ($folder) {
			$perms = array();
			if (!empty($canEdit)) {
				array_push($perms, 'edit');
			}
			if (!empty($canDelete)) {
				array_push($perms, 'delete');
			}
			$folderPermissions[$folder -> getIdentity()] = $perms;
		}
		$this -> view -> foldersPermissions = $folderPermissions;
	}

}
?>
