<?php
class Ultimatenews_Widget_FeaturedUltimatenewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    
    $table = Engine_Api::_()->getDbtable('contents','ultimatenews');
    $limit =$this->_getParam('max',10);
   
    if (!isset($limit) || $limit <=0)
        $limit = 10;
    $selectFeatured = $table->select('engine4_ultimatenews_contents')->setIntegrityCheck(false)    
          ->joinLeft("engine4_ultimatenews_categories","engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id",array('logo'=>'engine4_ultimatenews_categories.category_logo','logo_icon'=>'engine4_ultimatenews_categories.logo','display_logo'=>'engine4_ultimatenews_categories.display_logo','mini_logo'=>'engine4_ultimatenews_categories.mini_logo'))
          ->where('engine4_ultimatenews_categories.is_active= ? ',1)
          ->where('engine4_ultimatenews_categories.approved= ? ',1)
         ->where('engine4_ultimatenews_contents.is_featured = ? ',1)
         ->order('engine4_ultimatenews_contents.pubDate DESC')
        ->limit($limit);
	
    $featuredultimatenews = $table->fetchAll($selectFeatured);
    
    if( count($featuredultimatenews) <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->featuredultimatenews = $featuredultimatenews;
    $this->view->totalItem = count($featuredultimatenews);
        
  }
}
