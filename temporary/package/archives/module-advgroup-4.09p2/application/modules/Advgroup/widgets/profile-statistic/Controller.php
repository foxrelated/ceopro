<?php
class Advgroup_Widget_ProfileStatisticController extends Engine_Content_Widget_Abstract{
 
  public function indexAction(){
   // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('group');
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

    // Get staff
    $ids = array();
    $ids[] = $subject->getOwner()->getIdentity();
    $list = $subject->getOfficerList();
    foreach( $list->getAll() as $listiteminfo )
    {
      $ids[] = $listiteminfo->child_id;
    }

    $staff = array();
    foreach( $ids as $id )
    {
      $user = Engine_Api::_()->getItem('user', $id);
      $staff[] = array(
        'membership' => $subject->membership()->getMemberInfo($user),
        'user' => $user,
      );
    }
    $this->view->group = $subject;
    $this->view->staff = $staff;

    //Get more Statistic
    $groupTable = Engine_Api::_()->getItemTable('group');

    $albumTable = Engine_Api::_()->getItemTable('advgroup_album');
    $select = new Zend_Db_Select($albumTable->getAdapter());
    $select -> from($albumTable->info('name'), 'COUNT(*) AS count')
            -> where('album_id > 0')
            -> where('group_id = ?',$subject->getIdentity());
    $this->view->count_albums =  $select->query()->fetchColumn(0);

    $photoTable = Engine_Api::_()->getItemTable('advgroup_photo');
    $select = new Zend_Db_Select($photoTable->getAdapter());
    $select -> from($photoTable->info('name'), 'COUNT(*) AS count')
            -> where('photo_id > 0')
            -> where('group_id = ?',$subject->getIdentity());
    $this->view->count_photos =  $select->query()->fetchColumn(0);

    $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
    $select = new Zend_Db_Select($topicTable->getAdapter());
    $select -> from($topicTable->info('name'), 'COUNT(*) AS count')
            -> where('topic_id > 0')
            -> where('group_id = ?',$subject->getIdentity());
    $this->view->count_topics =  $select->query()->fetchColumn(0);
  }
}
?>
