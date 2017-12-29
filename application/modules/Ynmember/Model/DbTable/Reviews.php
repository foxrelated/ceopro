<?php

class Ynmember_Model_DbTable_Reviews extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmember_Model_Review';
    protected $_name = 'ynmember_reviews';

    public function getAllReviewsByResourceId($resource_id)
    {
        $select = $this->select();
        $select->where("resource_id = ?", $resource_id);
        return $this->fetchAll($select);
    }

    public function getAllReviewsByUserId($user_id)
    {
        $select = $this->select();
        $select->where("user_id = ?", $user_id);
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
                $select->where("resource_id IN (?)", $userIds);
            } else
                $select->where("resource_id IN ('')");
        }
        return $this->fetchAll($select);
    }

    public function checkHasReviewed($resource_id, $user_id)
    {
        $select = $this->select();
        $select
            ->where('resource_id = ?', $resource_id)
            ->where('user_id = ?', $user_id)
            ->limit(1);
        $row = $this->fetchRow($select);
        if ($row) {
            return true;
        }
        return false;
    }

    public function getReviewSelect($params)
    {
        $reviewTbl = Engine_Api::_()->getItemTable("ynmember_review");
        $reviewTblName = $reviewTbl->info('name');

        $ratingTbl = Engine_Api::_()->getItemTable("ynmember_rating");
        $ratingTblName = $ratingTbl->info('name');

        $select = $reviewTbl->select()
            ->setIntegrityCheck(false)
            ->from($reviewTblName)
            ->joinLeft($ratingTblName, "{$reviewTblName}.`review_id` = {$ratingTblName}.`review_id` AND {$ratingTblName}.`rating_type` = '0'");

        if(!isset($params['isAdmin'])) {
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
                    $select->where("{$reviewTblName}.resource_id IN (?)", $userIds);
                } else
                    $select->where("{$reviewTblName}.resource_id IN ('')");
            }
        }

        if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where("{$reviewTblName}.user_id = ?", $params['user_id']);
        }
        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where("{$reviewTblName}.user_id = ?", $params['user']->getIdentity());
        }
        if (!empty($params['users'])) {
            $str = (string)(is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users']);
            $select->where("{$reviewTblName}.user_id in (?)", new Zend_Db_Expr($str));
        }
        //reviewer
        if (!empty($params['resource_id'])) {
            $select->where("{$reviewTblName}.resource_id in (?)", $params['resource_id']);
        }
        //review for
        if (!empty($params['user_id'])) {
            $select->where("{$reviewTblName}.user_id in (?)", $params['user_id']);
        }
        //title
        if (!empty($params['title'])) {
            $select->where("{$reviewTblName}.title LIKE ?", '%' . $params['title'] . '%');
        }
        //from date
        if (!empty($params['from_date'])) {
            $select->where("{$reviewTblName}.creation_date >= ?", date('Y-m-d H:i:s', strtotime($params['from_date'])));
        }
        //to date
        if (!empty($params['to_date'])) {
            $select->where("{$reviewTblName}.creation_date <= ?", date('Y-m-d H:i:s', strtotime($params['to_date'])));
        }
        // Could we use the search indexer for this?
        if (!empty($params['keyword'])) {
            $select->where("{$reviewTblName}.title LIKE ? OR {$reviewTblName}.summary LIKE ?", '%' . $params['keyword'] . '%');
        }
        // rating filter
        if (!empty($params['filter_rating']) && $params['filter_rating'] != '-1') {
            $select->where("{$ratingTblName}.rating = ?", $params['filter_rating']);
        }
        // ordering

        if (!empty($params['orderby'])) {
            switch ($params['orderby']) {
                case 'creation_date':
                    $select->order("{$reviewTblName}.creation_date DESC");
                    break;
                case 'most_rating':
                    $select->order("{$ratingTblName}.rating DESC");
                    break;
                case 'least_rating':
                    $select->order("{$ratingTblName}.rating ASC");
                    break;
                case 'view_count':
                    $select->order("{$reviewTblName}.view_count DESC");
                    break;
                case 'comment_count':
                    $select->order("{$reviewTblName}.comment_count DESC");
                    break;
                case 'helpful':
                    $select->order("{$reviewTblName}.helpful_count DESC");
                    break;
                default:
                    $select->order("{$reviewTblName}.creation_date DESC");
                    break;
            }
        } else {
            $select->order("{$reviewTblName}.creation_date DESC");
        }
        return $select;
    }

    public function getReviewPaginator($params)
    {
        $paginator = Zend_Paginator::factory($this->getReviewSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        if (empty($params['limit'])) {
            $page = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmember.page', 10);
            $paginator->setItemCountPerPage($page);
        }
        return $paginator;
    }

    public function countReviewByUser($user)
    {
        if (is_null($user)) {
            return 0;
        }
        $reviews = $this->fetchAll($this->select()->where("resource_id = ? ", $user->getIdentity()));
        return count($reviews);
    }
}
