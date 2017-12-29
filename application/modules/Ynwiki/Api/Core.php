<?php

class Ynwiki_Api_Core extends Core_Api_Abstract
{

    protected $_moduleName = 'ynwiki';

     public function checkparentallow($page,$viewer,$action){        
        $i = 0; 
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
        while($i < 20 && is_object($page)) {
            if(!$helper->setAuthParams($page, $viewer, $action)->checkRequire()) {                   
                return false;
            }       
			$page = $page->getParentPage();
            $i++;            
        }                    
        
        // echo "true";
        return true;    
    }   
    /**
     * @param array $param  etc array('user_id'=>12)
     * @return Zend_Db_Select
     *
     */
    

    
    public function selectWikiPages($params = array())
    {
        $model = new Ynwiki_Model_DbTable_Pages;
        $select = $model -> select();
        return $select;
    }
    public function getSpaces($limit = null)
    {   
        $model = new Ynwiki_Model_DbTable_Pages;
        $select = $model -> select()->where('level = ?',0)->order("page_id DESC");
        if($limit)
            $select->limit($limit);
        return $model->fetchAll($select); 
    }
    public function checkTitle($title, $parent_id)
    {
        $model = new Ynwiki_Model_DbTable_Pages;
        $select = $model -> select()->where('title = ?',$title);
        $select->where("parent_page_id = ?",$parent_id);
        $row = $model->fetchRow($select); 
        if($row)
            return true;
        else
            return false; 
    }
    public function getChilds()
    {
        $model = new Ynwiki_Model_DbTable_Pages;
        $select = $model -> select()->where('parent_page_id = ?',1);
        return $model->fetchAll($select); 
    }
    public function canRate($row,$user_id)
    {
        $rateTable = Engine_Api::_()->getDbtable('rates', 'ynwiki');
        $select = $rateTable->select()
        ->where('page_id = ?', $row->getIdentity())
        ->where('user_id = ?', $user_id);

        return (count($rateTable->fetchAll($select)) > 0)?0:1;
    }
    public function saveView($row,$user_id)
    {
        $viewTable = Engine_Api::_()->getDbtable('views', 'ynwiki');
        $select = $viewTable->select()
        ->where('page_id = ?', $row->getIdentity())
        ->where('user_id = ?', $user_id);
        $viewUser = $viewTable->fetchRow($select);
        if(!$viewUser)
        {
            $view_table = Engine_Api::_()->getItemTable('ynwiki_view');
            $view = $view_table->createRow();
            $view->user_id = $user_id;
            $view->creation_date  = date('Y-m-d H:i:s');
            $view->page_id = $row->page_id;
            $view->save();
        }
        else
        {
            $viewUser->creation_date =  date('Y-m-d H:i:s');
            $viewUser->save();
        }
        return true; 
    }
    public function saveEdit($row,$user_id)
    {
        $viewTable = Engine_Api::_()->getDbtable('edits', 'ynwiki');
        $select = $viewTable->select()
        ->where('page_id = ?', $row->getIdentity())
        ->where('user_id = ?', $user_id);
        $viewUser = $viewTable->fetchRow($select);
        if(!$viewUser)
        {
            $view_table = Engine_Api::_()->getItemTable('ynwiki_edit');
            $view = $view_table->createRow();
            $view->user_id = $user_id;
            $view->creation_date  = date('Y-m-d H:i:s');
            $view->page_id = $row->page_id;
            $view->save();
        }
        else
        {
            $viewUser->creation_date =  date('Y-m-d H:i:s');
            $viewUser->save();
        }
        return true; 
    }
    public function getAVGrate($page_id)
    {
        $rateTable = Engine_Api::_()->getDbtable('rates', 'ynwiki');
        $select = $rateTable->select()
        ->from($rateTable->info('name'), 'AVG(rate_number) as rates')
        ->group("page_id")
        ->where('page_id = ?', $page_id);
        $row = $rateTable->fetchRow($select);
        return ((count($row) > 0)) ? $row->rates : 0;
    }
    public static function partialViewFullPath($partialTemplateFile) 
    {
        $ds = DIRECTORY_SEPARATOR;
        return "application{$ds}modules{$ds}Ynwiki{$ds}views{$ds}scripts{$ds}{$partialTemplateFile}";
    }
    public function getFollow($user_id = null, $page_id = null)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $followTable = Engine_Api::_()->getDbtable('follows', 'ynwiki');
        $select = $followTable->select()
        ->where('page_id = ?', $page_id)
        ->where('user_id = ?',$user_id);
        $row = $followTable->fetchRow($select);
        return $row;
    }
    public function getFavourite($user_id = null, $page_id = null)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'ynwiki');
        $select = $favouriteTable->select()
        ->where('page_id = ?', $page_id)
        ->where('user_id = ?',$user_id);
        $row = $favouriteTable->fetchRow($select);
        return $row;
    }
     public function getPagesPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getPagesSelect($params));
                
        if( !empty($params['page']) )
        {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     public function getReportsPaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getReportsSelect($params));
		
        if( !empty($params['page']) )
        {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     public function getReportsSelect($param = null)
     {
         $table = Engine_Api::_()->getDbtable('reports', 'ynwiki');
         $Name = $table->info('name');
         $select = $table->select()->from($Name);
         if(isset($param['page_title']) && $param['page_title'] != "")
         {
             $title = $param['page_title'];
             $select->joinLeft("engine4_ynwiki_pages","engine4_ynwiki_pages.page_id = $Name.page_id","")
                    ->where("engine4_ynwiki_pages.title Like ?","%$title%");
         }
         if(isset($param['type']) && $param['type'] != "")
         {
             $select->where("type = ?",$param['type']);
         }
		 if(isset($param['page_id']) && $param['page_id'] != "")
         {
         	
             $select->where("page_id IN (?)",$param['page_id']);
         }
		
         $select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :'creation_date '.$param['direction']);
       
	   	
        return $select;
     }
     public function getPagesSelect($param = null)
     {
        $table = Engine_Api::_()->getDbtable('pages', 'ynwiki');
        $Name = $table->info('name');
         
         // Get Tagmaps table
        $tags_table = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tags_name = $tags_table->info('name');
         
         $select = $table->select()->from($Name)->setIntegrityCheck(false);
         if(isset($param['follow']) && $param['follow'] == true && $param['user_id'])
         {
             $select->joinLeft("engine4_ynwiki_follows","engine4_ynwiki_follows.page_id = $Name.page_id","")
             ->where("engine4_ynwiki_follows.user_id = ?",$param['user_id']);
         }
         if(isset($param['favourite']) && $param['favourite'] == true && $param['user_id'])
         {
             $select->joinLeft("engine4_ynwiki_favourites","engine4_ynwiki_favourites.page_id = $Name.page_id","")
             ->where("engine4_ynwiki_favourites.user_id = ?",$param['user_id']);
         }
         if(isset($param['edit']) && $param['edit'] != "" && isset($param['profile']) && $param['profile'] != "")
         {
             $select->join("engine4_ynwiki_edits","engine4_ynwiki_edits.page_id = $Name.page_id","")
             ->where("engine4_ynwiki_edits.user_id = ?", $param['profile']);
         }
         if(isset($param['space']) && $param['space'] == true)
         {
             $select->where("level = 0");
         }
         
         if((isset($param['name']) && $param['name'] != "") || isset($param['title']))
         {
             if (isset($param['title']))
                $name = $param['title'];
             else 
                $name = $param['name'];
             $select->where("title Like ?","%$name%");
         }
         if(isset($param['owner']) && $param['owner'] != "")
         {
             $name = $param['owner'];
             $select->join("engine4_users","engine4_users.user_id = $Name.user_id","");
             $select->where("engine4_users.displayname Like ?","%$name%");
         }
        //Tag filter
        if( !empty($param['tag']) )
        {
          $select
            ->joinLeft($tags_name, "$tags_name.resource_id = $Name.page_id","")
            ->where($tags_name.'.resource_type = ?', 'ynwiki_page')
            ->where($tags_name.'.tag_id = ?', $param['tag']);
        }
         
        if(!isset($param['direction'])) 
            $param['direction'] = "DESC";

        //Order by filter
        if(isset($param['orderby']) && $param['orderby'] == 'displayname'){
           $select -> join('engine4_users as u',"u.user_id = $Name.user_id",'')
                      -> order("u.displayname ".$param['direction']);
        }
        else{
            if(isset($param['filter']) && $param['filter'] != '')
            {
               $select->order($Name.".".$param['filter'].' '.$param['direction']); 
            }
            else
            {
                $select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :'creation_date '.$param['direction']);
            }
            
        }

        //Parent_type filter
        if(isset($param['parent_type']))
        {
          $select->where($Name.'.parent_type = ?',$param['parent_type']);
        }
       
        //Parent id filter
        if(isset($param['parent_id']))
        {
          $select->where($Name.'.parent_id = ?',$param['parent_id']);
        }
       // echo $select;die;
        return $select;
     }
     public function getAllAdmins()
     {
         $table = Engine_Api::_()->getItemtable('user');
         $Name = $table->info('name');
         $select = $table->select();
         $select->where("level_id = 1")
         ->OrWhere("level_id = 2");
         $users = $table->fetchAll($select);
         return $users;
     }
     public function trash($str){
        $query = 'INSERT IGNORE INTO `engine4_ynwiki_recycles` ( `page_id`,
                  `parent_page_id`,
                  `revision_id`,
                  `user_id`,
                  `photo_id`,
                  `creator_id`,
                  `owner_type`,
                  `parent_type`,
                  `parent_id`,
                  `slug`,
                  `view_count`,
                  `comment_count`,
                  `like_count`,
                  `search`,
                  `notify`,
                  `draft`,
                  `category_id`,
                  `follow_count`,
                  `rate_ave`,
                  `favourite_count`,
                  `rate_count`,
                  `title`,
                  `description`,
                  `body`,
                  `creation_date`,
                  `modified_date`,
                  `level`)
                  
                  SELECT
                  `page_id`,
                  `parent_page_id`,
                  `revision_id`,
                  `user_id`,
                  `photo_id`,
                  `creator_id`,
                  `owner_type`,
                  `parent_type`,
                  `parent_id`,
                  `slug`,
                  `view_count`,
                  `comment_count`,
                  `like_count`,
                  `search`,
                  `notify`,
                  `draft`,
                  `category_id`,
                  `follow_count`,
                  `rate_ave`,
                  `favourite_count`,
                  `rate_count`,
                  `title`,
                  `description`,
                  `body`,
                  `creation_date`,
                  `modified_date`,
                  `level`
                  FROM `engine4_ynwiki_pages` WHERE `page_id` IN('.$str.');';            
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try
        {
            $db->query($query);            
            $db->commit();           
        }catch( Exception $e )
        {            
          $db->rollBack();
          throw $e;
        }       
     }
     
          
     public function getRecyclePaginator($params = array())
     {
        $paginator = Zend_Paginator::factory($this->getRecycleSelect($params));
        if( !empty($params['page']) )
        {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
     }
     public function getRecycleSelect($param = null)
     {
        $table = Engine_Api::_()->getDbtable('recycles', 'ynwiki');        
        $Name = $table->info('name');
         
         // Get Tagmaps table
        $tags_table = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tags_name = $tags_table->info('name');
         
         $select = $table->select()->from($Name)->setIntegrityCheck(false);
         if(isset($param['follow']) && $param['follow'] == true && $param['user_id'])
         {
             $select->joinLeft("engine4_ynwiki_follows","engine4_ynwiki_follows.page_id = $Name.page_id","")
             ->where("engine4_ynwiki_follows.user_id = ?",$param['user_id']);
         }
         if(isset($param['favourite']) && $param['favourite'] == true && $param['user_id'])
         {
             $select->joinLeft("engine4_ynwiki_favourites","engine4_ynwiki_favourites.page_id = $Name.page_id","")
             ->where("engine4_ynwiki_favourites.user_id = ?",$param['user_id']);
         }
         if(isset($param['edit']) && $param['edit'] != "" && isset($param['profile']) && $param['profile'] != "")
         {
             $select->join("engine4_ynwiki_edits","engine4_ynwiki_edits.page_id = $Name.page_id","")
             ->where("engine4_ynwiki_edits.user_id = ?", $param['profile']);
         }
         if(isset($param['space']) && $param['space'] == true)
         {
             $select->where("level = 0");
         }
         
         if(isset($param['name']) && $param['name'] != "")
         {
             $name = $param['name'];
             $select->where("title Like ?","%$name%");
         }
         if(isset($param['owner']) && $param['owner'] != "")
         {
             $name = $param['owner'];
             $select->join("engine4_users","engine4_users.user_id = $Name.user_id","");
             $select->where("engine4_users.displayname Like ?","%$name%");
         }
        //Tag filter
        if( !empty($param['tag']) )
        {
          $select
            ->joinLeft($tags_name, "$tags_name.resource_id = $Name.page_id","")
            ->where($tags_name.'.resource_type = ?', 'ynwiki_page')
            ->where($tags_name.'.tag_id = ?', $param['tag']);
        }
         
        if(!isset($param['direction'])) 
            $param['direction'] = "DESC";

        //Order by filter
        if(isset($param['orderby']) && $param['orderby'] == 'displayname'){
           $select -> join('engine4_users as u',"u.user_id = $Name.user_id",'')
                      -> order("u.displayname ".$param['direction']);
        }
        else{
            if(isset($param['filter']) && $param['filter'] != '')
            {
               $select->order($Name.".".$param['filter'].' '.$param['direction']); 
            }
            else
            {
                $select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :'creation_date '.$param['direction']);
            }
            
        }

        //Parent_type filter
        if(isset($param['parent_type']))
        {
          $select->where($Name.'.parent_type = ?',$param['parent_type']);
        }

        //Parent id filter
        if(isset($param['parent_id']))
        {
          $select->where($Name.'.parent_id = ?',$param['parent_id']);
        }
        return $select;
     }
}
