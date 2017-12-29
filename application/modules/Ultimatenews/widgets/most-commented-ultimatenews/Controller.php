<?php
class Ultimatenews_Widget_MostCommentedUltimatenewsController extends Ultimatenews_Content_Widget_Base
{
  public function indexAction()
  { 
	$table = Engine_Api::_()->getDbtable('Contents','ultimatenews');
    $limit =$this->_getParam('max');
	
	if (!isset($limit) || $limit <=0)
		$limit = 5;
    $selectTop = $table->select('engine4_ultimatenews_contents')->setIntegrityCheck(false)    
          ->joinLeft("engine4_ultimatenews_categories","engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id",array('logo'=>'engine4_ultimatenews_categories.category_logo','logo_icon'=>'engine4_ultimatenews_categories.logo','display_logo'=>'engine4_ultimatenews_categories.display_logo','mini_logo'=>'engine4_ultimatenews_categories.mini_logo'))
          ->joinLeft("engine4_core_comments","engine4_core_comments.resource_id = engine4_ultimatenews_contents.content_id",array('count_comment'=>'Count(resource_id)'))
          ->where('engine4_core_comments.resource_type = ?','ultimatenews_content')
          ->where('engine4_ultimatenews_categories.is_active= ? ',1)
          ->where('engine4_ultimatenews_categories.approved= ? ',1)
          ->group("resource_id")  
          ->order("Count(resource_id) DESC")
		  ->limit($limit);
	
    $commentultimatenews = $table->fetchAll($selectTop);
	
	if( count($commentultimatenews) <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->commentultimatenews = $this->_prepareContent($commentultimatenews);
	    
  }
}