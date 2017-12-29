<?php

class Advgroup_Widget_FeaturedGroupsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
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
            ->where('featured = 1')
            ->where('is_subgroup = 0')
            ->where('search = 1')
            ->order("rand()")
            ->limit($this->_getParam('max', 9));

        $this->view->groups = $table->fetchAll($select);
        $this->view->limit = $this->_getParam('max', 9);
    }
}

?>