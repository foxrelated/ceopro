<?php

class Ynmultilisting_Model_DbTable_Mappings extends Engine_Db_Table
{
    protected $_name = 'ynmultilisting_mappings';

    public function checkHasItem($listing_id, $item_id, $type)
    {
        $select = $this->select()
            ->where('listing_id = ?', $listing_id)
            ->where('item_id = ?', $item_id)
            ->where('type = ?', $type);
        $row = $this->fetchRow($select);
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function getVideoCount($listingTypeId = null)
    {
        // get enabled video type
        $video_option = Engine_Api::_()->ynmultilisting()->getMultilistingVideoType();
        $select = $this->select()->where("type = '$video_option' OR type ='profile_$video_option'");
        if ($listingTypeId) {
            $tableListing = Engine_Api::_()->getItemTable('ynmultilisting_listing');
            $listings = $tableListing->getListingTypeListings($listingTypeId);
            $arrIDs = array();
            foreach ($listings as $listing) {
                $arrIDs[] = $listing->getIdentity();
            }
            if (count($arrIDs)) {
                $select->where('listing_id IN (?)', $arrIDs);
            } else {
                $select->where("1 = 0");
            }
        }
        return count($this->fetchAll($select));
    }

    public function getVideosPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getVideosSelect($params));
    }

    public function getUltimatevideoPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getUltimatevideo($params));
    }

    public function getWidgetVideosPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getWidgetVideosSelect($params));
    }

    public function getWidgetUltimateVideosPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getWidgetUltimateVideosSelect($params));
    }

    public function deleteItem($params = array())
    {

        $tableName = $this->info('name');
        $db = $this->getAdapter();
        $db->beginTransaction();
        try {
            $db->delete($tableName, array(
                'type = ?' => $params['type'],
                'item_id = ?' => $params['item_id']
            ));
            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            return $e;
        }
        return "true";
    }

    public function getVideosSelect($params = array())
    {

        //Get album table
        $tableName = $this->info('name');

        $table_video = Engine_Api::_()->getItemTable('video');
        $tableName_video = $table_video->info('name');
        $select = $table_video->select()->from(array('p' => $tableName_video));

        $select->setIntegrityCheck(false)
            ->join("$tableName as m", "p.video_id = m.item_id", '');
        $select->where("m.type = 'profile_video'");
        if (!empty($params['listing_id'])) {
            $select->where('m.listing_id=?', $params['listing_id']);
        }
        if (!empty($params['type'])) {
            $select->where("m.type = ?", $params['type']);
        }
        return $select;
    }

    public function getUltimatevideo($params = array())
    {

        //Get album table
        $tableName = $this->info('name');

        $table_video = Engine_Api::_()->getItemTable('ynultimatevideo_video');
        $tableName_video = $table_video->info('name');
        $select = $table_video->select()->from(array('p' => $tableName_video));

        $select->setIntegrityCheck(false)
            ->join("$tableName as m", "p.video_id = m.item_id", '');
        $select->where("m.type = 'profile_ynultimatevideo_video'");
        if (!empty($params['listing_id'])) {
            $select->where('m.listing_id=?', $params['listing_id']);
        }
        if (!empty($params['type'])) {
            $select->where("m.type = ?", $params['type']);
        }
        return $select;
    }


    public function getWidgetVideosSelect($params = array())
    {

        //Get album table
        $tableName = $this->info('name');

        $table_video = Engine_Api::_()->getItemTable('video');
        $rName = $table_video->info('name');
        $select = $table_video->select()->from(array('p' => $rName));

        $select->setIntegrityCheck(false)
            ->join("$tableName as m", "p.video_id = m.item_id", '');
//    $select->where("m.type LIKE '%video%'");
        $select->where("m.type = 'video'");
        if (!empty($params['listing_id'])) {
            $select->where('m.listing_id=?', $params['listing_id']);
        }
        if (!empty($params['title'])) {
            $select->where('p.title LIKE ?', "%" . $params['title'] . "%");
        }
        if (!empty($params['owner'])) {
            $tableUser = Engine_Api::_()->getItemTable('user');
            $tableUserName = $tableUser->info('name');
            $select1 = $tableUser->select()
                ->from(array('p' => $tableUserName),
                    array('user_id'))
                ->where("`displayname` LIKE ?", "%" . $params['owner'] . "%");
            $list_user = $tableUser->fetchAll($select1);
            $list_user = $list_user->toArray();
            $select->where('m.user_id IN (?)', $list_user[0]);
        }
        if (!empty($params['orderby'])) {
            if (isset($params['order'])) {
                $order = $params['order'];
            } else {
                $order = '';
            }
            switch ($params['orderby']) {
                case 'most_liked' :
                    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
                    $likeTableName = $likeTable->info('name');
                    $likeVideoTableSelect = $likeTable->select()->where('resource_type = ?', 'video');
                    $select->joinLeft($likeVideoTableSelect, "t.resource_id = p.video_id");
                    $select->group("p.video_id");
                    $select->order("count(t.like_id) DESC");
                    break;
                case 'most_commented' :
                    $commentTable = Engine_Api::_()->getDbTable('comments', 'core');
                    $commentTableName = $commentTable->info('name');
                    $commentVideoTableSelect = $commentTable->select()->where('resource_type = ?', 'video');
                    $select->join($commentVideoTableSelect, "t.resource_id = p.video_id");
                    $select->group("p.video_id");
                    $select->order("count(t.comment_id) DESC");
                    break;
                default :
                    $select->order("p.{$params['orderby']} DESC");
            }
        }
        $select->order("p.creation_date DESC");
        return $select;
    }

    public function getWidgetUltimateVideosSelect($params = array())
    {

        //Get album table
        $tableName = $this->info('name');

        $table_video = Engine_Api::_()->getItemTable('ynultimatevideo_video');
        $rName = $table_video->info('name');
        $select = $table_video->select()->from(array('p' => $rName));

        $select->setIntegrityCheck(false)
            ->join("$tableName as m", "p.video_id = m.item_id", '');
//    $select->where("m.type LIKE '%video%'");
        $select->where("m.type = 'ynultimatevideo_video'");
        if (!empty($params['listing_id'])) {
            $select->where('m.listing_id=?', $params['listing_id']);
        }
        if (!empty($params['title'])) {
            $select->where('p.title LIKE ?', "%" . $params['title'] . "%");
        }
        if (!empty($params['owner'])) {
            $tableUser = Engine_Api::_()->getItemTable('user');
            $tableUserName = $tableUser->info('name');
            $select1 = $tableUser->select()
                ->from(array('p' => $tableUserName),
                    array('user_id'))
                ->where("`displayname` LIKE ?", "%" . $params['owner'] . "%");
            $list_user = $tableUser->fetchAll($select1);
            $list_user = $list_user->toArray();
            $select->where('m.user_id IN (?)', $list_user[0]);
        }
        if (!empty($params['orderby'])) {
            if (isset($params['order'])) {
                $order = $params['order'];
            } else {
                $order = '';
            }
            switch ($params['orderby']) {
                case 'most_liked' :
                    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
                    $likeTableName = $likeTable->info('name');
                    $likeVideoTableSelect = $likeTable->select()->where('resource_type = ?', 'video');
                    $select->joinLeft($likeVideoTableSelect, "t.resource_id = p.video_id");
                    $select->group("p.video_id");
                    $select->order("count(t.like_id) DESC");
                    break;
                case 'most_commented' :
                    $commentTable = Engine_Api::_()->getDbTable('comments', 'core');
                    $commentTableName = $commentTable->info('name');
                    $commentVideoTableSelect = $commentTable->select()->where('resource_type = ?', 'video');
                    $select->join($commentVideoTableSelect, "t.resource_id = p.video_id");
                    $select->group("p.video_id");
                    $select->order("count(t.comment_id) DESC");
                    break;
                default :
                    $select->order("p.{$params['orderby']} DESC");
            }
        }
        $select->order("p.creation_date DESC");
        return $select;
    }
}