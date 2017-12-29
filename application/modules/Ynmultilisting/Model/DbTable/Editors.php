<?php
class Ynmultilisting_Model_DbTable_Editors extends Engine_Db_Table {
    
	protected $_rowClass = 'Ynmultilisting_Model_Editor';
	
	public function getAllEditors($listingtype_id = null)
	{
		$select = $this -> select();
		if($listingtype_id)
		{
			$select -> where('listingtype_id = ?', $listingtype_id);
		}
		$editors =  $this -> fetchAll($select);
		$editorArr = array();
		foreach($editors as $editor)
		{
			if(!in_array($editor -> user_id, $editorArr))
				$editorArr[] = $editor -> user_id;
		}
		return $editorArr;
	}
	
	public function getEditorsPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getEditorsSelect($params));
	}

	public function getEditorsSelect($params = array()) {
		
		$editorTbl = $this;
		$editorTblName = $editorTbl -> info('name');

		$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
		$userTblName = $userTbl -> info('name');
		
		$listingTypeTbl = Engine_Api::_() -> getItemtable('ynmultilisting_listingtype');
		$listingTypeTblName = $listingTypeTbl -> info('name');	
			
		$select = $editorTbl -> select();
		$select -> setIntegrityCheck(false);

		$select -> from("$editorTblName as editor", "editor.*");
		$select -> joinLeft("$listingTypeTblName as listingtype", "listingtype.listingtype_id = editor.listingtype_id", null) ;
		$select -> joinLeft("$userTblName as user", "user.user_id = editor.user_id", null) ;
		
		if(isset($params['title']))
		{
			$select -> where('user.displayname LIKE ?', '%'.$params['title'].'%');
		}
		
		if(isset($params['listingtype_id']) && $params['listingtype_id'] != 'all')
		{
			$select -> where('editor.listingtype_id = ?', $params['listingtype_id']);
		}
		
		if (empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
			
	    if (!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order('editor_id ASC');
		}
		return $select;
	}
	
    public function checkIsEditor($listingTypeID, $user = null) {
        if (is_null($user)) {
            $user = Engine_Api::_()->user()->getViewer();
        }
		$select = $this -> select() 
						-> where('listingtype_id = ?', $listingTypeID)
						-> where('user_id = ?', $user -> getIdentity())
						-> limit(1);
		$editor = $this -> fetchRow($select);
		if($editor)
		{
			return true;
		}
		return false;
    }
}