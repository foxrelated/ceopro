<?php

class Advgroup_Widget_ListPopularGroupsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $count = $this->_getParam('itemCountPerPage', 12);
        $popularType = $this->_getParam('popularType', 'member');
        if (!in_array($popularType, array('view', 'member'))) {
            $popularType = 'member';
        }
        $this->view->popularType = $popularType;
        $this->view->popularCol = $popularCol = $popularType . '_count';

        $table = Engine_Api::_()->getItemTable('group');
        $tableName = $table -> info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('advgroup', 'groups');
        }
        else
        {
            $select = $table->select()->from("$tableName", array("$tableName.*"));
        }
        $select
            ->where('search = ?', 1)
            ->where("is_subgroup = ?", 0)
            ->order($popularCol . ' DESC')
            ->limit($count);
        $this->view->groups = $groups = $table->fetchAll($select);
        $this->view->limit = $count;
        // Hide if nothing to show
        if (count($groups) <= 0) {
            return $this->setNoRender();
        }
    }
}