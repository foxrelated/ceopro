<?php

class Advgroup_Widget_SubGroupsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->core()->hasSubject('group')) {
            return $this->setNoRender();
        }
        $group = Engine_Api::_()->core()->getSubject('group');

        if (!$group->authorization()->isAllowed($viewer, "view")) {
            return $this->setNoRender();
        }

        $table = Engine_Api::_()->getItemTable('group');
        $tableName = $table -> info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('advgroup', 'groups');
        }
        else
        {
            $select = $table->select()->from("$tableName", array("$tableName.*"));
        }

        if ($group->is_subgroup) {
            $this->view->sub_mode = false;
            $select ->where('group_id = ?', $group->parent_id);
        } else {
            $this->view->sub_mode = true;
            $select ->where('parent_id = ?', $group->group_id);
        }
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 1));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        if (count($paginator) <= 0) {
            $this->setNoRender();
        } else {
            $this->view->sub_groups = $paginator;
        }
    }

}
