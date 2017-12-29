<?php

class Advgroup_Widget_ListActiveGroupsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $count = $this->_getParam('itemCountPerPage', 12);
        $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
        $topicName = $topicTable->info('name');
        $table = Engine_Api::_()->getItemTable('group');
        $tableName = $table->info('name');

        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $activeSelect = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('advgroup', 'groups', array("COUNT('topic_id') AS topic_count"));
        }
        else
        {
            $activeSelect = $table->select()->from("$tableName", array("$tableName.*", "COUNT('topic_id') AS topic_count"));
        }
        $activeSelect
            ->setIntegrityCheck(false)
            ->joinRight($topicName, "$topicName.group_id = $tableName.group_id", "$topicName.topic_id")
            ->where("$tableName.search = ?", 1)
            ->where("$tableName.is_subgroup = ?", 0)
            ->group("$tableName.group_id")
            ->order("COUNT('topic_id') DESC")
            ->limit($count);
        $this->view->groups = $groups = $table->fetchAll($activeSelect);
        $this->view->limit = $count;
        if (count($groups) <= 0) {
            return $this->setNoRender();
        }
    }
}

?>
