<?php
class Ynmultilisting_Model_Listingtype extends Core_Model_Item_Abstract 
{
    public function getAllCategories()
    {
    	$table = Engine_Api::_()->getItemTable('ynmultilisting_category');
    	$select = $table -> select() -> where ("listingtype_id = ?", $this -> getIdentity());
    	return $table -> fetchAll($select);
    }
    
	public function getCategories() {
        $table = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting');
        $tree = array();
        $node = $this -> getTopCategory();
        $table->appendChildToTree($node, $tree);
        return $tree;
    }
    
    public function getTopCategory(){
    	$table = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting');
    	$select = $table 
    	-> select()
    	-> where("level = 0")
    	-> where("listingtype_id = ?", $this -> getIdentity())
		-> limit(1);
    	return $table -> fetchRow($select);
    }

    public function getQuicklinks($params = array()) {
    	$results = array();	
  		$rows = Engine_Api::_()->getDbTable('quicklinks', 'ynmultilisting')->getListingTypeQuicklinks($this->getIdentity(), $params);
    	if (empty($params['hasListing'])) {
    		return $rows;
    	}
		foreach ($rows as $row) {
			if ($row->getTotalListings()) {
				$results[] = $row;
			}
		}
		return $results;
	}
    
    public function getPromotion() {
        return Engine_Api::_()->getDbTable('promotions', 'ynmultilisting')->getListingTypePromotion($this->getIdentity());
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getAllListings($params = array()) {
        return  Engine_Api::_()->getDbTable('listings', 'ynmultilisting')->getListingTypeListings($this->getIdentity(), $params);
    }
    
    public function getListingCount()
    {
    	$listings =  $this -> getAllListings();
    	return count($listings);
    }
    
    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynmultilisting_general',
            'controller' => 'index',
            'action' => 'index',
            'listingtype_id' => $this->getIdentity(),
        ), $params);
        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, true);
    }
    
    public function getTopCategories() {
        $categories = ($this->manage_menu) ? Engine_Api::_()->getDbTable('categories', 'ynmultilisting')->getListingTypeCategories($this->getIdentity(), true) : array_slice(Engine_Api::_()->getDbTable('categories', 'ynmultilisting')->getListingTypeCategories($this->getIdentity()), 0, 6);
        if(!$this->manage_menu) unset($categories[0]);
        return $categories;
    }
    
    public function getMoreCategories() {
        $categories = ($this->manage_menu) ? Engine_Api::_()->getDbTable('categories', 'ynmultilisting')->getListingTypeCategories($this->getIdentity(), false, true) : array_slice(Engine_Api::_()->getDbTable('categories', 'ynmultilisting')->getListingTypeCategories($this->getIdentity()), 0, 6);
        if(!$this->manage_menu) unset($categories[0]);
        return $categories;
    }

	public function moveListingTo($newTypeId)
	{
		$table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
		$db = $table -> getAdapter();
		$tableName = $table -> info('name');
		try {
			$db -> beginTransaction();
			$db -> update($tableName, array(
				'listingtype_id' => $newTypeId,
			), array(
				'listingtype_id = ?' => $this->getIdentity(),
			));
			$db -> commit();
		} 
		catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}
	
	public function moveCategoryTo($newTypeId)
	{
		$table = Engine_Api::_()->getItemTable('ynmultilisting_category');
		$db = $table -> getAdapter();
		$tableName = $table -> info('name');
		try {
			$db -> beginTransaction();
			$db -> update($tableName, array(
				'listingtype_id' => $newTypeId,
			), array(
				'listingtype_id = ?' => $this->getIdentity(),
			));
			$db -> commit();
		} 
		catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}
    
    //HOANGND check if listing has category
    public function hasCategory($category_id) {
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            if ($category_id == $category->getIdentity()) {
                return true;
            }
        }
        return false;
    }
    
    //HOANGND check permission on listing type
    public function checkPermission($user = null, $type, $name) {
        if (!$user) {
            $user = Engine_Api::_()->user()->getViewer();
        }
        $level_id = ($user->getIdentity()) ? $user->level_id : 5;
        $listingtypePermission = Engine_Api::_()->getDbTable('memberlevelpermission', 'ynmultilisting')->getAllowed($type, $level_id, $name, $this->getIdentity());
        if (empty($listingtypePermission)) {
            return Engine_Api::_()->authorization()->getPermission($level_id, $type, $name);
        }
        else {
            return $listingtypePermission[$name];
        }
    }
    
    //HOANGND check getmission on listing type
    public function getPermission($user = null, $type, $name) {
        if (is_null($user)) {
            $user = Engine_Api::_()->user()->getViewer();
        }
        $level_id = ($user->getIdentity()) ? $user->level_id : 5;
        $listingtypePermission = Engine_Api::_()->getDbTable('memberlevelpermission', 'ynmultilisting')->getAllowed($type, $level_id, $name, $this->getIdentity());
        if (empty($listingtypePermission)) {
            $value = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed($type, $level_id, $name);
		    if (is_null($value)) {
                $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
                $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $level_id)
                    ->where('type = ?', $type)
                    ->where('name = ?', $name));
                if ($row) {
                    $value = $row->value;
                }
            }
            return $value;
        }
        else {
            if (in_array($name, array('auth_view', 'auth_comment', 'auth_share', 'auth_photo', 'auth_video', 'auth_discussion'))) {
                $listingtypePermission[$name] = json_decode($listingtypePermission[$name]);
            }
            return $listingtypePermission[$name];
        }
    }

    //HOANGND check allow on listing type
    public function checkAllow($user = null, $type, $name, $object) {
        if (is_null($user)) {
            $user = Engine_Api::_()->user()->getViewer();
        }
        $level_id = ($user->getIdentity()) ? $user->level_id : 5;
        $listingtypePermission = Engine_Api::_()->getDbTable('memberlevelpermission', 'ynmultilisting')->getAllowed($type, $level_id, $name, $this->getIdentity());
        if (empty($listingtypePermission)) {
            return $object->authorization()->isAllowed($user, $name);
        }
        else {
            $value = $listingtypePermission[$name];
            if (!$value) return false;
            if ($value == '2') return true;
            return Engine_Api::_()->authorization()->getAdapter('context')->isAllowed($object, $user, $name);
        }
    }
}