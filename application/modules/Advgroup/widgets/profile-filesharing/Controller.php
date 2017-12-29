<?php
class Advgroup_Widget_ProfileFilesharingController extends Engine_Content_Widget_Abstract
{
	protected $_parentType;
	protected $_parentId;
	protected $_viewer;
	protected $_childCount;

	public function indexAction()
	{
		$file_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynfilesharing');
		if (!$file_enable)
		{
			return $this -> setNoRender();
		}
		
		$this -> view -> viewer = $this -> _viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _parentType = "group";
		
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
				$this -> _parentId = $this -> _getParam('group_id');
			}
			
		}
		else{
			$group = Engine_Api::_() -> core() -> getSubject();
	   		$this -> _parentId = $group->group_id;
		}
	

		$this -> view -> parentId = $this -> _parentId;
		$this -> view -> parentType = $this -> _parentType;
		
		
		
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//check auth create
			
		$canCreate = $group -> authorization() -> isAllowed(null, 'folder');
		
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'folder');
		if ($canCreate && $levelCreate ) {
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
		
		//Get Viewer, Group 
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		
	
		if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer))
		{
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return $this -> setNoRender();
			}
			else
			if (!$group -> authorization() -> isAllowed($viewer, "view"))
			{
				return $this -> setNoRender();
			}
		}
		else
		if (!$group -> authorization() -> isAllowed($viewer, 'view'))
		{
			return $this -> setNoRender();
		}
	
		$parent = Engine_Api::_() -> getItem($this -> _parentType, $this -> _parentId);

		$filesharingApi = Engine_Api::_() -> ynfilesharing();

		// Get filesharing table
		$file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
		$file_name = $file_table -> info('name');
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');

		// Search Params
		$params['parent_type'] = $this -> _parentType;
		$params['parent_id'] = $this -> _parentId;
		$files = array();
		$folders = array();
		if (isset($params['type']))
		{
			switch ($params ['type'])
			{
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
		}
		else
		{
			$folders = $filesharingApi -> getSubFolders(NULL, $parent);
		}
		
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $folders->count() > 0 ) {
	    	 
	      $this->_childCount = $folders->count();	
	    }
		
		$this -> view -> files = $files;
		//$this->view->subFolders = $folders = $filesharingApi->getSubFolders(NULL, $owner);
		$this -> view -> subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders, $this -> _viewer);
		$totalUploaded = Engine_Api::_()->ynfilesharing()->getCurrentFolderSizeOfObject($parent);
		$totalUploaded = number_format($totalUploaded/1048576, 2);
		$this -> view ->totalUploaded = $totalUploaded;
		$maxSizeKB = (INT)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $this->_viewer, 'usertotal');
		$space_limit = 0;
		if($this->_viewer->getIdentity())
		{
			$space_limit = (int) Engine_Api::_()->authorization()->getPermission($this->_viewer->level_id, 'user', 'quota');
		}
		if($space_limit && $space_limit < $maxSizeKB)
		{
			$maxSizeKB = $space_limit;
		}
		$maxSizeKB = number_format($maxSizeKB/1024,2);
		$this -> view -> maxSizeKB = $maxSizeKB;				
	}

	 public function getChildCount()
	  {
	    return $this->_childCount;
	  }
}