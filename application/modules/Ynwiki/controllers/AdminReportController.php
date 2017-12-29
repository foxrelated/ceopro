<?php
class Ynwiki_AdminReportController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {
    // Get navigation bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynwiki_admin_main', array(), 'ynwiki_admin_main_report');

    $this->view->form = $form = new Ynwiki_Form_Admin_SearchReport;
    $form->isValid($this->_getAllParams());
    $params = $form->getValues();
	
	$model = new  Ynwiki_Model_DbTable_Pages();
	$arrPageId = $model->getAllPage();
	if(count($arrPageId)==0)
		$params['page_id'] = 'null';
	else
		$params['page_id'] = $arrPageId;
	
	
    if(empty($params['orderby'])) $params['orderby'] = 'creation_date';
    if(empty($params['direction'])) $params['direction'] = 'DESC';
    $this->view->formValues = $params;
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $report = Engine_Api::_()->getItem('ynwiki_report', $value);
          if( $report ) $report->delete();
        }
      }
    }
    // Get Page Paginator
    $this->view->paginator = Engine_Api::_()->ynwiki()->getReportsPaginator($params);
    
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.page',10);
    $this->view->paginator->setItemCountPerPage($items_per_page);
    if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
  }

  /*----- Delete Report Function-----*/
  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->report_id=$id;
    
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      //Process delete action
      try
      {
        $report = Engine_Api::_()->getItem('ynwiki_report', $id);
        // delete the page into the database
        $report->delete();
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
    $this->renderScript('admin-report/delete.tpl');
  }
}