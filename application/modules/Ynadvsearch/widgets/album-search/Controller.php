<?php
class Ynadvsearch_Widget_AlbumSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $advalbum_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('advalbum');
    if($advalbum_enable) {
        return $this -> setNoRender();
    }
    
    $searchForm = $this->view->searchForm = new Ynadvsearch_Form_AlbumSearch();
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $searchForm
      ->setMethod('get')
      ->populate($request->getParams())
      ;
    
  }
}