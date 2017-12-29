<?php
class Advgroup_Model_DbTable_Mappings extends Engine_Db_Table
{
  	protected $_name = 'advgroup_mappings';
	
	public function getAlbumsPaginator($params = array())
    {
   	 	return Zend_Paginator::factory($this->getAlbumsSelect($params));
    }
  	
    public function getListingsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getListingsSelect($params));
    }
    
	public function getVideoIdsMapping($group_id)
	{
		$select = $this -> select() -> from($this, new Zend_Db_Expr("`item_id`"));
		$select -> where("type = 'video'");
		$select -> where("group_id =?", $group_id);
		return $this->fetchAll($select);
	}
	
	public function deleteItem($params = array()){
		$table = Engine_Api::_()->getItemTable('advgroup_mapping');
		$tableName = $table->info('name');
		$db = Engine_Api::_() -> getDbtable('mappings', 'advgroup') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$db->delete($tableName, array(
			    'type = ?' => $params['type'],
			    'item_id = ?' => $params['item_id']
			));
			$db -> commit();
			
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return $e;
		}
		return "true";
	}
  public function getAlbumsSelect($params = array()){

    //Get album table
    $table = Engine_Api::_()->getItemTable('advgroup_mapping');
    $tableName = $table->info('name');
	
	$table_music = Engine_Api::_()->getItemTable($params['ItemTable']);
    $tableName_music = $table_music->info('name');
	$select = $table_music->select()->from(array('p' => $tableName_music ));
	
	 // check join condition
    if( $params['ItemTable'] == 'mp3music_album')  {
      	$select -> setIntegrityCheck(false)
                -> join("$tableName as h","p.album_id = h.item_id",'');
    }
	else {
		$select	-> setIntegrityCheck(false)
                -> join("$tableName as h","p.playlist_id = h.item_id",'');
	}
	
	//type
    if(isset ($params['ItemTable'])){
        $select
        ->where("h.type = ?", $params['ItemTable']);
    }

    //Group
    if(isset ($params['group_id'])){
        $select
        ->where("h.group_id = ?", $params['group_id']);
    }
	
	//Search
    if(!empty($params['search'])){
      $select->where('p.title LIKE ? OR p.Description LIKE ?','%'.$params['search'].'%');
    }

     // User
    if( !empty($params['user_id']) ) {
      $select
        ->where('h.user_id = ?', $params['user_id']);
    }


    // Order
    switch( $params['order'] ) {
      case 'comment':
          $select -> order ('p.comment_count DESC');
      break;
	  case 'play':
          $select -> order ('p.play_count DESC');
      break;
      case 'recent':
      default:
          $select -> order('p.creation_date DESC');
      break;
    }
    return $select;
  }

    public function getListingsSelect($params = array()) {
        
        $table = Engine_Api::_()->getItemTable('ynlistings_listing');
        $Name = $table -> info('name');
        
        $postTable = Engine_Api::_()->getItemTable('ynlistings_post');
        $postTblName = $postTable->info('name');

        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynlistings', 'listings');
        }
        else
        {
            $select = $table->select()->from("$Name", array("$Name.*"));
        }

        $select -> setIntegrityCheck(false); 
        $select -> from("$Name as listing", "listing.*, COUNT($postTblName.post_id) as discuss_count");
        $select -> joinLeft("$postTblName","$postTblName.listing_id = listing.listing_id", "");
        $select -> group('listing.listing_id');
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'ynlistings_listing';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('listing.listing_id IN (?)', $ids);    
        }    
        else {
            $select -> where('listing.listing_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('listing.title LIKE ?','%'.$params['search'].'%');
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        if (isset($params['manage']) && $params['manage']) {
            $select->where(new Zend_Db_Expr("IF(listing.user_id = $user_id, 1, listing.search = 1 AND listing.status = 'open' AND listing.approved_status = 'approved')"));
        }
        
        else {
            $select
                ->where('listing.search = ?', 1)
                ->where('listing.status = ?', 'open')
                ->where('listing.approved_status = ?', 'approved');
        }
        // Order
        switch( $params['order'] ) {
            case 'recent':
                $select -> order('listing.listing_id DESC');
                break;
            case 'view':
                $select -> order('listing.view_count DESC');
                break;
            case 'like':
                $select -> order('listing.like_count DESC');
                break;
            case 'discussion':
                $select -> order('discuss_count DESC');
                break;
            case 'title':
                $select -> order('listing.title ASC');
                break;
            default:
                $select -> order('listing.listing_id DESC');
                break;
        }
        
        return $select;
    }
	
	public function getSocialMusicPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getSocialMusicSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getSocialMusicSelect($params = array()) {
        
        $table_music = Engine_Api::_()->getItemTable($params['ItemTable']);
        $select = $table_music->select();
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
		$type = $params['type'];
        if (!empty($ids)) {
            $select -> where($type."_id IN (?)", $ids);    
        }    
        else {
            $select -> where($type."_id IN (0)");  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ? OR description LIKE ?','%'.$params['search'].'%');
        }
        
        // Order
        if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = $type."_id";
					$params['direction'] = 'DESC';
					break;
					
				case 'most_liked':
					$params['order'] = 'like_count';
					$params['direction'] = 'DESC';
					break;
				
				case 'most_discussed':
					$params['order'] = 'comment_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_viewed':
					$params['order'] = 'view_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_played':
					$params['order'] = 'play_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'a_z':
					$params['order'] = 'title';
					$params['direction'] = 'ASC';
					break;
					
				case 'z_a':
					$params['order'] = 'title';
					$params['direction'] = 'DESC';
					break;
				
				default:
					break;
			}
		}
		
		if(empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
		
	    if(!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order($type."_id DESC");
		}
        return $select;
    }

    public function getItemIdsMapping($type, $params = array()) {
        $select = $this -> select() -> from($this, new Zend_Db_Expr("`item_id`"));
        $select -> where("type = ?", $type);
        if (isset($params['group_id'])) {
            $select -> where("group_id = ?", $params['group_id']);
        }
        if (isset($params['user_id'])) {
            $select -> where("user_id = ?", $params['user_id']);
        }
        $select -> order("creation_date DESC");
        $mapping_ids = $this->fetchAll($select);
        $ids = array();
        foreach($mapping_ids as $mapping_id)
        {
            $ids[] = $mapping_id -> item_id;
        }
        return $ids;
    }
}