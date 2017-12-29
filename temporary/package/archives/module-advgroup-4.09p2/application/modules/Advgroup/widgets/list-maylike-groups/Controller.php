<?php

class Advgroup_Widget_ListMaylikeGroupsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->group = $subject = Engine_Api::_()->core()->getSubject('group');
        if ($subject->is_subgroup && !$subject->isParentGroupOwner($viewer)) {
            $parent_group = $subject->getParentGroup();
            if (!$parent_group->authorization()->isAllowed($viewer, "view")) {
                return $this->setNoRender();
            } else if (!$subject->authorization()->isAllowed($viewer, "view")) {
                return $this->setNoRender();
            }
        } else if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        $cur_group = Engine_Api::_()->core()->getSubject();
        $count = $this->_getParam('itemCountPerPage', 3);
        $recentType = $this->_getParam('recentType', 'creation');
        if (!in_array($recentType, array('creation', 'modified'))) {
            $recentType = 'creation';
        }
        $this->view->recentType = $recentType;
        $this->view->recentCol = $recentCol = $recentType . '_date';

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
            ->where('category_id = ?', $cur_group->category_id)
            ->where('group_id <>?', $cur_group->getIdentity())
            ->order('rand()')
            ->limit($count);
        if ($recentType == 'creation') {
            $select->order('group_id DESC');
        } else {
            $select->order($recentCol . ' DESC');
        }
        $this->view->groups = $groups = $table->fetchAll($select);
        // Hide if nothing to show
        if (count($groups) <= 0) {
            return $this->setNoRender();
        }

    }
}