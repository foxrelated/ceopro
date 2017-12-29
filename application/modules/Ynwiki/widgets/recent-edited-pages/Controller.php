<?php
class Ynwiki_Widget_RecentEditedPagesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
    {
       if($this->_getParam('number') != ''  && $this->_getParam('number') >= 0){
                $limit = $this->_getParam('number');
            }
       else $limit = 5;
       $viewer = Engine_Api::_()->user()->getViewer();
       $table = Engine_Api::_()->getDbtable('pages', 'ynwiki');
       $Name = $table->info('name');
       $select = $table->select()->from($Name)->setIntegrityCheck(false)
       ->join("engine4_ynwiki_edits","engine4_ynwiki_edits.page_id = $Name.page_id","")
       ->where('engine4_ynwiki_edits.user_id = ?',$viewer->getIdentity())
       ->order("engine4_ynwiki_edits.creation_date DESC")
       ->limit($limit); 
      
	  $this->view->comments =  $arr_pages = $table->fetchAll($select);
      if(count($arr_pages) <= 0) 
        $this->setNoRender();
	  $css = "global_form_box";
	  $page_listing_id = 'page_recent_edited';
	  $no_pages_message = $this->view->translate('Nobody has posted a page yet.');
	  $this->view->html_full = $this->view->partial(Ynwiki_Api_Core::partialViewFullPath('_widgets.tpl'), array('arr_pages'=>$arr_pages, 'page_listing_id'=> $page_listing_id, 'no_pages_message'=>$no_pages_message, 'css'=>$css));
  }
}