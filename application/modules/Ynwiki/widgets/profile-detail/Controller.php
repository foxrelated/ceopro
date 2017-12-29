<?php
class Ynwiki_Widget_ProfileDetailController extends Engine_Content_Widget_Abstract
{
   	
  public function indexAction()
  {
  	// Check permission
  
  	$this->getElement()->removeDecorator('Title');
  	$viewer = Engine_Api::_()->user()->getViewer();
  	$request = Zend_Controller_Front::getInstance()->getRequest();
  	$page = Engine_Api::_()->getItem('ynwiki_page', $request->getParam('pageId'));
  	
	
  	 
  	// Prepare data
  	$pageTable = Engine_Api::_()->getDbtable('pages', 'ynwiki');
  	
  	$this->view->page = $page;
  	$this->view->owner = $owner = $page->getOwner();
  	$this->view->viewer = $viewer;
  	if (!$viewer->getIdentity()){
  		$this->view->can_rate = $can_rate = 0;
  	}
  	else{
  		$this->view->can_rate = $can_rate = Engine_Api::_()->ynwiki()->canRate($page,$viewer->getIdentity());
  	}
  	/*
  	 if($page->isOwner($viewer) && Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.rate', 0) == 0)
  	 {
  	$this->view->can_rate = $can_rate = 0;
  	}*/
  	if( !$page->isOwner($viewer) ) {
  		$pageTable->update(array(
  				'view_count' => new Zend_Db_Expr('view_count + 1'),
  		), array(
  				'page_id = ?' => $page->getIdentity(),
  		));
  	}
  	//Get tags
  	$t_table = Engine_Api::_()->getDbtable('tags', 'core');
  	$tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
  	$p_table = Engine_Api::_()->getItemTable('ynwiki_page');
  	$tName = $t_table->info('name');
  	$tmName = $tm_table->info('name');
  	$pName = $p_table->info('name');
  	 
  	$filter_select = $tm_table->select()->from($tmName,"$tmName.*")
  	->setIntegrityCheck(false)
  	->joinLeft($pName,"$pName.page_id = $tmName.resource_id",'')
  	//->where("$pName.draft = 0")
  	->where("$pName.page_id = ?",$page->getIdentity());
  	
  	$select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
  	$select->joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
  	$select  ->order("$tName.text");
  	$select  ->group("$tName.text");
  	$select  ->where("t.resource_type = ?","ynwiki_page");
  	$this->view->tags = $tags = $t_table->fetchAll($select);
  	if($viewer->getIdentity() > 0)
  		Engine_Api::_()->ynwiki()->saveView($page,$viewer->getIdentity());
  	
    
  }
  private function checkparentallow($page,$viewer,$action){
  	$i = 1;
  	while($page->parent_page_id != 0 && $page != 20) {
  
  		if(!$this->_helper->requireAuth()->setAuthParams($page->getParentPage(), $viewer, $action)->isValid()){
  			return false;
  		}
  		$page = $page->getParentPage();
  		$i++;
  	}
  	return true;
  }

  
}