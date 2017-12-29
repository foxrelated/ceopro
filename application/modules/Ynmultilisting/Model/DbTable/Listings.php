<?php

class Ynmultilisting_Model_DbTable_Listings extends Engine_Db_Table
{
    protected $_rowClass = 'Ynmultilisting_Model_Listing';

    public function getTotalListingsByUser($user_id)
    {
        $select = $this->select()
            ->where("status NOT IN (?)", array('draft', 'expired'))
            ->where('deleted = ?', '0')
            ->where('user_id = ?', $user_id);
        return count($this->fetchAll($select));
    }

    public function getAllChildrenListingsByCategory($node)
    {
        $return_arr = array();
        $cur_arr = array();
        $list_categories = array();
        Engine_Api::_()->getItemTable('ynmultilisting_category')->appendChildToTree($node, $list_categories);
        foreach ($list_categories as $category) {
            $select = $this->select()->where('category_id = ?', $category->category_id);
            $cur_arr = $this->fetchAll($select);
            if (count($cur_arr) > 0) {
                $return_arr[] = $cur_arr;
            }
        }
        return $return_arr;
    }

    public function getListingsByCategory($category_id)
    {
        $select = $this->select()->where('category_id = ?', $category_id);
        return $this->fetchAll($select);
    }

    public function getTotalComments()
    {
        $select = $this->select()->from($this, new Zend_Db_Expr("SUM(comment_count) as total_comment_count"));
        return $this->fetchRow($select)->total_comment_count;
    }

    public function getTotalListings($listingTypeId = null)
    {
        $select = $this->select();
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        $select->where('deleted = ?', '0');
        return count($this->fetchAll($select));
    }

    public function getPublishedListings($listingTypeId = null)
    {
        $select = $this->select()->where("status = 'open'")->where("approved_status = 'approved'");
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        return count($this->fetchAll($select));
    }

    public function getDraftListings($listingTypeId = null)
    {
        $select = $this->select()->where("status = 'draft'");
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        return count($this->fetchAll($select));
    }

    public function getClosedListings($listingTypeId = null)
    {
        $select = $this->select()->where("status = 'closed'");
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        return count($this->fetchAll($select));
    }

    public function getOpenListings($listingTypeId = null)
    {
        $select = $this->select()->where("status = 'open'");
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        return count($this->fetchAll($select));
    }

    public function getApprovedListings($listingTypeId = null)
    {
        $select = $this->select()->where("approved_status = 'approved'");
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        return count($this->fetchAll($select));
    }

    public function getDisApprovedListings($listingTypeId = null)
    {
        $select = $this->select()->where("approved_status = 'denied'");
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        return count($this->fetchAll($select));
    }

    public function getFeaturedListings($listingTypeId = null)
    {
        $select = $this->select()->where("featured = '1'");
        if ($listingTypeId) {
            $select->where('listingtype_id = ?', $listingTypeId);
        }
        return count($this->fetchAll($select));
    }

    public function getReviewCount($listingTypeId = null)
    {
        $tableReview = Engine_Api::_()->getDbTable('reviews', 'ynmultilisting');
        $name = $tableReview->info('name');
        $select = $tableReview->select()
            ->from($name, 'COUNT(*) AS count');
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
        return $select->query()->fetchColumn(0);
    }

    public function getListingsPaginator($params = array())
    {
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased') && !isset($params['admin'])) {
            $params = Engine_Api::_()->ynlocationbased()->mergeWithCookie('ynmultilisting', $params);
        }
        $paginator = Zend_Paginator::factory($this->getListingsSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getListingsSelect($params = array())
    {
        if (!empty($params['quicklink_id'])) {
            $quicklink_id = $params['quicklink_id'];
            unset($params['quicklink_id']);
            $quicklink = Engine_Api::_()->getItem('ynmultilisting_quicklink', $quicklink_id);
            return $quicklink->getListingsSelect($params);
        }
        $listingTbl = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $listingTblName = $listingTbl->info('name');

        $searchTable = Engine_Api::_()->fields()->getTable('ynmultilisting_listing', 'search');
        $searchTableName = $searchTable->info('name');

        $userTbl = Engine_Api::_()->getDbtable('users', 'user');
        $userTblName = $userTbl->info('name');

        $categoryTbl = Engine_Api::_()->getItemTable('ynmultilisting_category');
        $categoryTblName = $categoryTbl->info('name');

        $tagsTbl = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tagsTblName = $tagsTbl->info('name');

        $postTable = Engine_Api::_()->getItemTable('ynmultilisting_post');
        $postTblName = $postTable->info('name');

        $target_distance = $base_lat = $base_lng = "";
        if (isset($params['lat'])) {
            $base_lat = $params['lat'];
        }
        if (isset($params['long'])) {
            $base_lng = $params['long'];
        }
        //Get target distance in miles
        if (isset($params['within'])) {
            $target_distance = $params['within'];
            if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi') == 'km')
                $target_distance *= Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.mi.to.km', 0.621371);
        }

        $select = $listingTbl->select();
        $select->setIntegrityCheck(false);

        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select->from("$listingTblName as listing", new Zend_Db_Expr("listing.*, COUNT($postTblName.post_id) as discuss_count, ( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance"));
            $select->where("latitude <> ''");
            $select->where("longitude <> ''");
        } else {
            $select->from("$listingTblName as listing", new Zend_Db_Expr("listing.*, COUNT($postTblName.post_id) as discuss_count"));
        }
        $select
            ->joinLeft("$postTblName", "$postTblName.listing_id = listing.listing_id", "")
            ->joinLeft("$userTblName as user", "user.user_id = listing.user_id", "")
            ->joinLeft("$categoryTblName as category", "category.category_id = listing.category_id", "")
            ->joinLeft("$searchTableName as search", "search.item_id = listing.listing_id", "");

        $select->group("listing.listing_id");
        $tmp = array();
        foreach ($params as $k => $v) {
            if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                continue;
            } else if (false !== strpos($k, '_field_')) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } else if (false !== strpos($k, '_alias_')) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $params = $tmp;

        if (isset($params['listing_title']) && $params['listing_title'] != '') {
            $select->where('listing.title LIKE ?', '%' . $params['listing_title'] . '%');
        }

        if (isset($params['owner']) && $params['owner'] != '') {
            $select->where('user.displayname LIKE ?', '%' . $params['owner'] . '%');
        }

        if (!empty($params['category']) && $params['category'] != 'all') {
            $categorySelect = $categoryTbl->select()->where('option_id = ?', $params['category']);
            $category = $categoryTbl->fetchRow($categorySelect);
            if ($category) {
                $tree = array();
                $node = $categoryTbl->getNode($category->getIdentity());
                Engine_Api::_()->getItemTable('ynmultilisting_category')->appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select->where('listing.category_id IN (?)', $categories);
            }
        }

        if (!empty($params['category_id']) && $params['category_id'] != 'all') {
            $node = $categoryTbl->getNode($params['category_id']);
            if ($node) {
                $tree = array();
                Engine_Api::_()->getItemTable('ynmultilisting_category')->appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select->where('listing.category_id IN (?)', $categories);
            }
        }

        if (isset($params['listingtype_id']) && $params['listingtype_id'] != '0') {
            $select->where("listing.listingtype_id = ?", $params['listingtype_id']);
        }

        if (isset($params['category_ids']) && is_array($params['category_ids']) && !empty($params['category_ids'])) {
            $select->where('listing.category_id IN (?)', $params['category_ids']);
        }

        if (isset($params['approved_status']) && $params['approved_status'] != 'all') {
            $select->where('listing.approved_status = ?', $params['approved_status']);
        }

        if (isset($params['status']) && $params['status'] != 'all') {
            $select->where('listing.status = ?', $params['status']);
        }

        if (isset($params['featured']) && $params['featured'] != 'all') {
            $select->where('listing.featured = ?', $params['featured']);
        }

        if (isset($params['user_id'])) {
            $select->where('listing.user_id = ?', $params['user_id']);
        } else {
            if (empty($params['admin'])) {
                $select
                    ->where('listing.search = ?', 1)
                    ->where('listing.status = ?', 'open')
                    ->where('listing.approved_status = ?', 'approved');
            }
        }

        if (isset($params['owner_ids']) && is_array($params['owner_ids']) && !empty($params['owner_ids'])) {
            $select->where('listing.user_id IN (?)', $params['owner_ids']);
        }

        if (isset($params['expire_from'])) {
            $select->where('listing.expiration_date >= ?', $params['expire_from']);
        }

        if (isset($params['expire_to'])) {
            $select->where('listing.expiration_date <= ?', $params['expire_to']);
        }

        if (isset($params['price']) && is_array($params['price']) && !empty($params['price'])) {
            $where = '';
            foreach ($params['price'] as $price) {
                $priceArr = unserialize($price);
                if (!empty($priceArr['from']) || !empty($priceArr['to'])) {
                    $subWhere = '';
                    if (!empty($priceArr['from'])) {
                        $from = $priceArr['from'];
                        $subWhere = "(listing.`price` >= $from)";
                    }
                    if (!empty($priceArr['to'])) {
                        $to = $priceArr['to'];
                        $subWhere .= ($subWhere) ? " AND (listing.`price` <= $to)" : "(listing.`price` <= $to)";
                    }
                    $currency = $priceArr['currency'];
                    $subWhere .= "AND (listing.`currency` = '$currency')";
                    $where .= ($where) ? " OR ($subWhere)" : "($subWhere)";
                }
            }
            if ($where) $select->where($where);
        }
        //Tags
        if (!empty($params['tag'])) {
            $select->setIntegrityCheck(false)->joinLeft($tagsTblName, "$tagsTblName.resource_id = listing.listing_id", "")->where($tagsTblName . '.resource_type = ?', 'ynmultilisting_listing')->where($tagsTblName . '.tag_id = ?', $params['tag']);
        }

        $searchParts = Engine_Api::_()->fields()->getSearchQuery('ynmultilisting_listing', $params);
        foreach ($searchParts as $k => $v) {
            $select->where("search.$k", $v);
        }

        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select->having("distance <= $target_distance");
            $select->order("distance ASC");
        }

        $select->where('listing.deleted = ?', '0');

        if (isset($params['listing_ids']) && is_array($params['listing_ids']) && !empty($params['listing_ids'])) {
            $select->orwhere("listing.listing_id IN (?) AND listing.deleted = 0 AND listing.search = 1 AND listing.status = 'open' AND listing.approved_status = 'approved'", $params['listing_ids']);
        }

        if (!empty($params['random'])) {
            $select->order("rand()");
        } else if (isset($params['order'])) {
            if (empty($params['direction'])) {
                $params['direction'] = ($params['order'] == 'listing.title') ? 'ASC' : 'DESC';
            }

            if ($params['order'] == 'discuss_count') {
                $select->order("COUNT($postTblName.post_id)" . ' ' . $params['direction']);
            } else {
                $select->order($params['order'] . ' ' . $params['direction']);
            }
        } else {
            if (!empty($params['direction'])) {
                $select->order('listing.listing_id' . ' ' . $params['direction']);
            } else {
                $select->order('listing.listing_id DESC');
            }
        }

        if (!empty($params['limit'])) {
            $select->limit($params['limit']);
        }


        $searchParts = Engine_Api::_()->fields()->getSearchQuery('ynmultilisting_listing', $params);
        foreach ($searchParts as $k => $v) {
            $select->where("search.$k", $v);
        }

        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select->having("distance <= $target_distance");
            $select->order("distance ASC");
        }
        return $select;
    }

    public function getListingTypeListings($listingtype_id, $params = array())
    {
        $tableName = $this -> info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
        }
        else
        {
            $select = $this->select()->from("$tableName", array("$tableName.*"));
        }
        $select->where('listingtype_id = ?', $listingtype_id);
        if (isset($params['title'])) {
            $select->where('title LIKE ?', '%' . $params['title'] . '%');
        }
        if (isset($params['publish'])) {
            $select->where('search = ?', 1)
                ->where('status = ?', 'open')
                ->where('approved_status = ?', 'approved');
        }
        if (isset($params['order'])) {
            $direction = (isset($params['direction'])) ? $params['direction'] : 'ASC';
            $select->order($params['order'] . ' ' . $direction);
        }

        if (isset($params['highlight']) && $params['highlight'] == 1) {
            $select->where("highlight = 1");
        }

        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }

        $select->where('deleted = ?', 0);
        $listings = $this->fetchAll($select);
        return $listings;
    }

    public function getListingsByText($text)
    {
        $select = $this->select()->where("title LIKE ?", "%$text%");
        $select
            ->where('search = ?', 1)
            ->where('status = ?', 'open')
            ->where('approved_status = ?', 'approved')
            ->where('deleted = ?', 0);
        return $this->fetchAll($select);
    }

    public function getListingByCategoryId($categoryId)
    {
        $tableName = $this -> info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
        }
        else
        {
            $select = $this->select()->from("$tableName", array("$tableName.*"));
        }
        $categoryTbl = Engine_Api::_()->getItemTable('ynmultilisting_category');
        if (isset($categoryId) && $categoryId != 'all') {
            $node = $categoryTbl->getNode($categoryId);
            if ($node) {
                $tree = array();
                Engine_Api::_()->getItemTable('ynmultilisting_category')->appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select->where('category_id IN (?)', $categories);
                $select
                    ->where('search = ?', 1)
                    ->where('status = ?', 'open')
                    ->where('approved_status = ?', 'approved')
                    ->where('deleted = ?', 0);
                return $this->fetchAll($select);
            }
        } else {
            return array();
        }
    }
}