<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Advgroup
 * @author     YouNet Company
 */

class Advgroup_Widget_GroupsTagsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
    $tag_map_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
    $group_table = Engine_Api::_()->getItemTable('group');
    $tag_name = $tag_table->info('name');
    $tag_map_name = $tag_map_table->info('name');
    $group_name = $group_table->info('name');

    $filter_select = $tag_map_table->select()->from($tag_map_name,"$tag_map_name.*")
                     ->setIntegrityCheck(false)
                     ->joinLeft($group_name,"$group_name.group_id = $tag_map_name.resource_id",'')
                     ->where("$group_name.search = ?","1");

    $select = $tag_table->select()->from($tag_name,array("$tag_name.*","Count($tag_name.tag_id) as count"));
    $select  ->joinLeft($filter_select, "t.tag_id = $tag_name.tag_id",'');
    $select  ->order("$tag_name.text");
    $select  ->group("$tag_name.text");
    $select  ->where("t.resource_type = ?","group");

    if(Engine_Api::_()->core()->hasSubject('user')){
      $user = Engine_Api::_()->core()->getSubject('user');
      $select -> where("t.tagger_id = ?", $user->getIdentity());
    }
    else if( Engine_Api::_()->core()->hasSubject('group') ) {
      $group = Engine_Api::_()->core()->getSubject('group');
      $user = $group->getOwner();
      $select -> where("t.tagger_id = ?", $user->getIdentity());
    }

    $this->view->tags = $tags = $tag_table->fetchAll($select);
    if(count($tags) == 0)
      $this->setNoRender();
  }
}
?>
