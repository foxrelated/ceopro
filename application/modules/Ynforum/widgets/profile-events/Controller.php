<?php

class Ynforum_Widget_ProfileEventsController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		if (!Engine_Api::_() -> hasItemType('event')) {
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
		if(!Engine_Api::_() -> hasItemType('event'))
		{
			 return $this->setNoRender();
		}	
		// Get paginator
		$table = Engine_Api::_() -> getItemTable('event');
		$name = $table -> info('name');
		$h_table = Engine_Api::_() -> getDbTable('highlights', 'ynforum');
		$h_name = $h_table -> info('name');
		$select = $table -> select() -> from($name)
			-> joinLeft($h_name, "$h_name.item_id = $name.event_id AND $h_name.type = 'event'", '') 
			-> where('search = ?', 1) 
			-> where('parent_id = ?', $subject -> getIdentity())
			-> where('parent_type = ?', 'forum')
			-> order ("highlight DESC") 
			-> order("$name.creation_date" . ' DESC');
		$itemCountPerPage = $this -> _getParam('itemCountPerPage', 5);
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($itemCountPerPage);
		$this -> view -> forum = $subject;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$subject -> checkPermission($viewer, 'forum', 'fevent.create') && !$paginator -> getTotalItemCount())
		{
			return $this -> setNoRender();
		}
	}

}
