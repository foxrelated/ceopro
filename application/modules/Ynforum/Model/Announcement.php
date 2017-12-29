<?php
class Ynforum_Model_Announcement extends Core_Model_Item_Abstract {
	protected $_parent_type = 'user';

	protected $_owner_type = 'user';

	public function getHref($params = array()) {
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'default', true);
	}

	protected function _update() {
		parent::_update();
	}

	public function setProfile() {
		$table = Engine_Api::_() -> getDbtable('announcements', 'ynforum');
		$where = $table -> getAdapter() -> quoteInto('forum_id = ?', $this -> forum_id);
		$table -> update(array('highlight' => 0, ), $where);
		$this -> highlight = !$this -> highlight;
		$this -> save();
	}

}
