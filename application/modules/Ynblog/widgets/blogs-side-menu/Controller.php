<?php
class Ynblog_Widget_BlogsSideMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Only blog or user as subject
    if( Engine_Api::_()->core()->hasSubject('blog') ) {
      $this->view->blog = $blog = Engine_Api::_()->core()->getSubject('blog');
      $this->view->owner = $owner = $blog->getOwner();
    } else if( Engine_Api::_()->core()->hasSubject('user') ) {
      $this->view->blog = null;
      $this->view->owner = $owner = Engine_Api::_()->core()->getSubject('user');
    } else {
      return $this->setNoRender();
    }

    //Get viewer
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    // Get navigation
    $this->view->gutterNavigation = $gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynblog_gutter');
  }
}
