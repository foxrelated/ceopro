<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Advgroup
 * @author     YouNet Company
 */

class Advgroup_Widget_ProfileWikisController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
  	
     // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->group = $subject = Engine_Api::_()->core()->getSubject('group');
    if($subject->is_subgroup && !$subject->isParentGroupOwner($viewer)){
       $parent_group = $subject->getParentGroup();
        if(!$parent_group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
        else if(!$subject->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
    }
    else if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    //Get number of videos display
    $max = $this->_getParam('itemCountPerPage');
    if(!is_numeric($max) | $max <=0) $max = 10;

    $params = array();
    $params['parent_type'] = $subject->getType();
    $params['parent_id'] = $subject->getIdentity();
    $params['page'] = $this->_getParam('page',1);
    $params['limit'] = $max;

    $this->view->pages = $paginator = Engine_Api::_()->ynwiki ()->getPagesPaginator($params);

    if($paginator->getTotalItemCount() <= 0){
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }
  public function getChildCount()
  {
    return $this->_childCount;
  }
}
?>
