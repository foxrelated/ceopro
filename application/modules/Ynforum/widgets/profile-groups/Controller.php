<?php

class Ynforum_Widget_ProfileGroupsController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		if (!Engine_Api::_() -> hasItemType('group')) {
			return $this -> setNoRender();
		}
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		// Get subject and check auth
		$subject = Engine_Api::_() -> core() -> getSubject();
		if (!$subject -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> setNoRender();
		}
		if(!Engine_Api::_() -> hasItemType('group'))
		{
			 return $this->setNoRender();
		}	
		// Get paginator
		$table = Engine_Api::_() -> getItemTable('group');
		$name = $table -> info('name');
		$h_table = Engine_Api::_() -> getDbTable('highlights', 'ynforum');
		$h_name = $h_table -> info('name');
		$select = $table -> select() -> from($name)
			-> join($h_name, "$h_name.item_id = $name.group_id AND $h_name.type = 'group'", '') 
			-> where('search = ?', 1) 
			-> where('forum_id = ?', $subject -> getIdentity()) 
			-> order("highlight DESC") 
			-> order("$name.creation_date" . ' DESC');
		$itemCountPerPage = $this -> _getParam('itemCountPerPage', 5);
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($itemCountPerPage);
		$this -> view -> forum = $subject;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$subject -> checkPermission($viewer, 'forum', 'fgroup.create') && !$paginator -> getTotalItemCount())
		{
			return $this -> setNoRender();
		}
	}

}
