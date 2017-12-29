<?php

class Ynmember_Api_Core extends Core_Api_Abstract
{

    public function getMutualFriends($subject)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        // Don't render this if friendships are disabled
        if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
            return false;
        }

        // Diff friends
        $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
        $friendsName = $friendsTable->info('name');

        // Mututal friends/following mode
        $col1 = 'resource_id';
        $col2 = 'user_id';

        $select = new Zend_Db_Select($friendsTable->getAdapter());
        $select
            ->from($friendsName, $col1)
            ->join($friendsName, "`{$friendsName}`.`{$col1}`=`{$friendsName}_2`.{$col1}", null)
            ->where("`{$friendsName}`.{$col2} = ?", $viewer->getIdentity())
            ->where("`{$friendsName}_2`.{$col2} = ?", $subject->getIdentity())
            ->where("`{$friendsName}`.active = ?", 1)
            ->where("`{$friendsName}_2`.active = ?", 1);
        // Now get all common friends
        $uids = array();
        foreach ($select->query()->fetchAll() as $data) {
            $uids[] = $data[$col1];
        }

        if (count($uids) <= 0) {
            return false;
        }
        // Get paginator
        $usersTable = Engine_Api::_()->getItemTable('user');
        $select = $usersTable->select()
            ->where('user_id IN(?)', $uids);

        $results = $usersTable->fetchAll($select);
        return $results;
    }

    public function setCoverPhoto($user, $photo)
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else
            if (is_array($photo) && !empty($photo['tmp_name'])) {
                $file = $photo['tmp_name'];
            } else
                if (is_string($photo) && file_exists($photo)) {
                    $file = $photo;
                } else {
                    throw new Event_Model_Exception('invalid argument passed to setPhoto');
                }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $user->getIdentity(),
            'parent_type' => 'user'
        );

        // Save
        $storage = Engine_Api::_()->storage();
        $angle = 0;
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($file);

            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 8 :
                        $angle = 90;
                        break;
                    case 3 :
                        $angle = 180;
                        break;
                    case 6 :
                        $angle = -90;
                        break;
                }
            }
        }
        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file);
        if ($angle != 0)
            $image->rotate($angle);
        $image->resize(720, 720)->write($path . '/m_' . $name)->destroy();

        $iMain = $storage->create($path . '/m_' . $name, $params);

        // Remove temp files
        @unlink($path . '/m_' . $name);

        // Update row
        $user->modified_date = date('Y-m-d H:i:s');
        $user->cover_id = $iMain->file_id;
        $user->save();

        return $user;
    }

    public function getUsersByName($name)
    {
        $tableUser = Engine_Api::_()->getItemTable('user');
        $select = $tableUser->select();
        $select->where('displayname LIKE ?', '%' . $name . '%');
        $result = $tableUser->fetchAll($select);
        return $result;
    }

    public function checkYouNetPlugin($name)
    {
        $table = Engine_Api::_()->getDbTable('modules', 'core');
        $select = $table->select()->where('name = ?', $name)->where('enabled  = 1');
        $result = $table->fetchRow($select);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function getGateway($gateway_id)
    {
        return $this->getPlugin($gateway_id)->getGateway();
    }

    public function getPlugin($gateway_id)
    {
        if (null === $this->_plugin) {
            if (null == ($gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id))) {
                return null;
            }
            Engine_Loader::loadClass($gateway->plugin);
            if (!class_exists($gateway->plugin)) {
                return null;
            }
            $class = str_replace('Payment', 'Ynmember', $gateway->plugin);

            Engine_Loader::loadClass($class);
            if (!class_exists($class)) {
                return null;
            }

            $plugin = new $class($gateway);
            if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
                throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
            }
            $this->_plugin = $plugin;
        }
        return $this->_plugin;
    }

    public function getMemberPaginator($params = array())
    {
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $params = Engine_Api::_()->ynlocationbased()->mergeWithCookie('ynmember', $params);
        }
        $paginator = Zend_Paginator::factory($this->getMemberSelect($params));
        return $paginator;
    }

    public function getMemberSelect($params = array())
    {
        // Process options
        $options = $params;
        $tmp = array();
        foreach ($options as $k => $v) {
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
        $options = $tmp;

        // Get table info
        $table = Engine_Api::_()->getItemTable('user');
        $userTableName = $table->info('name');

        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $searchTableName = $searchTable->info('name');

        $displayname = @$options['displayname'];
        if (!empty($options['extra'])) {
            extract($options['extra']); // is_online, has_photo, submit
        }

        // Construct query
        $select = $table->select()->setIntegrityCheck(false)
            ->from($userTableName)
            ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
            ->where("{$userTableName}.search = ?", 1)
            ->where("{$userTableName}.enabled = ?", 1);

        //Get your location
        $target_distance = $base_lat = $base_lng = "";
        if (isset($params['lat']) && $params['lat'])
            $base_lat = $params['lat'];
        if (isset($params['long']) && $params['long'])
            $base_lng = $params['long'];
        if (isset($params['within'])) {
            $target_distance = $params['within'];
            if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi') == 'km')
                $target_distance *= Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.mi.to.km', 0.621371);
        }

        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $workPlaceTbl = Engine_Api::_()->getItemTable('ynmember_workplace');
            $userIds1 = $workPlaceTbl->getUserIdByLocation($base_lat, $base_lng, $target_distance);
            $livePlaceTbl = Engine_Api::_()->getItemTable('ynmember_liveplace');
            $userIds2 = $livePlaceTbl->getUserIdByLocation($base_lat, $base_lng, $target_distance);
            $userIds = array_unique(array_merge($userIds1, $userIds2));
            if (count($userIds)) {
                $select->where("{$userTableName}.user_id IN (?)", $userIds);
            }
            else
                $select->where("{$userTableName}.user_id IN ('')");
        }
        // Build the photo and is online part of query
        if (isset($has_photo) && !empty($has_photo)) {
            $select->where($userTableName . '.photo_id != ?', "0");
        }

        if (isset($is_online) && !empty($is_online)) {
            $select
                ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
                ->group("engine4_user_online.user_id")
                ->where($userTableName . '.user_id != ?', "0");
        }

        // Add displayname
        if (!empty($displayname)) {
            $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
        }

        // Build search part of query
        $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
        foreach ($searchParts as $k => $v) {
            $select->where("`{$searchTableName}`.{$k}", $v);
        }

        switch ($options['order']) {
            case 'az':
                $select->order("{$userTableName}.displayname ASC");
                break;
            case 'za':
                $select->order("{$userTableName}.displayname DESC");
                break;
            case 'recent':
                $select->order("{$userTableName}.creation_date DESC");
                break;
            case 'most_view':
                $select->order("{$userTableName}.view_count DESC");
                break;
            case 'most_like':
                $select->order("{$userTableName}.like_count DESC");
                break;
            case 'most_rating':
                $select->order("{$userTableName}.rating DESC");
                break;
            default:
                break;
        }

        //function for SHOW
        if (isset($options['show'])) {
            $show = $options['show'];
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                // Filter by FRIEND
                if ($show == 'friend') {
                    $friendIds = array();
                    foreach ($viewer->membership()->getMembersInfo() as $row) {
                        $friendIds[] = $row->user_id;
                    }
                    if (count($friendIds)) {
                        $select->where("user_id IN (?)", $friendIds);
                    } else {
                        $select->where("1 = 0");
                    }
                } // Filter by NETWORK
                else if ($show == 'network') {
                    $network_table = Engine_Api::_()->getDbtable('membership', 'network');
                    $network_select = $network_table->select('resource_id')->where('user_id = ?', $viewer->getIdentity());
                    $networks = $network_table->fetchAll($network_select);
                    $userNetworkIds = array();
                    if (count($networks)) {
                        foreach ($networks as $network) {
                            $network = Engine_Api::_()->getItem('network', $network->resource_id);
                            foreach ($network->membership()->getMembersInfo() as $row) {
                                if (!in_array($row->user_id, $userNetworkIds)) {
                                    $userNetworkIds[] = $row->user_id;
                                }
                            }
                        }
                        if (count($userNetworkIds)) {
                            $select->where("user_id IN (?)", $userNetworkIds);
                        } else {
                            $select->where("1 = 0");
                        }
                    } else {
                        $select->where("1 = 0");
                    }
                } // Filter by FEATURED
                else if ($show == 'featured') {
                    $featureTbl = Engine_Api::_()->getItemTable('ynmember_feature');
                    $featureTblName = $featureTbl->info('name');
                    $select->joinLeft($featureTblName, "{$userTableName}.user_id = {$featureTblName}.user_id");
                    $select->where("active = ?", '1');
                } // Filter by I LIKED
                else if ($show == 'like') {
                    $coreLikeTbl = Engine_Api::_()->getDbTable('likes', 'core');
                    $likeSelect = $coreLikeTbl->select()
                        ->where("poster_type = ? ", 'user')
                        ->where("poster_id = ? ", $viewer->getIdentity())
                        ->where("resource_type = ? ", 'user');
                    $likes = $coreLikeTbl->fetchAll($likeSelect);
                    if (count($likes)) {
                        $userLikeIds = array();
                        foreach ($likes as $like) {
                            $userLikeIds[] = $like->resource_id;
                        }
                        if (count($userLikeIds)) {
                            $select->where("user_id IN (?)", $userLikeIds);
                        }
                    } else {
                        $select->where("1 = 0");
                    }
                }
            }
        }
        return $select;
    }

    public function canAddFriendButton($subject)
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        if (is_null($subject)) {
            return false;
        }

        // Not logged in
        if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
            return false;
        }

        // No blocked
        if ($viewer->isBlockedBy($subject)) {
            return false;
        }

        // Check if friendship is allowed in the network
        $eligible = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
        if (!$eligible) {
            return false;
        } // check admin level setting if you can befriend people in your network
        else if ($eligible == 1) {
            $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            $networkMembershipName = $networkMembershipTable->info('name');

            $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
            $select
                ->from($networkMembershipName, 'user_id')
                ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
                ->where("`{$networkMembershipName}_2`.user_id = ?", $subject->getIdentity());

            $data = $select->query()->fetch();

            if (empty($data)) {
                return false;
            }
        }

        // One-way mode
        $direction = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

        if (!$direction) {
            $viewerRow = $viewer->membership()->getRow($subject);
            $subjectRow = $subject->membership()->getRow($viewer);

            // Viewer?
            if (null === $subjectRow) {
                return array(
                    'label' => 'Follow',
                    'icon' => 'application/modules/User/externals/images/friends/add.png',
                    'class' => 'smoothbox',
                    'route' => 'user_extended',
                    'params' => array(
                        'controller' => 'friends',
                        'action' => 'add',
                        'user_id' => $subject->getIdentity()
                    ),
                );
            } else if ($subjectRow->resource_approved == 0) {
                // Cancel follow request
                return false;
            } else {
                // Unfollow
                return false;
            }

            // Subject?
            if (null === $viewerRow) {
                // Do nothing
            } else if ($viewerRow->resource_approved == 0) {
                // Approve follow request
                return false;
            } else {
                // Remove as follower?
                return false;
            }
        } // Two-way mode
        else {
            $row = $viewer->membership()->getRow($subject);

            if (null === $row) {
                // Add
                return array(
                    'label' => Zend_Registry::get("Zend_Translate")->_("Add Friend"),
                    'icon' => 'application/modules/User/externals/images/friends/add.png',
                    'class' => 'smoothbox',
                    'route' => 'user_extended',
                    'params' => array(
                        'controller' => 'friends',
                        'action' => 'add',
                        'user_id' => $subject->getIdentity()
                    ),
                );
            } else if ($row->user_approved == 0) {
                return false;
            } else if ($row->resource_approved == 0) {
                return false;
            } else {
                return false;
            }
        }

    }

    function getItemTitle($type)
    {
        $const = array(
            'blog_new' => 'blog',
            'ynblog_new' => 'blog',
            'video_new' => 'video',
            'ynvideo_video_new' => 'video',
            'group_create' => 'group',
            'advgroup_create' => 'group',
            'event_create' => 'event',
            'ynevent_create' => 'event',
        );

        if (!in_array($type, array_keys($const))) {
            return "item";
        }
        return $const[$type];
    }

    function getMemberPhoto($member, $type = null)
    {
        if (!is_null($type)) {
            $photoUrl = $member->getPhotoUrl($type);
        } else {
            $photoUrl = $member->getPhotoUrl();
            if (!$photoUrl) {
                $photoUrl = $member->getPhotoUrl('thumb.profile');
            }
        }
        if (!$photoUrl) {
            $view = Zend_Registry::get("Zend_View");
            $photoUrl = $view->layout()->staticBaseUrl . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
        }
        return $photoUrl;
    }

    function canFilterByBirthday($user)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $isHidden = false;
        $relationship = 'everyone';
        if ($viewer && $viewer->getIdentity()) {
            if ($viewer->getIdentity() == $user->getIdentity()) {
                $relationship = 'self';
            } else if ($viewer->membership()->isMember($user, true)) {
                $relationship = 'friends';
            } else {
                $relationship = 'registered';
            }
        }

        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
        foreach ($fieldStructure as $map) {
            $field = $map->getChild();
            $value = $field->getValue($user);
            $firstValue = $value;
            if (is_array($value) && !empty($value)) {
                $firstValue = $value[0];
            }
            if ($field->type == 'birthdate') {
                if (!empty($firstValue->privacy) && $relationship != 'self') {
                    if ($firstValue->privacy == 'self' && $relationship != 'self') {
                        $isHidden = true; //continue;
                    } else if ($firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self')) {
                        $isHidden = true; //continue;
                    } else if ($firstValue->privacy == 'registered' && $relationship == 'everyone') {
                        $isHidden = true; //continue;
                    }
                }
                break;
            }
        }
        if ($isHidden) {
            return false;
        } else {
            return true;
        }
    }
}
