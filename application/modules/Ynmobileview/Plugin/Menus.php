<?php
class Ynmobileview_Plugin_Menus
{
	public function onMenuInitialize_CoreFooterYnmobile($row)
	{
		$session = new Zend_Session_Namespace('mobile');
		$router = Zend_Controller_Front::getInstance() -> getRouter();

		// Mobile version visible
		if ($session -> mobile)
		{
			$route = array(
				'uri' => $router -> assemble(array()) . '?mobile=0',
				'enabled' => 1,
				'label' => "Full Site"
			);
			// Full site visible
		}
		else
		{
			$route = array(
				'uri' => $router -> assemble(array()) . '?mobile=1',
				'enabled' => 1,
				'label' => "Mobile Site"
			);
		}

		return $route;
	}

	public function onMenuInitialize_YnmobileviewProfileFriend($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		// Not logged in
		if (!$viewer -> getIdentity() || $viewer -> getGuid(false) === $subject -> getGuid(false))
		{
			return false;
		}

		// Check if friendship is allowed in the network
		$eligible = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.eligible', 2);
		if (!$eligible)
		{
			return '';
		}
		// check admin level setting if you can befriend people in your network
		else
		if ($eligible == 1)
		{

			$networkMembershipTable = Engine_Api::_() -> getDbtable('membership', 'network');
			$networkMembershipName = $networkMembershipTable -> info('name');

			$select = new Zend_Db_Select($networkMembershipTable -> getAdapter());
			$select -> from($networkMembershipName, 'user_id') -> join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null) -> where("`{$networkMembershipName}`.user_id = ?", $viewer -> getIdentity()) -> where("`{$networkMembershipName}_2`.user_id = ?", $subject -> getIdentity());

			$data = $select -> query() -> fetch();

			if (empty($data))
			{
				return '';
			}
		}

		// One-way mode
		$direction = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.direction', 1);
		if (!$direction)
		{
			$viewerRow = $viewer -> membership() -> getRow($subject);
			$subjectRow = $subject -> membership() -> getRow($viewer);
			$params = array();

			// Viewer?
			if (null === $subjectRow)
			{
				// Follow
				$params[] = array(
					'label' => 'Follow',
					'icon' => 'application/modules/User/externals/images/friends/add.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'add',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
			else
			if ($subjectRow -> resource_approved == 0)
			{
				// Cancel follow request
				$params[] = array(
					'label' => 'Cancel Follow Request',
					'icon' => 'application/modules/User/externals/images/friends/remove.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'cancel',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
			else
			{
				// Unfollow
				$params[] = array(
					'label' => 'Unfollow',
					'icon' => 'application/modules/User/externals/images/friends/remove.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'remove',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
			// Subject?
			if (null === $viewerRow)
			{
				// Do nothing
			}
			else
			if ($viewerRow -> resource_approved == 0)
			{
				// Approve follow request
				$params[] = array(
					'label' => 'Approve Follow Request',
					'icon' => 'application/modules/User/externals/images/friends/add.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'confirm',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
			else
			{
				// Remove as follower?
				$params[] = array(
					'label' => 'Remove as Follower',
					'icon' => 'application/modules/User/externals/images/friends/remove.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'remove',
						'user_id' => $subject -> getIdentity(),
						'rev' => true,
					),
				);
			}
			if (count($params) == 1)
			{
				return $params[0];
			}
			else
			if (count($params) == 0)
			{
				return false;
			}
			else
			{
				return $params;
			}
		}

		// Two-way mode
		else
		{
			$row = $viewer -> membership() -> getRow($subject);
			if (null === $row)
			{
				// Add
				return array(
					'label' => 'Add to My Friends',
					'icon' => 'application/modules/User/externals/images/friends/add.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'add',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
			else
			if ($row -> user_approved == 0)
			{
				// Cancel request
				return array(
					'label' => 'Cancel Friend Request',
					'icon' => 'application/modules/User/externals/images/friends/remove.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'cancel',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
			else
			if ($row -> resource_approved == 0)
			{
				// Approve request
				return array(
					'label' => 'Approve Friend Request',
					'icon' => 'application/modules/User/externals/images/friends/add.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'confirm',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
			else
			{
				// Remove friend
				return array(
					'label' => 'Remove from Friends',
					'icon' => 'application/modules/User/externals/images/friends/remove.png',
					'class' => 'smoothbox',
					'route' => 'user_extended',
					'params' => array(
						'controller' => 'friends',
						'action' => 'remove',
						'user_id' => $subject -> getIdentity()
					),
				);
			}
		}
	}

	public function onMenuInitialize_YnmobileviewProfileMessage($row)
	{
		// Not logged in
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
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

		return array(
			'label' => "Send Message",
			'icon' => 'application/modules/Messages/externals/images/send.png',
			'route' => 'messages_general',
			'params' => array(
				'action' => 'compose',
				'to' => $subject -> getIdentity()
			),
		);
	}

	public function onMenuInitialize_YnmobileviewGroupJoin($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Group_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		$row = $subject -> membership() -> getRow($viewer);

		// Not yet associated at all
		if (null === $row)
		{
			if ($subject -> membership() -> isResourceApprovalRequired())
			{
				return array(
					'label' => 'Request Membership',
					'icon' => 'application/modules/Group/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'request',
						'group_id' => $subject -> getIdentity(),
					),
				);
			}
			else
			{
				return array(
					'label' => 'Join Group',
					'icon' => 'application/modules/Group/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'join',
						'group_id' => $subject -> getIdentity()
					),
				);
			}
		}

		// Full member
		// @todo consider owner
		else
		if ($row -> active)
		{
			if (!$subject -> isOwner($viewer))
			{
				return array(
					'label' => 'Leave Group',
					'icon' => 'application/modules/Group/externals/images/member/leave.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'leave',
						'group_id' => $subject -> getIdentity()
					),
				);
			}
			else
			{
				return array(
					'label' => 'Delete Group',
					'icon' => 'application/modules/Group/externals/images/delete.png',
					'class' => 'smoothbox',
					'route' => 'group_specific',
					'params' => array(
						'action' => 'delete',
						'group_id' => $subject -> getIdentity()
					),
				);
			}
		}
		
else
		if (!$row -> resource_approved && $row -> user_approved)
		{
			return array(
				'label' => 'Cancel Membership Request',
				'icon' => 'application/modules/Group/externals/images/member/cancel.png',
				'class' => 'smoothbox',
				'route' => 'group_extended',
				'params' => array(
					'controller' => 'member',
					'action' => 'cancel',
					'group_id' => $subject -> getIdentity()
				),
			);
		}
		
else
		if (!$row -> user_approved && $row -> resource_approved)
		{
			return array(
				array(
					'label' => 'Accept Membership Request',
					'icon' => 'application/modules/Group/externals/images/member/accept.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'accept',
						'group_id' => $subject -> getIdentity()
					),
				),
				array(
					'label' => 'Ignore Membership Request',
					'icon' => 'application/modules/Group/externals/images/member/reject.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'reject',
						'group_id' => $subject -> getIdentity()
					),
				)
			);
		}

		else
		{
			throw new Group_Model_Exception('Wow, something really strange happened.');
		}

		return false;
	}

	public function onMenuInitialize_YnmobileviewGroupShare()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Group_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => 'Share Group',
			'icon' => 'application/modules/Group/externals/images/share.png',
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'activity',
				'controller' => 'index',
				'action' => 'share',
				'type' => $subject -> getType(),
				'id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			),
		);
	}

	public function onMenuInitialize_YnmobileviewEventShare()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => 'Share This Event',
			'icon' => 'application/modules/Event/externals/images/share.png',
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'activity',
				'controller' => 'index',
				'action' => 'share',
				'type' => $subject -> getType(),
				'id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			),
		);
	}

	public function onMenuInitialize_YnmobileviewEventJoin()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('Whoops, not a event!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		$row = $subject -> membership() -> getRow($viewer);

		// Not yet associated at all
		if (null === $row)
		{
			if ($subject -> membership() -> isResourceApprovalRequired())
			{
				return array(
					'label' => 'Request Invite',
					'icon' => 'application/modules/Event/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'request',
						'event_id' => $subject -> getIdentity(),
					),
				);
			}
			else
			{
				return array(
					'label' => 'Join Event',
					'icon' => 'application/modules/Event/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'join',
						'event_id' => $subject -> getIdentity()
					),
				);
			}
		}

		// Full member
		// @todo consider owner
		else
		if ($row -> active)
		{
			if (!$subject -> isOwner($viewer))
			{
				return array(
					'label' => 'Leave Event',
					'icon' => 'application/modules/Event/externals/images/member/leave.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'leave',
						'event_id' => $subject -> getIdentity()
					),
				);
			}
		}
		else
		if (!$row -> resource_approved && $row -> user_approved)
		{
			return array(
				'label' => 'Cancel Invite Request',
				'icon' => 'application/modules/Event/externals/images/member/cancel.png',
				'class' => 'smoothbox',
				'route' => 'event_extended',
				'params' => array(
					'controller' => 'member',
					'action' => 'cancel',
					'event_id' => $subject -> getIdentity()
				),
			);
		}
		else
		if (!$row -> user_approved && $row -> resource_approved)
		{
			return array(
				array(
					'label' => 'Accept Event Invite',
					'icon' => 'application/modules/Event/externals/images/member/accept.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'accept',
						'event_id' => $subject -> getIdentity()
					),
				),
				array(
					'label' => 'Ignore Event Invite',
					'icon' => 'application/modules/Event/externals/images/member/reject.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'reject',
						'event_id' => $subject -> getIdentity()
					),
				)
			);
		}

		else
		{
			throw new Event_Model_Exception('An error has occurred.');
		}

		return false;
	}

	public function onMenuInitialize_YnmobileviewEventDelete()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, 'delete'))
		{
			return false;
		}

		return array(
			'label' => 'Delete Event',
			'icon' => 'application/modules/Event/externals/images/delete.png',
			'class' => 'smoothbox',
			'route' => 'event_specific',
			'params' => array(
				'action' => 'delete',
				'event_id' => $subject -> getIdentity(),
			),
		);
	}

	public function onMenuInitialize_YnmobileviewProfileEdit($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		$label = "Edit My Profile";
		if (!$viewer -> isSelf($subject))
		{
			$label = "Edit Member Profile";
		}

		if ($subject -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return array(
				'label' => $label,
				'icon' => 'application/modules/User/externals/images/edit.png',
				'route' => 'user_extended',
				'params' => array(
					'controller' => 'edit',
					'action' => 'profile',
					'id' => ($viewer -> getGuid(false) == $subject -> getGuid(false) ? null : $subject -> getIdentity()),
				)
			);
		}
		return false;
	}

	public function onMenuInitialize_YnmobileviewProfileCover($row)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$label = "Edit Cover";
		if ($viewer -> isSelf($subject))
		{
			return array(
				'label' => $label,
				'icon' => 'application/modules/User/externals/images/edit.png',
				'route' => 'ynmobi_cover',
				'params' => array(
				)
			);
		}
		return false;
	}

}
