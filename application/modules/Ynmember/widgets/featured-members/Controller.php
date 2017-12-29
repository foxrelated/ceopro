<?php

class Ynmember_Widget_FeaturedMembersController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $limit = (int)$this->_getParam('itemCountPerPage', 7);
        $this->view->limit = $limit;
        $userTbl = Engine_Api::_()->getItemTable('user');
        $userTblName = $userTbl->info('name');
        $featureTbl = Engine_Api::_()->getItemTable('ynmember_feature');
        $featureTblName = $featureTbl->info('name');
        $select = $userTbl->select()->setIntegrityCheck(false)
            ->from($userTblName)
            ->joinLeft($featureTblName, "{$userTblName}.`user_id` = {$featureTblName}.`user_id`", array("{$featureTblName}.active"))
            ->where("{$userTblName}.`enabled` = 1")->where("{$userTblName}.`verified` = 1")->where("{$userTblName}.`approved` = 1")
            ->where("{$featureTblName}.`active` = 1")
            ->order(new Zend_Db_Expr(('rand()')))
            ->limit($limit);

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
                $select->where("{$userTblName}.user_id IN (?)", $userIds);
            } else
                $select->where("{$userTblName}.user_id IN ('')");
        }
        $this->view->users = $users = $userTbl->fetchAll($select);

        if (!count($users)) {
            return $this->setNoRender();
        }
    }
}