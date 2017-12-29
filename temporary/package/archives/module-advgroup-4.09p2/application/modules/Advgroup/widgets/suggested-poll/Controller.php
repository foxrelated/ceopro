<?php
class Advgroup_Widget_SuggestedPollController extends Engine_Content_Widget_Abstract{
  public function indexAction(){
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->group = $group = Engine_Api::_()->core()->getSubject('group');
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
    
    $this->view->group = $group = Engine_Api::_()->core()->getSubject('group');
    if( !$group->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    //Poll Select
    $table =  Engine_Api::_()->getItemTable('advgroup_poll');
    $select = $table->select()->where('closed = 0')->where('group_id = ?',$group->group_id)
              ->order("RAND()")
              ->limit(1);
    $this->view->poll = $poll =  $table->fetchRow($select);
    if(!$poll) return $this->setNoRender();
    }
}
?>
