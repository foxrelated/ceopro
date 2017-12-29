<?php
class Ynwiki_AdminManageController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {
    // Get navigation bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynwiki_admin_main', array(), 'ynwiki_admin_main_manage');

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
          $page = Engine_Api::_()->getItem('ynwiki_page', $value);
          if( $page ){
            //$page->reDelete();
            
            $arr = $page->getChildOfPage($data,$page->page_id);
            $tmp = array();
            $tmp[]= $page->page_id;
            foreach($arr AS $obj)
                $tmp[] = $obj->page_id;
            $listid = implode(',',$tmp);   
            Engine_Api::_()->ynwiki()->trash($listid);    
            $page->recycle();
            
          }
        }
      }
    }
    // Get Page Paginator
    $this->view->paginator = Engine_Api::_()->ynwiki()->getPagesPaginator($params);
    
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
     $page = Engine_Api::_()->getItem('ynwiki_page', $id);
        // delete the page into the database
        
        $arr = $page->getChildOfPage($data,$page->page_id);
        $this->view->childs = count($arr);
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      //Process delete action
      try
      {
        //$page = Engine_Api::_()->getItem('ynwiki_page', $id);
        // delete the page into the database
        
        //$arr = $page->getChildOfPage(&$data,$page->page_id);
        $tmp = array();
        $tmp[]= $page->page_id;
        foreach($arr AS $obj)
            $tmp[] = $obj->page_id;
        $listid = implode(',',$tmp);   
        Engine_Api::_()->ynwiki()->trash($listid);    
        $page->recycle();
        //$page->reDelete();        
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
    $this->renderScript('admin-manage/delete.tpl');
  }
}