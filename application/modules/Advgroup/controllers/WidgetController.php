<?php
class Advgroup_WidgetController extends Core_Controller_Action_Standard
{
  public function requestGroupAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
  }
  
}