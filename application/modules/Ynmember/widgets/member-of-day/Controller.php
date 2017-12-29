<?php

class Ynmember_Widget_MemberOfDayController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
            return $this->setNoRender();
        }
        $tableUser = Engine_Api::_()->getItemTable('user');
        $select = $tableUser->select()->where('member_of_day = 1')->limit(1);
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
            $userTableName = $tableUser->info('name');
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
        $user = $tableUser->fetchRow($select);

        if (!$user) {
            return $this->setNoRender();
        }
        // Load fields view helpers
        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->view->user = $user;
    }
}