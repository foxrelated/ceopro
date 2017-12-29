<?php

class Ynbusinesspages_Model_DbTable_Reviews extends Engine_Db_Table
{
    protected $_rowClass = 'Ynbusinesspages_Model_Review';

    public function getReviewsPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getTopicsSelect($params));
    }

    public function getTopicsSelect($params = array())
    {
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_review');
        $tableName = $table->info('name');
        $businessTbl = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $businessTblName = $businessTbl->info('name');
        $select = $table
            ->select()
            ->from($tableName);

        $select->joinLeft("$businessTblName as business", "business.business_id = $tableName.business_id", null);
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
            $select->setIntegrityCheck(false);
            $locationTbl = Engine_Api::_()->getDbTable('locations', 'ynbusinesspages');
            $locationTblName = $locationTbl->info('name');
            $select->joinLeft("$locationTblName as location",
                "location.business_id = $tableName.business_id",
                "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( location.latitude ) ) * cos( radians( location.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( location.latitude ) ) ) ) AS distance");
            $select->where('location.main = ?', 1);
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('ynbusinesspages_radius_unit', 0)) {
                $target_distance = $target_distance / 1.609344;
            }
            $select->having("distance <= $target_distance");
        }
        $select->where('business.status = ?', 'published');

        // User
        if (!empty($params['user_id'])) {
            $select
                ->where("$tableName.user_id = ?", $params['user_id']);
        }

        //Business
        if (isset ($params['business_id'])) {
            $select
                ->where("$tableName.business_id = ?", $params['business_id']);
        }

        // Order
        if (isset($params['order'])) {
            switch ($params['order']) {
                case 'modified_date':
                    $select->order('modified_date DESC');
                    break;
                case 'recent':
                default:
                    $select->order('creation_date DESC');
                    break;
            }
        } else {
            $select->order('creation_date DESC');
        }
        return $select;
    }
}
