<?php

class Ynmember_Widget_MostReviewedMembersController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $tableReview = Engine_Api::_()->getItemTable('ynmember_review');
        $tableReviewName = $tableReview->info('name');
        $select = $tableReview->select()
            ->from(array('r' => $tableReviewName),
                array('resource_id', 'count' => 'count(`resource_id`)'));
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
                $select->where("r.resource_id IN (?)", $userIds);
            } else
                $select->where("r.resource_id IN ('')");
        }
        $select = $select->group("r.resource_id");
        $select->order("count DESC")->limit($this -> _getParam('itemCountPerPage', 3));
        $this->view->list_show_users = $list_show_users = $tableReview->fetchAll($select);
        if (!count($list_show_users)) {
            return $this->setNoRender();
        }
    }
}