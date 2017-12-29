<?php
class Ynwiki_AdminRecycleController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {
    // Get navigation bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynwiki_admin_main', array(), 'ynwiki_admin_main_recycle');

    $this->view->form = $form = new Ynwiki_Form_Admin_Search;
    $form->isValid($this->_getAllParams());
    $params = $form->getValues();
    if(empty($params['orderby'])) $params['orderby'] = 'creation_date';
    if(empty($params['direction'])) $params['direction'] = 'DESC';
    $this->view->formValues = $params;
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $page = Engine_Api::_()->getItem('ynwiki_recycle', $value);
          if( $page ) $page->reDelete();
        }
      }
    }
    
    // Get Page Paginator
    $this->view->paginator = Engine_Api::_()->ynwiki()->getRecyclePaginator($params);
    
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.page',10);
    $this->view->paginator->setItemCountPerPage($items_per_page);
    if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
  }

  /*----- Delete Page Function-----*/
  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->page_id=$id;
    
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      //Process delete action
      try
      {
        
        $page = Engine_Api::_()->getItem('ynwiki_recycle', $id);        
        // delete the page into the database
        $page->reDelete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      // Refresh parent page
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-recycle/delete.tpl');
  }
  
  public function restoreAction(){
        
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        
        $this->view->page_id=$id;
        $page = Engine_Api::_()->getItem('ynwiki_recycle', $id);
       
        //$arr = $page->getChildOfPage(&$data,$id);
        $arr = $page->getBreadCrumNode();         
        $this->view->childs = count($arr);      
        $tmp = array();
        $tmp[]= $id;
        foreach($arr AS $obj)
            $tmp[] = $obj->page_id;
        $tmp = array_unique($tmp);    
        $listid = implode(',',$tmp);   
       
        $query = "INSERT IGNORE INTO `engine4_ynwiki_pages`
                   ( `page_id`,
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
                  FROM `engine4_ynwiki_recycles` WHERE `page_id` IN(".$listid.");"; 
        //echo $query;die;          
        $query1 = "DELETE FROM `engine4_ynwiki_recycles` WHERE page_id IN (".$listid.")";
        // Check post
        if( $this->getRequest()->isPost() )
        {  
            
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
                        
            try
            {
                $db->query($query);
                $db->query($query1);
                $db->commit();
            }catch( Exception $e )
            {
              $db->rollBack();
              throw $e;
            } 
            
            // Refresh parent page
            $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh'=> 10,
                  'messages' => array('')
            ));   
        }
        
        // Output
        //$this->renderScript('admin-recycle/restore.tpl');
           
     }
}