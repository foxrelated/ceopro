<?php
class Advgroup_Widget_ProfileTopDiscussionsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('group');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Get paginator
    $table = Engine_Api::_()->getItemTable('advgroup_topic');
    $select = $table->select()
      ->where('group_id = ?', Engine_Api::_()->core()->getSubject()->getIdentity())
      ->order('post_count DESC');
      ;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show and not viewer
    if( $paginator->getTotalItemCount() <= 0 && !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }

    $this->view->canPost = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment');

    // Add count to title if configured
//    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
//      $this->_childCount = $paginator->getTotalItemCount();
//    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}