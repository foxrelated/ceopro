<?php
class Ynblog_BlogController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // only show to member_level if authorized
    if( !$this->_helper->requireAuth()->setAuthParams('blog', $viewer, 'view')->isValid() ) {
      return;
    }

    // Get subject
    if( ($blog_id = $this->_getParam('blog_id',  $this->_getParam('id'))) &&
        ($blog = Engine_Api::_()->getItem('blog')) instanceof Ynblog_Model_Blog ) {
      Engine_Api::_()->core()->setSubject($blog);
    } else {
      $blog = null;
    }

    // Must have a subject
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }

    // Must be allowed to view this blog
    if( !$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'view')->isValid() ) {
      return;
    }
  }
}