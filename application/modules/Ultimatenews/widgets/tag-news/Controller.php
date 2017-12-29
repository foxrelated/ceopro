<?php
class Ultimatenews_Widget_TagNewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
        $t_table = Engine_Api::_()->getDbtable('tags', 'core');
        $tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $c_table = Engine_Api::_()->getItemTable('ultimatenews_content');
        $tName = $t_table->info('name');
        $tmName = $tm_table->info('name');
        $cName = $c_table->info('name');

        $filter_select = $tm_table->select()->from($tmName,"$tmName.*")
                         ->setIntegrityCheck(false)
                         ->joinLeft($cName, "$cName.content_id = $tmName.resource_id",'');
        
        $select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
        $select->joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
         $select  ->order("count DESC");
        $select  ->group("$tName.text");
        $select  ->where("t.resource_type = ?","ultimatenews_content");
        $this->view->tags = $tags = $t_table->fetchAll($select);
        if(count($tags) <= 0)
        {
            $this->setNoRender();
        }
  }
}
