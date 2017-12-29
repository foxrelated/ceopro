<?php

class Ynadvsearch_Widget_YnFilesharingResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $ynfilesharing_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynfilesharing');
        if(!$ynfilesharing_enable)
        {
            return $this -> setNoRender();
        }
        
        $filesharingApi = Engine_Api::_() -> ynfilesharing();

        // Get filesharing table
        $file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
        $file_name = $file_table -> info('name');
        $folder_table = Engine_Api::_() -> getItemTable('folder');
        $folder_name = $folder_table -> info('name');

        // Search Params
        $form = new Ynfilesharing_Form_Search();
        $params =  Zend_Controller_Front::getInstance()->getRequest()->getParams();
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
            $folders = $filesharingApi -> getSubFolders(NULL, NULL);
        }
        $files_total = $filesharingApi -> selectFilesByOptions(array());
        $folders_total = $filesharingApi -> selectFoldesByOptions(array());
        
        $from_folder_id = 0;
        if(!empty($params['from_folder_id']))
        {
            $from_folder_id = $params['from_folder_id'];
        }
        $formFolderId = $from_folder_id;
        
        $this->view->folders_total = $filesharingApi -> getFolders($folders_total, 'view', $viewer, 0, null);
        $this->view->folders_search = $filesharingApi -> getFolders($folders, 'view', $viewer, 0, null);
        $this->view->files_total = $filesharingApi -> getFiles($files_total, 'view', $viewer);
                
        $limit = Ynfilesharing_Plugin_Constants::DEFAULT_LIMIT;
        $viewedFolders = $filesharingApi -> getFolders($folders, 'view', $viewer, $formFolderId, $limit);
        if (count($viewedFolders) > $limit)
        {
            $this -> view -> canViewMore = true;
            $this -> view -> lastFolder = array_pop($viewedFolders);
        }
        else
        {
            $this -> view -> canViewMore = false;
        }
        $this -> view -> folders = $viewedFolders;
        $this -> view -> files = $filesharingApi -> getFiles($files, 'view', $viewer);
        $this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders);
        $this -> view -> params = $params;

        if (Engine_Api::_() -> authorization() -> isAllowed('folder', $viewer, 'view'))
        {
            $this -> view -> canCreate = Engine_Api::_() -> authorization() -> isAllowed('folder', $viewer, 'create');
        }
        
         //count result
        $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
        $contentType = $table->getContentType('ynfilesharing_folder');
        if ($contentType) $this->view->label_content = $contentType->title;
        
        // Render
        if ($this -> _getParam('view_more') == true)
        {
            $this -> view -> isViewMore = true;
            $this -> _helper -> layout -> disableLayout();
        }
    }

}
