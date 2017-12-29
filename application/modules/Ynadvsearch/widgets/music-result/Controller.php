<?php

class Ynadvsearch_Widget_MusicResultController extends Engine_Content_Widget_Abstract {

  public function indexAction()
  {
  	$music_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('music');
	if(!$music_enable)
	{
		return $this -> setNoRender();
	}
	
	// Get viewer info
    $this->view->viewer     = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
	
	$values = Zend_Controller_Front::getInstance()->getRequest()->getParams();

    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);
	
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
    $paginator->setCurrentPageNumber(Zend_Controller_Front::getInstance()->getRequest() ->getParam('page', 1));
  }
}
