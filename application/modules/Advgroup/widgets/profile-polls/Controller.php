<?php
class Advgroup_Widget_ProfilePollsController extends Engine_Content_Widget_Abstract{

  protected $_childCount;
   
  public function indexAction(){
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->group = $group = Engine_Api::_()->core()->getSubject('group');
	
	$canCreate = $group -> authorization() -> isAllowed(null, 'poll');
	$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'poll');
	if ($canCreate && $levelCreate)
		$this -> view -> canCreate = true;
	else
		$this -> view -> canCreate = false;
	
    if($group->is_subgroup && !$group->isParentGroupOwner($viewer)){
       $parent_group = $group->getParentGroup();
        if(!$parent_group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
        else if(!$group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
    }
    else if( !$group->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    //Poll Select
    $params = array();
    $params['order'] = 'recent';
    $params['browse'] = 1;
    $params['closed'] = 0;
    $params['group_id'] = $group->group_id;
    
    $this->view->paginator = $paginator =  Engine_Api::_()->getItemTable('advgroup_poll')
                                             ->getPollsPaginator($params);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam(1));
      
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
