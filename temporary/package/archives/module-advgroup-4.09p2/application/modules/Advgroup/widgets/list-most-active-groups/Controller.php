<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    AdvGroup
 * @author     YouNet Company
 */
class Advgroup_Widget_ListMostActiveGroupsController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {
        $count = $this->_getParam('itemCountPerPage', 12);
        $time = $this->_getParam('time', 1);
        if (!in_array($time, array(1, 2, 3))) {
            $time = 1;
        }

        $date = date('Y-m-d H:i:s');
        switch ($time) {
            case 1:
                $newdate = strtotime('-30 day', strtotime($date));
                break;
            case 2:
                $newdate = strtotime('-60 day', strtotime($date));
                break;
            case 3:
                $newdate = strtotime('-90 day', strtotime($date));
        }
        $newdate = date('Y-m-d H:i:s', $newdate);

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
            ->where("$topicName.creation_date > ?", $newdate)
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
