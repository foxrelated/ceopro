<?php
class Ynmultilisting_Model_DbTable_Wishlists extends Engine_Db_Table {
    protected $_rowClass = 'Ynmultilisting_Model_Wishlist';
	
	public function getAvailableWishlists($user_id, $listingtype_id) {
		$select = $this->select()->where('user_id = ?', $user_id)->where('listingtype_id = ?', $listingtype_id);
		return $this->fetchAll($select);
	}
	
	public function getWishlistSelect($params = array()) {
		$tableName = $this->info('name');

    	$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
    	$userTblName = $userTbl -> info('name');

    	$select = $this -> select();
    	$select -> setIntegrityCheck(false);

    	$select -> from("$tableName as wishlist", "wishlist.*");
    	$select
    	-> joinLeft("$userTblName as user", "user.user_id = wishlist.user_id", "");


    	if (!empty($params['title']))  {
    		$select->where('wishlist.title LIKE ?', '%'.$params['title'].'%');
    	}
    	
    	if (!empty($params['owner_name']))  {
    		$select->where('user.displayname LIKE ? OR user.email LIKE ?', '%'.$params['owner_name'].'%');
    	}
    	
		if (!empty($params['owner_type']) && $params['owner_type'] == 'friend') {
			$friendSelect = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();
			$user_ids = array(Engine_Api::_()->user()->getViewer()->getIdentity());
			$friends = $userTbl->fetchAll($friendSelect);
			foreach ($friends as $friend) {
				$user_ids[] = $friend->getIdentity();
			}
			$select->where('user.user_id IN (?)', $user_ids);
		}
    	
		if (!empty($params['user_id']))  {
    		$select->where('wishlist.user_id = ?', $params['user_id']);
    	}
		
		if (!empty($params['listingtype_id']))  {
    		$select->where('wishlist.listingtype_id = ?', $params['listingtype_id']);
    	}
    	
		$select->order('wishlist.wishlist_id DESC');
		
    	return $select;
    }
}