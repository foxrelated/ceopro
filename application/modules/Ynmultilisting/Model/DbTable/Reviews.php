<?php
class Ynmultilisting_Model_DbTable_Reviews extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmultilisting_Model_Review';

    public function checkHasReviewed($listing_id, $user_id, $getRow = null)
    {
        if(empty($getRow))
        {
            $getRow = false;
        }
        $select = $this -> select();
        $select
            -> where('listing_id = ?', $listing_id)
            -> where('user_id = ?', $user_id)
            -> limit(1);
        $row =  $this -> fetchRow($select);
        if($getRow)
        {
            return $row;
        }
        if($row)
        {
            return true;
        }
        return false;
    }

    public function getRateListing($listing_id)
    {
        $select = $this-> select() -> where('listing_id = ?', $listing_id);
        $rows = $this -> fetchAll($select);
        $count = 0;
        $total = 0;
        foreach($rows as $row)
        {
            $count++;
            $total += $row -> overal_rating;
        }
        $rate = round(($total/$count), 1);
        return $rate;
    }

    public function getReviewsPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getReviewsSelect($params));
        if( !empty($params['page']) )
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
            $paginator->setItemCountPerPage($params['limit']);
        }
        if( empty($params['limit']) )
        {
            $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmultilisting.page', 10);
            $paginator->setItemCountPerPage($page);
        }
        return $paginator;
    }

    public function getReviewsSelect($params = array())
    {
        $reviewTbl = $this;
        $reviewTblName = $reviewTbl->info('name');

        $listingTbl = Engine_Api::_() -> getItemTable('ynmultilisting_listing');
        $listingTblName = $listingTbl -> info('name');

        $listingTypeTbl = Engine_Api::_() -> getItemtable('ynmultilisting_listingtype');
        $listingTypeTblName = $listingTypeTbl -> info('name');

        $select = $reviewTbl -> select();
        $select -> setIntegrityCheck(false);
        $select -> from("{$reviewTblName} as review", "review.*");

        $based_locations = array();
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $based_locations = Engine_Api::_()->ynlocationbased()->mergeWithCookie('ynmultilisting');
        }

        if (!empty($based_locations['lat'])
            && !empty($based_locations['long'])
            && !empty($based_locations['within'])
            && is_numeric($based_locations['within'])
        ) {
            $base_lat = $based_locations['lat'];
            $base_lng = $based_locations['long'];
            $target_distance = $based_locations['within'];
            if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi') == 'km')
                $target_distance *= Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.mi.to.km', 0.621371);
            $select -> joinLeft("{$listingTblName} as listing","review.listing_id = listing.listing_id", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( listing.latitude ) ) * cos( radians( listing.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( listing.latitude ) ) ) ) AS distance");
            $select->where("listing.latitude <> ''");
            $select->where("listing.longitude <> ''");
            $select->having("distance <= $target_distance");
            $select->order("distance ASC");
        }
        else
        {
            $select -> joinLeft("{$listingTblName} as listing","review.listing_id = listing.listing_id", "");
        }
        $select -> joinLeft("{$listingTypeTblName} as listingtype", "listingtype.listingtype_id = listing.listingtype_id", "");

        //Listing
        if(isset ($params['listingtype_id']) && $params['listingtype_id'] != 'all'){
            $select
                ->where("listing.listingtype_id = ?", $params['listingtype_id']);
        }

        //editor or member
        if(!empty($params['type'])){
            $tableEditors = Engine_Api::_() -> getDbTable('editors', 'ynmultilisting');
            $editorlist = $tableEditors -> getAllEditors();
            switch ($params['type']) {
                case 'member':
                    if($editorlist)
                    {
                        $select->where("review.user_id NOT IN (?)", $editorlist);
                    }
                    break;
                case 'editor':
                    if($editorlist)
                    {
                        $select->where("review.user_id IN (?)", $editorlist);
                    }
                    else
                    {
                        $select->where("1 = 0");
                    }
                    break;
                default:

                    break;
            }
        }

        //Listing
        if(isset ($params['listing_id'])){
            $select
                ->where("review.listing_id = ?", $params['listing_id']);
        }

        //Listings
        if(isset ($params['listing_ids']))
        {
            if (count($params['listing_ids']))
            {
                $select->where("review.listing_id IN (?)", $params['listing_ids']);
            }
            else
            {
                $select -> where("1 = 0");
            }

        }

        //Listing title
        if(!empty($params['listing_title'])){
            $select->where("listing.title LIKE ?", "%".$params['listing_title']."%");
        }
        if(!empty($params['keyword'])){
            $select->where("listing.title LIKE ?", "%".$params['keyword']."%");
        }

        //user_id
        if( !empty($params['user_id']))
        {
            $select->where("review.user_id = ?", $params['user_id']);
        }

        //user_ids
        if(isset ($params['user_ids'])){
            if (count($params['user_ids'])) {
                $select->where("review.user_id IN (?)", $params['user_ids']);
            } else {
                $select -> where("1 = 0");
            }
        }

        //reviewer_name
        if( !empty($params['reviewer_name']))
        {
            $user_id = array();
            $list_reviewer = Engine_Api::_() -> ynmultilisting() -> getUsersByName($params['reviewer_name']);
            foreach($list_reviewer as $item) {
                $user_id[] = $item ->  getIdentity();
            }
            if(count($user_id)) {
                $select->where("review.user_id IN (?)", $user_id);
            }
			else {
				$select->where("1 = 0");
			}
        }
        //title
        if( !empty($params['title']))
        {
            $select->where("review.title LIKE ?", '%'.$params['title'].'%');
        }

        //from date
        if( !empty($params['from_date']) )
        {
            $select->where("review.creation_date >= ?", date('Y-m-d H:i:s', strtotime($params['from_date'])));
        }

        //to date
        if( !empty($params['to_date']) )
        {
            $select->where("review.creation_date <= ?", date('Y-m-d H:i:s', strtotime($params['to_date'])));
        }

        //filter rating
        if (!empty($params['filter_rating']))
        {
            $select -> where("review.overal_rating = ?", intval($params['filter_rating']));
        }

        if (empty($params['direction'])) {
            $params['direction'] = 'DESC';
        }

        if (!empty($params['order']) && !empty($params['direction'])) {
            $select -> order($params['order'] . ' ' . $params['direction']);
        } else {
            $select -> order("review.review_id DESC");
        }
        return $select;
    }
}
