<?php

class Ynfbpp_Api_Core
{

    /**
     * @property User_Model_User
     */
    private static $_viewer = NULL;

    public $view;

    public function __construct()
    {
        $view = Zend_Registry::get('Zend_View');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Ynfbpp/View/Helper', 'Ynfbpp_View_Helper_');
        $view -> addScriptPath(APPLICATION_PATH . '/application/modules/Ynfbpp/views/scripts');
        $this -> view = $view;
    }

    /**
     * get current viewer
     *  @return User_Model_User
     */
    public static function getViewer()
    {
        if (self::$_viewer == NULL)
        {
            self::$_viewer = Engine_Api::_() -> user() -> getViewer();
        }
        return self::$_viewer;
    }

    public function getUserPopupProfileFields()
    {
        $model = new Ynfbpp_Model_DbTable_Popup;
        $select = $model -> select() -> from($model -> info('name'), array(
            'field_id'
        )) -> where('enabled=?', 1) -> order('ordering asc');
        return $model -> getAdapter() -> fetchCol($select);
    }

    protected function _allowMessage($viewer, $subject)
    {
        // Not logged in
        if (!$viewer -> getIdentity() || $viewer -> getGuid(false) === $subject -> getGuid(false))
        {
            return false;
        }

        // Get setting?
        $permission = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'messages', 'create');
        if (Authorization_Api_Core::LEVEL_DISALLOW === $permission)
        {
            return false;
        }
        $messageAuth = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'messages', 'auth');
        if ($messageAuth == 'none')
        {
            return false;
        }
        else
            if ($messageAuth == 'friends')
            {
                // Get data
                $direction = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.direction', 1);
                if (!$direction)
                {
                    //one way
                    $friendship_status = $viewer -> membership() -> getRow($subject);
                }
                else
                    $friendship_status = $subject -> membership() -> getRow($viewer);

                if (!$friendship_status || $friendship_status -> active == 0)
                {
                    return false;
                }
            }
        return true;
    }

    public function _getUserActions($user, $viewer = null)
    {
        $actions = array();
        if (null === $viewer)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!$viewer || !$viewer -> getIdentity() || $user -> isSelf($viewer))
        {
            return $actions;
        }

        $direction = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.direction', 1);

        // Get data
        if (!$direction)
        {
            $row = $user -> membership() -> getRow($viewer);
        }
        else
            $row = $viewer -> membership() -> getRow($user);

        // Render

        // Check if friendship is allowed in the network
        $eligible = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.eligible', 2);
        if ($eligible == 0)
        {
            return $actions;
        }

        // check admin level setting if you can befriend people in your network
        else
            if ($eligible == 1)
            {

                $networkMembershipTable = Engine_Api::_() -> getDbtable('membership', 'network');
                $networkMembershipName = $networkMembershipTable -> info('name');

                $select = new Zend_Db_Select($networkMembershipTable -> getAdapter());
                $select -> from($networkMembershipName, 'user_id') -> join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null) -> where("`{$networkMembershipName}`.user_id = ?", $viewer -> getIdentity()) -> where("`{$networkMembershipName}_2`.user_id = ?", $user -> getIdentity());

                $data = $select -> query() -> fetch();

                if (empty($data))
                {
                    return array();
                }
            }

        if (!$direction)
        {
            // one-way mode
            if (null === $row)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'add',
                    'format' => 'smoothbox',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Follow'), array(
                    'class' => 'buttonlink smoothbox',
                    'title' => $this -> view -> translate('Add Friend')
                ));
            }
            else
                if ($row -> resource_approved == 0)
                {
                    $actions[] = $this -> view -> htmlLink(array(
                        'route' => 'user_extended',
                        'controller' => 'friends',
                        'action' => 'cancel',
                        'format' => 'smoothbox',
                        'user_id' => $user -> user_id
                    ), $this -> view -> translate('Cancel Request'), array(
                        'class' => 'buttonlink smoothbox',
                        'title' => $this -> view -> translate('Cancel Follow Request')
                    ));
                }
                else
                {
                    $actions[] = $this -> view -> htmlLink(array(
                        'route' => 'user_extended',
                        'controller' => 'friends',
                        'action' => 'remove',
                        'format' => 'smoothbox',
                        'user_id' => $user -> user_id
                    ), $this -> view -> translate('Unfollow'), array(
                        'class' => 'buttonlink smoothbox icon_friend_remove',
                        'title' => $this -> view -> translate('Unfollow')

                    ));
                }

        }
        else
        {
            // two-way mode
            if (null === $row)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'add',
                    'format' => 'smoothbox',
                    'user_id' => $user -> user_id
                ), '<span class="ynicon yn-user-plus"></span>'.$this -> view -> translate('Add to my friend'), array(
                    'class' => 'smoothbox active',
                    'title' => $this -> view -> translate('Add Friend')
                ));
            }
            else
                if ($row -> user_approved == 0)
                {
                    $actions[] = $this -> view -> htmlLink(array(
                        'route' => 'user_extended',
                        'controller' => 'friends',
                        'action' => 'cancel',
                        'format' => 'smoothbox',
                        'user_id' => $user -> user_id
                    ), '<span class="ynicon yn-stop"></span>'.$this -> view -> translate('Cancel Request'), array(
                        'class' => 'buttonlink smoothbox',
                        'title' => $this -> view -> translate('Cancel Request')
                    ));
                }
                else
                    if ($row -> resource_approved == 0)
                    {
                        $actions[] = $this -> view -> htmlLink(array(
                            'route' => 'user_extended',
                            'controller' => 'friends',
                            'format' => 'smoothbox',
                            'action' => 'confirm',
                            'user_id' => $user -> user_id
                        ), '<span class="ynicon yn-check"></span>'.$this -> view -> translate('Accept Request'), array(
                            'class' => 'buttonlink smoothbox',
                            'title' => $this -> view -> translate('Accept Request')
                        ));
                    }
                    else
                        if ($row -> active)
                        {
                            $actions[] = $this -> view -> htmlLink(array(
                                'route' => 'user_extended',
                                'controller' => 'friends',
                                'format' => 'smoothbox',
                                'action' => 'remove',
                                'user_id' => $user -> user_id
                            ), '<span class="ynicon yn-check"></span>'.$this -> view -> translate('Friend'), array(
                                'class' => 'buttonlink smoothbox',
                                'title' => $this -> view -> translate('Friend')
                            ));

                        }
        }

        // check if allow message
        if($this->_allowMessage($viewer, $user)){
            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'messages_general',
                'action' => 'compose',
                'to' => $user -> user_id
            ), '<span class="ynicon yn-comment-square"></span>'.$this -> view -> translate('Message'), array(
                'class' => 'buttonlink',
                'title' => $this -> view -> translate('Message')
            ));
        }

        if (Engine_Api::_()->hasModuleBootstrap('ynmember')) {
            $label = ($user->isSelf($viewer))
                ? Zend_Registry::get("Zend_Translate")->_("Share yourself")
                : Zend_Registry::get("Zend_Translate")->_("Share this member");
            if($this->_allowMessage($viewer, $user)){
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'ynmember_extended',
                    'controller' => 'member',
                    'action' => 'share',
                    'type' => 'user',
                    'id' => $user->getIdentity(),
                ), '<span class="ynicon yn-share"></span>'.$this -> view -> translate($label), array(
                    'class' => 'buttonlink smoothbox',
                    'title' => $this -> view -> translate($label)
                ));
            }
        }

        // check to add reward point
        if (Engine_Api::_() -> hasModuleBootstrap('ynrewardpoints'))
        {
            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'ynrewardpoints_general',
                'action' => 'give',
                'to' => $user -> user_id
            ), $this -> view -> translate('Points'), array(
                'class' => 'buttonlink',
                'title' => $this -> view -> translate('Give Reward Points')
            ));
        }

        return $actions;
    }

    public function _getYnmemberActions($user, $viewer = null)
    {
        $actions = array();
        $resourceUser = $subject = $user;
        if (null === $viewer)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!$viewer || !$viewer -> getIdentity())
        {
            return $actions;
        }

        if (Engine_Api::_()->hasModuleBootstrap('ynmember')) {


            // get notification action
            if (!$user->isSelf($viewer)) {
                $notificationTbl = Engine_Api::_()->getDbTable('notifications', 'ynmember');
                $notification = $notificationTbl->getNotificationRow(array(
                    'resource_id' => $resourceUser->getIdentity(),
                    'user_id' => $viewer->getIdentity()
                ));
                if (is_null($notification) || $notification->active == '0') {
                    $label = Zend_Registry::get("Zend_Translate")->_("Get Notification");
                } else {
                    $label = Zend_Registry::get("Zend_Translate")->_("Stop Getting Notification");
                }

                if ($resourceUser->authorization()->isAllowed($viewer, 'get_notification')) {
                    $actions[] = $this->view->htmlLink(array(
                        'route' => 'ynmember_extended',
                        'controller' => 'member',
                        'action' => 'get-notification',
                        'id' => $user->getIdentity(),
                        'active' => (is_null($notification) || $notification->active == '0') ? 1 : 0,
                    ), '<span class="ynicon yn-global"></span>' . $this->view->translate($label), array(
                        'class' => 'smoothbox',
                        'title' => $this->view->translate($label)
                    ));
                }
            }

            if (!$user->isSelf($viewer)) {
                // suggest friend action
                $actions[] = $this->view->htmlLink(array(
                    'route' => 'ynmember_extended',
                    'controller' => 'member',
                    'action' => 'suggest-friend',
                    'id' => $user->getIdentity(),
                ), '<span class="ynicon yn-user"></span>' . $this->view->translate('Suggest friends'), array(
                    'class' => 'smoothbox',
                    'title' => $this->view->translate('Suggest friends')
                ));
            }

            // Like member action

            if (!$subject->likes()->isLike($viewer)) {
                $label = ($subject->isSelf($viewer))
                    ? Zend_Registry::get("Zend_Translate")->_("Like yourself")
                    : Zend_Registry::get("Zend_Translate")->_("Like this member");
                $action = 'like';
            } else {
                $label = ($subject->isSelf($viewer))
                    ? Zend_Registry::get("Zend_Translate")->_("Unlike yourself")
                    : Zend_Registry::get("Zend_Translate")->_("Unlike this member");
                $action = 'unlike';
            }

            $actions[] = $this->view->htmlLink(array(
                'route' => 'ynmember_extended',
                'controller' => 'member',
                'action' => $action,
                'type' => 'user',
                'id' => $user->getIdentity(),
            ), '<span class="ynicon yn-thumb-up"></span>' . $this->view->translate($label), array(
                'class' => 'smoothbox',
                'title' => $this->view->translate($label)
            ));

            // review and rate action
            $requireAuth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth');
            $can_review_members = ($requireAuth->setAuthParams('ynmember_user', null, 'can_review_members')->checkRequire());
            $can_review_oneself = ($requireAuth->setAuthParams('ynmember_user', null, 'can_review_oneself')->checkRequire());

            $label = Zend_Registry::get("Zend_Translate")->_("Review & Rate");

            //check hasReviewed
            $tableReview = Engine_Api::_()->getItemTable('ynmember_review');
            $HasReviewed = $tableReview->checkHasReviewed($subject->getIdentity(), $viewer->getIdentity());

            if (!$HasReviewed && ($can_review_members || $can_review_oneself)) {
                $actions[] = $this->view->htmlLink(array(
                    'route' => 'ynmember_general',
                    'controller' => 'index',
                    'action' => 'rate-member',
                    'id' => $user->getIdentity(),
                ), '<span class="ynicon yn-star-circle"></span>' . $this->view->translate($label), array(
                    'class' => 'smoothbox',
                    'title' => $this->view->translate($label)
                ));
            }
        }

        if (!$user->isSelf($viewer)) {
            if( !$subject->isBlockedBy($viewer) ) {
                $label = Zend_Registry::get("Zend_Translate")->_("Block member");
                $action = 'add';
            } else {
                $label = Zend_Registry::get("Zend_Translate")->_("Unblock member");
                $action = 'remove';
            }

            $actions[] = $this->view->htmlLink(array(
                'route' => 'user_extended',
                'controller' => 'block',
                'action' => $action,
                'user_id' => $user->getIdentity(),
            ), '<span class="ynicon yn-stop"></span>' . $this->view->translate($label), array(
                'class' => 'smoothbox',
                'title' => $this->view->translate($label)
            ));
        }

        return $actions;
    }

    public function _getEventActions($subject, $viewer = null)
    {

        $actions = array();

        if ($viewer == null)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!is_object($viewer) || !$viewer -> getIdentity())
        {
            return array();
        }

        $row = $subject -> membership() -> getRow($viewer);

        // Not yet associated at all
        if (null === $row)
        {
            if ($subject -> membership() -> isResourceApprovalRequired())
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'event_extended',
                    'controller' => 'member',
                    'action' => 'request',
                    'format' => 'smoothbox',
                    'event_id' => $subject -> getIdentity(),
                ), $this->view->translate('Request Membership'), array(
                    'class' => 'buttonlink smoothbox',
                ));
            }
            else
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'event_extended',
                    'controller' => 'member',
                    'format' => 'smoothbox',
                    'action' => 'join',
                    'event_id' => $subject -> getIdentity()
                ), '<span class="ynicon yn-calendar-plus"></span>'.$this->view->translate('Join event'), array(
                    'class' => 'active buttonlink smoothbox',
                ));
            }
        }
        elseif ($row -> active)
        {
            if (!$subject -> isOwner($viewer))
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'event_extended',
                    'controller' => 'member',
                    'action' => 'leave',
                    'format' => 'smoothbox',
                    'event_id' => $subject -> getIdentity()
                ),'<span class="ynicon yn-check"></span>'.$this->view->translate('Joined'), array(
                    'class' => 'buttonlink smoothbox',
                ));
            }
            else
            {

            }
        }
        elseif (!$row -> resource_approved && $row -> user_approved)
        {
            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'event_extended',
                'controller' => 'member',
                'action' => 'cancel',
                'format' => 'smoothbox',
                'event_id' => $subject -> getIdentity()
            ), $this->view->translate('Cancel Request'), array(
                'class' => 'buttonlink smoothbox',
            ));
        }
        elseif (!$row -> user_approved && $row -> resource_approved)
        {
            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'event_extended',
                'controller' => 'member',
                'action' => 'accept',
                'format' => 'smoothbox',
                'event_id' => $subject -> getIdentity()
            ), $this->view->translate('Accept Request'), array(
                'class' => 'buttonlink smoothbox',
            ));

            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'event_extended',
                'controller' => 'member',
                'action' => 'reject',
                'event_id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
            ), $this->view->translate('Reject Request'), array(
                'class' => 'buttonlink smoothbox',
            ));
        }

        $actions[] = $this -> view -> htmlLink(array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject -> getType(),
            'id' => $subject -> getIdentity(),
            'route' => 'default',
            'format' => 'smoothbox',
        ), '<span class="ynicon yn-share"></span>'.$this->view->translate('Share event'), array(
            'class' => 'buttonlink smoothbox',
        ));

        return $actions;
    }

    public function _getGroupActions($subject, $viewer = null)
    {
        $actions = array();

        if ($viewer == null)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!is_object($viewer) || $viewer -> getIdentity() == 0)
        {
            return $actions;
        }

        // invite
        if ($subject -> authorization() -> isAllowed($viewer, 'invite'))
        {

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'invite',
                'group_id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
                'route' => 'group_extended'
            ), '<span class="ynicon yn-envelope-o"></span>'.$this->view->translate('Invite'), array(
                'class' => 'buttonlink smoothbox',
            ));
        }

        // membership
        $row = $subject -> membership() -> getRow($viewer);

        // Not yet associated at all
        if (null === $row)
        {
            if ($subject -> membership() -> isResourceApprovalRequired())
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'request',
                    'format' => 'smoothbox',
                    'group_id' => $subject -> getIdentity(),
                    'route' => 'group_extended'
                ), $this->view->translate('Request Membership'), array(
                    'class' => 'buttonlink smoothbox',
                ));

            }
            else
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'join',
                    'format' => 'smoothbox',
                    'group_id' => $subject -> getIdentity(),
                    'route' => 'group_extended',
                ), '<span class="ynicon yn-user-plus"></span>'.$this->view->translate('Join group'), array(
                    'class' => 'buttonlink smoothbox active',
                ));
            }
        }
        elseif ($row -> active)
        {
            if (!$subject -> isOwner($viewer))
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'leave',
                    'group_id' => $subject -> getIdentity(),
                    'format' => 'smoothbox',
                    'route' => 'group_extended'
                ), '<span class="ynicon yn-check"></span>'.$this->view->translate('Joined'), array(
                    'class' => 'buttonlink smoothbox',
                ));
            }
//            else
//            {
//
//                $actions[] = $this -> view -> htmlLink(array(
//                    'action' => 'delete',
//                    'group_id' => $subject -> getIdentity(),
//                    'format' => 'smoothbox',
//                    'route' => 'group_specific'
//                ), '<span class="ynicon yn-del"></span>'.$this->view->translate('Delete group'), array(
//                    'class' => 'buttonlink smoothbox',
//                ));
//            }
        }
        elseif (!$row -> resource_approved && $row -> user_approved)
        {

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'cancel',
                'format' => 'smoothbox',
                'group_id' => $subject -> getIdentity(),
                'route' => 'group_extended'
            ), '<span class="ynicon yn-stop"></span>'.$this->view->translate('Cancel request'), array(
                'class' => 'buttonlink smoothbox',
            ));
        }
        elseif (!$row -> user_approved && $row -> resource_approved)
        {

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'accept',
                'group_id' => $subject -> getIdentity(),
                'route' => 'group_extended'
            ), $this->view->translate('Accept Request'), array(
                'class' => 'buttonlink smoothbox',
            ));

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'reject',
                'group_id' => $subject -> getIdentity(),
                'route' => 'group_extended'
            ), $this->view->translate('Ignore Request'), array(
                'class' => 'buttonlink smoothbox',
            ));
        }

        $actions[] = $this -> view -> htmlLink(array(
            'route' => 'default',
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject -> getType(),
            'id' => $subject -> getIdentity(),
            'format' => 'smoothbox',
        ), '<span class="ynicon yn-share"></span>'.$this->view->translate('Share group'), array(
            'class' => 'buttonlink smoothbox',
        ));

        return $actions;
    }

    public function _getBusinessActions($subject, $viewer = null)
    {
        $actions = array();

        if ($viewer == null)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!is_object($viewer) || $viewer -> getIdentity() == 0)
        {
            return $actions;
        }

        $allowInvite = true;

        $package = $subject -> getPackage();

        // invite
        if ($package -> getIdentity() && $package -> allow_user_invite_friend && $subject -> isAllowed('invite'))
        {
            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'invite',
                'business_id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
                'route' => 'ynbusinesspages_extended'
            ), '<span class="ynicon yn-envelope-o"></span>'.$this->view->translate('Invite'), array(
                'class' => 'buttonlink smoothbox',
            ));
        }

        if ($package -> getIdentity() && $package -> allow_user_join_business) {

            // membership
            $row = $subject -> membership() -> getRow($viewer);

            // Not yet associated at all
            if (null === $row)
            {
                if ($subject -> membership() -> isResourceApprovalRequired())
                {

                    $actions[] = $this -> view -> htmlLink(array(
                        'controller' => 'member',
                        'action' => 'request',
                        'format' => 'smoothbox',
                        'business_id' => $subject -> getIdentity(),
                        'route' => 'ynbusinesspages_extended'
                    ), '<span class="ynicon yn-envelope"></span>'.$this->view->translate('Request Invite'), array(
                        'class' => 'buttonlink smoothbox',
                    ));

                }
                else
                {

                    $actions[] = $this -> view -> htmlLink(array(
                        'controller' => 'member',
                        'action' => 'join',
                        'format' => 'smoothbox',
                        'business_id' => $subject -> getIdentity(),
                        'route' => 'ynbusinesspages_extended',
                    ), '<span class="ynicon yn-briefcase"></span>'.$this->view->translate('Join business'), array(
                        'class' => 'buttonlink smoothbox active',
                    ));
                }
            }
            elseif ($row -> active)
            {
                if (!$subject -> isOwner($viewer))
                {

                    $actions[] = $this -> view -> htmlLink(array(
                        'controller' => 'member',
                        'action' => 'leave',
                        'business_id' => $subject -> getIdentity(),
                        'format' => 'smoothbox',
                        'route' => 'ynbusinesspages_extended'
                    ), '<span class="ynicon yn-check"></span>'.$this->view->translate('Joined'), array(
                        'class' => 'buttonlink smoothbox',
                    ));
                }
//            else
//            {
//                $actions[] = $this -> view -> htmlLink(array(
//                    'action' => 'delete',
//                    'business_id' => $subject -> getIdentity(),
//                    'format' => 'smoothbox',
//                    'route' => 'ynbusinesspages_specific'
//                ), '<span class="ynicon yn-del"></span>'.$this->view->translate('Delete'), array(
//                    'class' => 'buttonlink smoothbox',
//                ));
//            }
            }
            elseif (!$row -> resource_approved && $row -> user_approved)
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'cancel',
                    'format' => 'smoothbox',
                    'business_id' => $subject -> getIdentity(),
                    'route' => 'ynbusinesspages_extended'
                ), '<span class="ynicon yn-stop"></span>'.$this->view->translate('Cancel request'), array(
                    'class' => 'buttonlink smoothbox',
                ));
            }
            elseif (!$row -> user_approved && $row -> resource_approved)
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'accept',
                    'business_id' => $subject -> getIdentity(),
                    'format' => 'smoothbox',
                    'route' => 'ynbusinesspages_extended'
                ), $this->view->translate('Accept Request'), array(
                    'class' => 'buttonlink smoothbox',
                ));

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'reject',
                    'business_id' => $subject -> getIdentity(),
                    'format' => 'smoothbox',
                    'route' => 'ynbusinesspages_extended'
                ), $this->view->translate('Ignore Request'), array(
                    'class' => 'buttonlink smoothbox',
                ));
            }
        }

        if($package -> getIdentity() && $package -> allow_user_share_business)
        {
            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'default',
                'module' => 'activity',
                'controller' => 'index',
                'action' => 'share',
                'type' => $subject -> getType(),
                'id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
            ), '<span class="ynicon yn-share"></span>'.$this->view->translate('Share business'), array(
                'class' => 'buttonlink smoothbox',
            ));
        }

        return $actions;
    }

    public function _getCompanyActions($subject, $viewer = null)
    {
        $actions = array();

        if ($viewer == null)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!is_object($viewer) || $viewer -> getIdentity() == 0)
        {
            return $actions;
        }

        // Follow
        if (!$viewer -> isSelf($subject -> getOwner()))
        {
            $label = 'Follow';
            $icon = '<span class="ynicon yn-plus"></span>';
            $class = 'active';
            $tableFollow = Engine_Api::_() -> getItemTable('ynjobposting_follow');
            $followRow = $tableFollow -> getFollowBy($subject -> getIdentity(), $viewer -> getIdentity());
            if(isset($followRow) && $followRow -> active == 1)
            {
                $label = 'Following';
                $icon = '<span class="ynicon yn-check"></span>';
                $class = '';
            }

            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'ynjobposting_extended',
                'controller' => 'company',
                'action' => 'follow',
                'id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
            ), $icon.$this->view->translate($label), array(
                'class' => "buttonlink smoothbox $class",
            ));
        }

        return $actions;
    }

    public function _getStoreActions($subject, $viewer = null)
    {

    }

    public function _getListingActions($subject, $viewer = null)
    {

    }

    public function renderUser($username)
    {

        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $model = Engine_Api::_() -> getDbTable('users', 'user');
        $select = $model -> select() -> where('username=?', (string)$username);
        $subject = $model -> fetchRow($select);

        if (!is_object($subject))
        {
            $subject = $model -> find((int)$username) -> current();
        }

        $this -> view -> subject = $subject;

        $actions = $this -> _getUserActions($subject, $viewer);
        $ynMemberActions = $this -> _getYnmemberActions($subject, $viewer);

        // Don't render this if not authorized

        $this -> view -> actions = $actions;
        $this -> view -> ynmember_actions = $ynMemberActions;
        $onlineTable = Engine_Api::_() -> getDbtable('online', 'user');
        $step = 900;
        $select = $onlineTable -> select() -> where('user_id=?', (int)$subject -> getIdentity()) -> where('active > ?', date('Y-m-d H:i:s', time() - $step));
        $online = $onlineTable -> fetchRow($select);
        //echo $select;
        $this -> view -> isSubjectOnline = is_object($online);

        //limit
        return $this -> view -> render('index/render-user.tpl');
    }

    public function renderGroup($groupId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('group', $groupId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

        // share action

        $this -> view -> actions = $actions = $this -> _getGroupActions($subject, $viewer);
        return $this -> view -> render('index/render-group.tpl');
    }

    public function renderEvent($eventId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('event', $eventId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

        $this -> view -> actions = $actions = $this -> _getEventActions($subject, $viewer);
        return $this -> view -> render('index/render-event.tpl');
    }

    public function renderBusiness($itemId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('ynbusinesspages_business', $itemId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> actions = $actions = $this -> _getBusinessActions($subject, $viewer);
        return $this -> view -> render('index/render-business.tpl');
    }

    public function renderCompany($itemId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('ynjobposting_company', $itemId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> actions = $actions = $this -> _getCompanyActions($subject, $viewer);

        return $this -> view -> render('index/render-company.tpl');
    }

    public function renderListing($itemId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('ynlistings_listing', $itemId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

        return $this -> view -> render('index/render-listing.tpl');
    }

    public function renderMultilisting($itemId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('ynmultilisting_listing', $itemId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

        return $this -> view -> render('index/render-multilisting.tpl');
    }

    public function renderStore($itemId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('social_store', $itemId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
//        $this -> view -> actions = $actions = $this -> _getStoreActions($subject, $viewer);

        return $this -> view -> render('index/render-store.tpl');
    }

    public function getJsonDataAction($type, $id)
    {

        $json = array(
            'error' => 0,
            'html' => '',
            'match_type' => $type,
            'match_id' => $id,
            'message' => ''
        );
        try
        {
            $method = 'render' . ucfirst($type);
            if (method_exists($this, $method))
            {
                $json['html'] = $this -> $method($id);
            }
        }
        catch (Exception $e)
        {
            $json['error'] = 1;
            $json['message'] = $e -> getMessage();
            if (APPLICATION_ENV == 'development')
            {
                throw $e;
            }
        }

        return Zend_Json::encode($json);
    }

    public function getCoverPhotoUrl($user) {
        $coverPhotoUrl = "";

        if (Engine_Api::_()->hasModuleBootstrap('ynmember') && !empty($user->cover_id))
        {
            $coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($user->cover_id)->current();
            if($coverFile)
                $coverPhotoUrl = $coverFile->map();
        }

        return $coverPhotoUrl;
    }

    public function getUserFields($subject) {

        $userFields = array();

        $enabledFields = $this -> getUserPopupProfileFields();

        // nothing is enabled
        if (empty($enabledFields))
        {
            return $userFields;
        }
        $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($subject);

        if (count($fieldStructure) <= 1)
        {
            return $userFields;
        }

        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();

        $relationship = 'everyone';
        if( $viewer && $viewer->getIdentity() ) {
            if( $viewer->getIdentity() == $subject->getIdentity() ) {
                $relationship = 'self';
            } else if( $viewer->membership()->isMember($subject, true) ) {
                $relationship = 'friends';
            } else {
                $relationship = 'registered';
            }
        }

        foreach ($fieldStructure as $key => $map)
        {
            // Get field meta object
            $field = $map -> getChild();

            // skip not enabled fields
            if (!in_array($field->field_id, $enabledFields)) {
                continue;
            }

            // check privacy
            $isHidden = !$field->display;

            $value = $field -> getValue($subject);
            // Get first value object for reference

            $firstValue = $value;
            if( is_array($value) && !empty($value) ) {
                $firstValue = $value[0];
            }

            // Evaluate privacy
            if( !empty($firstValue->privacy) && $relationship != 'self' ) {
                if( $firstValue->privacy == 'self' && $relationship != 'self' ) {
                    $isHidden = true; //continue;
                } else if( $firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self') ) {
                    $isHidden = true; //continue;
                } else if( $firstValue->privacy == 'registered' && $relationship == 'everyone' ) {
                    $isHidden = true; //continue;
                }
            }

            if ($isHidden) {
                continue;
            }

            // Normal fields
            $tmp = $this -> getFieldValueString($field, $value, $subject, $map, $fieldStructure);


            if (!empty($tmp))
            {
                $userFields[$field -> type] = $tmp;
            }
        }

        return $userFields;
    }

    public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null)
    {
        $rawValue = array(
            'facebook',
            'twitter',
            'aim',
            'website',
        );
        if ((!is_object($value) || !isset($value -> value)) && !is_array($value))
        {
            return null;
        }

        if (in_array($field->type, $rawValue))
            return $value->value;

        $helperName = Engine_Api::_() -> fields() -> getFieldInfo($field -> type, 'helper');
        if (!$helperName)
        {
            return null;
        }

        $view = Zend_Registry::get('Zend_View');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        $helper = $view -> getHelper($helperName);
        if (!$helper)
        {
            return null;
        }

        $helper -> structure = $partialStructure;
        $helper -> map = $map;
        $helper -> field = $field;
        $helper -> subject = $subject;
        $tmp = $helper -> $helperName($subject, $field, $value);
        unset($helper -> structure);
        unset($helper -> map);
        unset($helper -> field);
        unset($helper -> subject);

        return $tmp;
    }

    public function getUserLivePlaces($subject)
    {
        $livePlaces = array();
        if (Engine_Api::_()->hasModuleBootstrap('ynmember')) {
            $tableLive = Engine_Api::_() -> getItemTable('ynmember_liveplace');
            $currentLivePlace = $tableLive -> getLiveCurrentPlacesByUserId($subject -> getIdentity());
            $pastLivePlaces = $tableLive -> getLivePastPlacesByUserId($subject -> getIdentity());
            foreach ($currentLivePlace as $livePlace) {
                $livePlaces[] = $livePlace->location;
            }
            foreach ($pastLivePlaces as $livePlace) {
                $livePlaces[] = $livePlace->location;
            }
        }

        return $livePlaces;
    }

    public function getUserGroups($subject)
    {
        $groupModule = Engine_Api::_()->hasModuleBootstrap('advgroup') ? 'advgroup' : (Engine_Api::_()->hasModuleBootstrap('group') ? 'group' : '');
        if ($groupModule) {
            $membership = Engine_Api::_()->getDbtable('membership', $groupModule);
            $select = $membership->getMembershipsOfSelect($subject);
            $select->where('group_id IS NOT NULL');

            $paginator = Zend_Paginator::factory($select);
            return $paginator;
        }
        return false;
    }

    public function getUserBusinessPages($subject)
    {
        if (Engine_Api::_()->hasModuleBootstrap('ynbusinesspages')) {
            $membership = Engine_Api::_()->getDbtable('membership', 'ynbusinesspages');
            $select = $membership->getMembershipsOfSelect($subject);
            $select->where('business_id IS NOT NULL');

            $paginator = Zend_Paginator::factory($select);
            return $paginator;
        }
        return false;
    }

    public function getStoreFollowCount($storeID = 0)
    {
        if (!$storeID)
            return 0;

        $followTable = Engine_Api::_()->getDbTable('follows', 'socialstore');
        $followTableName = $followTable->info('name');
        $select = $followTable->select()->from($followTableName, array('COUNT(*) AS count'))->where('store_id = ?', $storeID);
        return $select->query()->fetchColumn('count');
    }
}
