<?php
class Ynblog_Widget_BlogsMenuController extends Engine_Content_Widget_Abstract
{
  protected $_navigation;
  public function indexAction()
  {
    //Get main navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynblog_main');
    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynblog_quick');
  }
}