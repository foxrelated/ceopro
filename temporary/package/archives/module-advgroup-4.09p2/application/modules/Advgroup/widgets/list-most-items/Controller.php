<?php

class Advgroup_Widget_ListMostItemsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $headScript = new Zend_View_Helper_HeadScript();
        $headScript->appendFile('application/modules/Advgroup/externals/scripts/AdvgroupTabContent.js');
        $params = $this->_getAllParams();

        $tab_recent = $tab_popular = $tab_active = $tab_directory = $mode_list = $mode_grid = $mode_map = 1;
        $tab_enabled = $mode_enabled = array();
        $view_mode = 'list';

        if (isset($params['tab_popular'])) {
            $tab_popular = $params['tab_popular'];
        }
        if ($tab_popular) {
            $tab_enabled[] = 'popular';
        }
        if (isset($params['tab_recent'])) {
            $tab_recent = $params['tab_recent'];
        }
        if ($tab_recent) {
            $tab_enabled[] = 'recent';
        }
        if (isset($params['tab_active'])) {
            $tab_active = $params['tab_active'];
        }
        if ($tab_active) {
            $tab_enabled[] = 'active';
        }
        if (isset($params['tab_directory'])) {
            $tab_directory = $params['tab_directory'];
        }
        if ($tab_directory) {
            $tab_enabled[] = 'directory';
        }
        if (isset($params['mode_list'])) {
            $mode_list = $params['mode_list'];
        }
        if ($mode_list) {
            $mode_enabled[] = 'list';
        }
        if (isset($params['mode_grid'])) {
            $mode_grid = $params['mode_grid'];
        }
        if ($mode_grid) {
            $mode_enabled[] = 'grid';
        }
        if (isset($params['mode_map'])) {
            $mode_map = $params['mode_map'];
        }
        if ($mode_map) {
            $mode_enabled[] = 'map';
        }
        if (isset($params['view_mode'])) {
            $view_mode = $params['view_mode'];
        }

        if ($mode_enabled && !in_array($view_mode, $mode_enabled)) {
            $view_mode = $mode_enabled[0];
        }

        $this->view->tab_enabled = $tab_enabled;
        $this->view->mode_enabled = $mode_enabled;

        $class_mode = "advgroup_list-view";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "advgroup_grid-view";
                break;
            case 'map':
                $class_mode = "advgroup_map-view";
                break;
            default:
                $class_mode = "advgroup_list-view";
                break;
        }
        $this->view->class_mode = $class_mode;
        $this->view->view_mode = $view_mode;
        if (!$tab_enabled) {
            $this->setNoRender();
        }
        $itemCount = $this->_getParam('itemCountPerPage', 6);

        $table = Engine_Api::_()->getItemTable('group');
        $tableName = $table -> info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('advgroup', 'groups');
        }
        else
        {
            $select = $table->select()->from("$tableName", array("$tableName.*"));
        }
        $select->limit($itemCount);

        // recent groups
        $recentType = $this->_getParam('recentType', 'creation');
        if (!in_array($recentType, array('creation', 'modified'))) {
            $recentType = 'creation';
        }
        $this->view->recentType = $recentType;
        $this->view->recentCol = $recentCol = $recentType . '_date';

        $recentSelect = clone $select;
        $recentSelect -> where('search = ?', 1)
            ->where("is_subgroup = ?", 0);
        if ($recentType == 'creation') {
            $recentSelect->order('group_id DESC');
        } else {
            $recentSelect->order($recentCol . ' DESC');
        }
        $this->view->recentgroups = $table->fetchAll($recentSelect);

        //most popular groups
        $popularSelect = clone $select;
        $popularType = $this->_getParam('popularType', 'member');
        if (!in_array($popularType, array('view', 'member'))) {
            $popularType = 'member';
        }
        $this->view->popularType = $popularType;
        $this->view->popularCol = $popularCol = $popularType . '_count';

        $popularSelect
            ->where('search = ?', 1)
            ->where("is_subgroup = ?", 0)
            ->order($popularCol . ' DESC');
        $this->view->populargroups = $table->fetchAll($popularSelect);

        //most active group
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

        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $activeSelect = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('advgroup', 'groups', array("COUNT('topic_id') AS topic_count"));
        }
        else
        {
            $activeSelect = $table->select()->from("$tableName", array("$tableName.*", "COUNT('topic_id') AS topic_count"));
        }
        $activeSelect->limit($itemCount);

        $activeSelect
            ->setIntegrityCheck(false)
            ->joinRight($topicName, "$topicName.group_id = $tableName.group_id", "$topicName.topic_id")
            ->where("$tableName.search = ?", 1)
            ->where("$tableName.is_subgroup = ?", 0)
            ->where("$topicName.creation_date > ?", $newdate)
            ->group("$tableName.group_id")
            ->order("COUNT('topic_id') DESC");
        $this->view->activegroups = $table->fetchAll($activeSelect);

        //group directory
        $request = Zend_Controller_Front::getInstance()->getRequest();
        // clear title of widget
        if ($request->isPost()) {
            $this->getElement()->setTitle('');
        }
        $directorySelect = clone $select;
        $directorySelect
            ->where('search = ?', 1)
            ->where('is_subgroup = ?', 0)
            ->order('title ASC');
        $this->view->directory = $table->fetchAll($directorySelect);
    }

}
