<?php
class Advgroup_Widget_ProfileInfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $group = Engine_Api::_()->core()->getSubject('group');
    $viewer = Engine_Api::_()->user()->getViewer();
      if($group->is_subgroup && !$group->isParentGroupOwner($viewer)){
        $parent_group = $group->getParentGroup();
        if(!$parent_group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
        else if(!$group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
      }
      else if(!$group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
      }

    // Get staff
    $ids = array();
    $ids[] = $group->getOwner()->getIdentity();
    $list = $group->getOfficerList();
    foreach( $list->getAll() as $listiteminfo )
    {
      $ids[] = $listiteminfo->child_id;
    }

    $staff = array();
    foreach( $ids as $id )
    {
      $user = Engine_Api::_()->getItem('user', $id);
      $staff[] = array(
        'membership' => $group->membership()->getMemberInfo($user),
        'user' => $user,
      );
    }

    $this->view->group = $group;
    $this->view->staff = $staff;

    // Load fields view helpers
      $view = $this->view;
      $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($group);
  }
}