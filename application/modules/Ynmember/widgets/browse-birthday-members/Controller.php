<?php

class Ynmember_Widget_BrowseBirthdayMembersController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
        //Setup params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $page = $request->getParam('page', 1);
        if (isset($params['year']) &&
            isset($params['month']) &&
            isset($params['date'])
        ) {
            $pickedDay = $params['date'];
            $pickedMonth = $params['month'];
            $pickedYear = $params['year'];
            $pickedDate = $params['year'] . "-" . $params['month'] . "-" . $params['date'];
        } else {
            $pickedDay = date('d');
            $pickedMonth = date('m');
            $pickedYear = date('Y');
            $pickedDate = date('Y-m-d');
        }

        $table = Engine_Api::_()->getItemTable('user');
        $userTableName = $table->info('name');

        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $searchTableName = $searchTable->info('name');

        // Contruct query
        $select = $table->select()
            ->from($userTableName)
            ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
            ->where("{$userTableName}.search = ?", 1)
            ->where("{$userTableName}.enabled = ?", 1);

        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $cookies = Engine_Api::_()->ynlocationbased()->mergeWithCookie('ynmember');
        }
        $target_distance = $base_lat = $base_lng = "";
        if (isset($cookies['lat']) && $cookies['lat'])
            $base_lat = $cookies['lat'];
        if (isset($cookies['long']) && $cookies['long'])
            $base_lng = $cookies['long'];
        if (isset($cookies['within']))
            $target_distance = $cookies['within'];

        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $workPlaceTbl = Engine_Api::_()->getItemTable('ynmember_workplace');
            $userIds1 = $workPlaceTbl->getUserIdByLocation($base_lat, $base_lng, $target_distance);
            $livePlaceTbl = Engine_Api::_()->getItemTable('ynmember_liveplace');
            $userIds2 = $livePlaceTbl->getUserIdByLocation($base_lat, $base_lng, $target_distance);
            $userIds = array_unique(array_merge($userIds1, $userIds2));
            if (count($userIds)) {
                $select->where("{$userTableName}.user_id IN (?)", $userIds);
            } else
                $select->where("{$userTableName}.user_id IN ('')");
        }

        $select->where("MONTH(`{$searchTableName}`.birthdate) = ? ", $pickedMonth);
        $select->where("DAY(`{$searchTableName}`.birthdate) = ? ", $pickedDay);
        $users = $table->fetchAll($select);
        $members = array();
        if (count($users)) {
            foreach ($users as $user) {
                if (!Engine_Api::_()->ynmember()->canFilterByBirthday($user)) {
                    continue;
                } else {
                    $members[] = $user;
                }
            }
        }

        //Set curent page
        $paginator = Zend_Paginator::factory($members);
        $limit = $this->_getParam('itemCountPerPage', 15);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        // Load fields view helpers
        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->view->paginator = $paginator;
        $this->view->pickedDate = $pickedDate;
        $this->view->pickedDay = $pickedDay;
        $this->view->pickedMonth = $pickedMonth;
        $this->view->pickedYear = $pickedYear;

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        unset($params['rewrite']);

        $this->view->formValues = array_filter($params);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    }
}