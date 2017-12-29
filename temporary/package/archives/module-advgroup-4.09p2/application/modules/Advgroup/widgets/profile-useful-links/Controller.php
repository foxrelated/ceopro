<?php

class Advgroup_Widget_ProfileUsefulLinksController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  public function indexAction(){
    //Get viewer
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if($viewer->getIdentity()==0) {
       return $this->setNoRender();
    }
    //Check subject
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

    
    if(!$subject->membership()->isMember($viewer)){
      return $this->setNoRender();
    }

    //Get Link Paginator
    $table = Engine_Api::_()->getItemTable('advgroup_link');
    $select = $table->select()
      ->where('group_id = ?', Engine_Api::_()->core()->getSubject()->getIdentity())
      ->order('creation_date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // check add new helpful link
    $this->view->helpfulLink = true;
    if(!$viewer -> getIdentity()){
      $this->view->helpfulLink = false;
    }
	$subject = Engine_Api::_() -> core() -> getSubject();
	if ($subject -> getType() !== 'group') {
		$this->view->helpfulLink = false;
	}

	if ($subject -> is_subgroup) {
		if (!$subject->isOwner($viewer) && !$subject -> isParentGroupOwner($viewer)) {
			$this->view->helpfulLink = false;
		}
	}
	else
	if (!$subject->isOwner($viewer)) {
		$this->view->helpfulLink = false;
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