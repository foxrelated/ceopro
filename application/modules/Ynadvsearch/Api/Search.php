<?php
class Ynadvsearch_Api_Search extends Core_Api_Abstract {

    public function getPaginator($text, $types = array(), $from = null, $limit = null) {
        $table = Engine_Api::_ ()->getDbtable ( 'search', 'core' );
        $select = $this->getSelect($text, $types, $from, $limit);
        $results = $table->fetchAll ( $select );
        $results = $this->_checkPermission($results);
        return Zend_Paginator::factory($results);
    }

    public function getSelect($text, $types = array(), $from = null, $limit = null)
    {
        $priority = array('group', 'classified', 'event', 'ynlistings_listing', 'groupbuy_deal');
        $table = Engine_Api::_ ()->getDbtable ( 'search', 'core' );
        $table_name = $table->info ( 'name' );
        $db = $table->getAdapter ();
        $select = $table->select ();
        $text = '%' . preg_replace ( '/\s+/', '%', $text ) . '%';
        $availableTypes = Engine_Api::_ ()->getItemTypes ();
        $types = array_intersect ( $types, $availableTypes );
        $types = array_diff ( $types, array (
                "groupbuy_category",
                "groupbuy_location",
                "groupbuy_param",
                "groupbuy_album",
                "groupbuy_photo",
                "music_playlist_song",
                "ynshare",
                "ynsharebutton",
                "classified_album"
        ) );
        $types = "('" . implode ( "','", $types ) . "')";
        if (!empty($priority)) {
            $priority_str = "('" . implode ( "','", $priority ) . "')";
            $select->from("$table_name", array("$table_name.*","priority" => "IF($table_name.type IN $priority_str, 1, 0)"));
        }
        $select->where ( "$table_name.type IN " . $types );
        $select->where ( "$table_name.title like '$text' OR $table_name.description like '$text'" );
        if (!empty($priority)) {
            $select->order ( "priority DESC" );
        }
        $select->order ( "$table_name.type" );
        $select->order ( "$table_name.id DESC" );
        
        // for search result page
        if ($limit && $from != null) {
            $select->limit ( $limit + 1, $from );
        }
        
        else if ($limit) {
            $select->limit ( $limit );
        }
        return $select;
       
    }

    public function getResults($text, $types = array(), $from, $limit = null) {
        $table = Engine_Api::_ ()->getDbtable ( 'search', 'core' );
        $select = $this->getSelect($text, $types, $from, $limit);
        $results = $table->fetchAll ( $select );
    //    $results = $this->_checkPermission($results);
        return $results;
    }
    
    private function _checkPermission($search) {
        //check permissions
        $search_results = array();
        foreach ($search as $res_item) {
            $type = $res_item->type;
            $item = Engine_Api::_()->getItem($res_item->type, $res_item->id);
            $permission = true;
            $viewer = Engine_Api::_()->user()->getViewer();
            
            if (!$item) continue;
            if (!$item->getIdentity()) continue;
            if ($type == 'user' && ($item->verified != 1 || $item->enabled != 1 || $item->approved != 1)) {
               continue;
            }
   
            switch ($type) {
               case 'file':
                   $parent = Engine_Api::_()->getItem('folder', $item->folder_id);
                   if ($parent) {
                       $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
                   }
                   else {
                       $permission = false;
                   }
                   break;
               
               case 'mp3music_album_song':
                   $parent = Engine_Api::_()->getItem('mp3music_album', $item->album_id);
                   if ($parent) {
                       $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
                   }
                   else {
                       $permission = false;
                   }
                   break;
               
               case 'mp3music_playlist_song':
                   $parent = Engine_Api::_()->getItem('mp3music_playlist', $item->playlist_id);
                   if ($parent) {
                       $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
                   }
                   else {
                       $permission = false;
                   }
                   break;
               
               case 'user':
               case 'blog':
               case 'classified':
               case 'poll':
               case 'ynauction_product':
               case 'contest':
               case 'forum_topic':
               case 'group':
               case 'ynwiki_page':
               case 'event':
               case 'album':
               case 'advalbum_album':
               case 'advalbum_photo':
               case 'music_playlist':
               case 'music_playlist_song':
               case 'ynfundraising_campaign':
               case 'mp3music_playlist':
               case 'mp3music_album':
               case 'groupbuy_deal':
               case 'folder':
               case 'video':           
                   $permission = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($item, $viewer, 'view') -> checkRequire(); 
                   break;
               
               case 'ynlistings_listing':
                    $permission = true;        
               default:
                   break;
            }
            if ($permission) {
                array_push($search_results, $res_item);
            }
        }
        return $search_results;
    }

    public function checkItemPermission($res_item) {
        $type = $res_item->type;
        $item = Engine_Api::_()->getItem($res_item->type, $res_item->id);
        $permission = true;
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if (!$item) return false;
        if (!$item->getIdentity()) return false;
        if ($type == 'user' && ($item->verified != 1 || $item->enabled != 1 || $item->approved != 1)) 
        {
             if($viewer -> isAdminOnly())
			 {
			 	return true;
			 }
			 else {
				 return false;
			 }
        }
   
            switch ($type) {
               case 'file':
               $parent = Engine_Api::_()->getItem('folder', $item->folder_id);
               if ($parent) {
                   $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
               }
               else {
                   $permission = false;
               }
               break;
           
           case 'mp3music_album_song':
               $parent = Engine_Api::_()->getItem('mp3music_album', $item->album_id);
               if ($parent) {
                   $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
               }
               else {
                   $permission = false;
               }
               break;
           
           case 'mp3music_playlist_song':
               $parent = Engine_Api::_()->getItem('mp3music_playlist', $item->playlist_id);
               if ($parent) {
                   $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
               }
               else {
                   $permission = false;
               }
               break;
           
           case 'user':
           case 'blog':
           case 'classified':
           case 'poll':
           case 'ynauction_product':
           case 'contest':
           case 'forum_topic':
           case 'group':
           case 'ynwiki_page':
           case 'event':
           case 'album':
           case 'advalbum_album':
           case 'advalbum_photo':
           case 'music_playlist':
           case 'music_playlist_song':
           case 'ynfundraising_campaign':
           case 'mp3music_playlist':
           case 'mp3music_album':
           case 'groupbuy_deal':
           case 'folder':
           case 'video':           
               $permission = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($item, $viewer, 'view') -> checkRequire(); 
               break;
           
           case 'ynlistings_listing':
                $permission = true;        
           default:
               break;
        }
        return $permission;
    }
}