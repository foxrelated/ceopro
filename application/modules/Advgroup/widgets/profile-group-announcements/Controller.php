<?php
class Advgroup_Widget_ProfileGroupAnnouncementsController extends Engine_Content_Widget_Abstract
{
  public function indexAction(){
     // Get paginator
        // Don't render this if not authorized
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    $this->view->group = $group = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
	$this->view->user_id = $user_id = $viewer->getIdentity();
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
	//get marked announcements of current user
    $tableMark = Engine_Api::_()->getDbtable('marks', 'advgroup');
	$ids =  array();
	$select = $tableMark->select()
					->where('user_id  = ?', $user_id);
			
		foreach( $tableMark->fetchAll($select) as $row )
			{
					  	
				$ids[] = $row->announcement_id;
			}
	
	
    $table = Engine_Api::_()->getDbtable('announcements', 'advgroup');
    $select = $table->select()
      ->where('group_id = ?',$group->group_id)
      ->order('modified_date DESC');
	 if(!empty($ids))
	 {
	 	$select->where('announcement_id NOT IN (?)',$ids);
	 }
          
    ;
	
    $announcements = $table->fetchAll($select);
		
    // Hide if nothing to show
    if( !$announcements ) {
      
    }

    $this->view->announcements = $announcements;
	
    if(count($announcements)<=0 && !$group -> isOwner($viewer)) {
        return $this->setNoRender();
    }
	$menu = new Advgroup_Plugin_Menus();
	
	$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
	  	$canManage = $subject -> authorization() -> isAllowed(null, 'announcement');
		
		$allow_manage = "";
		$levelManage = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'announcement');
		if ($canManage && $levelManage) {
			$allow_manage = true;
		} else {
			$allow_manage = false;
		}
	$this->view->allow_manage = $allow_manage;
	
    $aManageAnnouncementButton = $menu->onMenuInitialize_AdvgroupManageAnnouncement();
    $this->view->aManageAnnouncementButton = $aManageAnnouncementButton;
  }
}
?>