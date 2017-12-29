<?php
class Advgroup_Plugin_Menus
{

    public function canSendMessageToOwner()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
	

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Contact"),
			'icon' => 'application/modules/Messages/externals/images/send.png',
			'route' => 'messages_general',
			'params' => array(
				'action' => 'compose',
				'to' => $subject -> getOwner() -> getIdentity(),	
			)
		);
	}
	
	public function canCreateGroups()
	{
		// Must be logged-in
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity())
		{
			return false;
		}

		// Must be able to create events
		if (!Engine_Api::_() -> authorization() -> isAllowed('group', $viewer, 'create'))
		{
			return false;
		}

		return true;
	}

	public function canViewGroups()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Must be able to view groups
		if (!Engine_Api::_() -> authorization() -> isAllowed('group', $viewer, 'view'))
		{
			return false;
		}

		return true;
	}

	public function onMenuInitialize_AdvgroupMainManage()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}

	public function onMenuInitialize_AdvgroupMainCreate()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Must be logged-in
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		// Must be able to create groups
		if (!Engine_Api::_() -> authorization() -> isAllowed('group', null, 'create'))
		{
			return false;
		}

		return true;
	}

	public function onMenuInitialize_AdvgroupManageAnnouncement()
	{
		// Get viewer, group and manage settings
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
	  	$canManage = $subject -> authorization() -> isAllowed(null, 'announcement');
		$allow_manage = "";
		$levelManage = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'announcement');
		if ($canManage && $levelManage) {
			$allow_manage = true;
		} else {
			$allow_manage = false;
		}
		
	
		// Must be a group
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		// Must be logged-in
		if (!$viewer -> getIdentity())
		{
			return false;
		}

		// Checking manage announcement permission
		if ($subject -> is_subgroup)
		{
			if (!$subject -> isParentGroupOwner($viewer) && !$subject -> isOwner($viewer) && !$allow_manage)
			{
				return false;
			}
		}
		else
		if (!$subject -> isOwner($viewer)&& !Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'announcement'))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Manage Announcement"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'announcement',
				'action' => 'manage',
				'group_id' => $subject -> getIdentity(),
			)
		);

	}

	public function onMenuInitialize_AdvgroupProfileEdit()
	{
		// Get viewer, group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		// Must be a group
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		// Checking group edit permission
		if ($subject -> is_subgroup)
		{
			if (!$viewer -> getIdentity() || (!$subject -> isParentGroupOwner($viewer) && !$subject -> authorization() -> isAllowed($viewer, 'edit')))
			{
				return false;
			}
		}
		else
		if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Edit Group Details"),
			'icon' => 'application/modules/Advgroup/externals/images/edit.png',
			'route' => 'group_specific',
			'params' => array(
				'controller' => 'group',
				'action' => 'edit',
				'group_id' => $subject -> getIdentity(),
				'ref' => 'profile'
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileStyle()
	{
		// Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		//Must be a group
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		// Checking style manage permission
		/*
		 if ($subject -> is_subgroup) {
		 if (!$viewer -> getIdentity() || (!$subject -> isParentGroupOwner($viewer) && !$subject -> authorization() ->
		 isAllowed($viewer, 'edit'))) {
		 return false;
		 }
		 }
		 else
		 if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit')) {
		 return false;
		 }
		 */
		if (!$subject -> authorization() -> isAllowed($viewer, 'style'))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Edit Group Style"),
			'class' => 'smoothbox',
			'route' => 'group_specific',
			'params' => array(
				'action' => 'style',
				'group_id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			)
		);
	}

	// Delete for parent group owner
	public function onMenuInitialize_AdvgroupProfileDelete()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
		if (!$viewer -> getIdentity())
		{
			return false;
		}
	
		if (($viewer -> isAdmin() && $subject -> isOwner($viewer)) || ($subject -> is_subgroup && $subject -> isParentGroupOwner($viewer) && $subject -> isOwner($viewer)))
		{
			
			return array(
				'label' => Zend_Registry::get("Zend_Translate")->_("Delete Group"),
				'icon' => 'application/modules/Advgroup/externals/images/delete.png',
				'class' => 'smoothbox',
				'route' => 'group_specific',
				'params' => array(
					'action' => 'delete',
					'group_id' => $subject -> getIdentity()
				),
			);
		}
		return false;
	}

	//Request Tab, Cancel Tab, Accept Tab, Ignore Tab, Join Tab, Leave Tab for user
	//Delete, Reject for owner group
	public function onMenuInitialize_AdvgroupProfileMember()
	{
		// Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		//Must be a group
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		$row = $subject -> membership() -> getRow($viewer);

		// Not yet associated at all
		if (null === $row || $row -> rejected_ignored)
		{
			if ($subject -> is_subgroup)
			{
				$parent_group = $subject -> getParentGroup();

				if ($parent_group -> membership() -> isResourceApprovalRequired())
				{
					return array(
						'label' => Zend_Registry::get("Zend_Translate")->_("Request Membership"),
						'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
						'class' => 'smoothbox',
						'route' => 'group_extended',
						'params' => array(
							'controller' => 'member',
							'action' => 'request',
							'group_id' => $subject -> getIdentity(),
						),
					);
				}
				elseif ($subject -> membership() -> isResourceApprovalRequired())
				{
					return array(
						'label' => Zend_Registry::get("Zend_Translate")->_("Request Membership"),
						'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
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
						'label' => Zend_Registry::get("Zend_Translate")->_("Join Group"),
						'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
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
			else
			{

				if ($subject -> membership() -> isResourceApprovalRequired())
				{

					return array(
						'label' => Zend_Registry::get("Zend_Translate")->_("Request Membership"),
						'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
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
						'label' => Zend_Registry::get("Zend_Translate")->_("Join Group"),
						'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
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
		}

		// Full member
		// @todo consider owner
		else
		if ($row -> active)
		{
			if (!$subject -> isOwner($viewer) && !$subject -> isParentGroupOwner($viewer))
			{
				return array(
					'label' => Zend_Registry::get("Zend_Translate")->_("Leave Group"),
					'icon' => 'application/modules/Advgroup/externals/images/member/leave.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'leave',
						'group_id' => $subject -> getIdentity()
					),
				);
			}
			/*
			else
						{
							return array(
								'label' => Zend_Registry::get("Zend_Translate")->_("Delete Group"),
								'icon' => 'application/modules/Advgroup/externals/images/delete.png',
								'class' => 'smoothbox',
								'route' => 'group_specific',
								'params' => array(
									'action' => 'delete',
									'group_id' => $subject -> getIdentity()
								),
							);
						}*/
			
		}
		
else
		if (!$row -> resource_approved && $row -> user_approved)
		{
			return array(
				'label' => Zend_Registry::get("Zend_Translate")->_("Cancel Membership Request"),
				'icon' => 'application/modules/Advgroup/externals/images/member/cancel.png',
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
					'label' => Zend_Registry::get("Zend_Translate")->_("Accept Membership Request"),
					'icon' => 'application/modules/Advgroup/externals/images/member/accept.png',
					'class' => 'smoothbox',
					'route' => 'group_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'accept',
						'group_id' => $subject -> getIdentity()
					),
				),
				array(
					'label' => Zend_Registry::get("Zend_Translate")->_("Ignore Membership Request"),
					'icon' => 'application/modules/Advgroup/externals/images/member/reject.png',
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
			throw new Advgroup_Model_Exception('Wow, something really strange happened.');
		}

		return false;
	}

	//Report Tab
	public function onMenuInitialize_AdvgroupProfileReport()
	{
		// Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		//Must be a group
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Report Group"),
			'class' => 'smoothbox ynicon yn-warning-circle',
			'route' => 'default',
			'params' => array(
				'module' => 'core',
				'controller' => 'report',
				'action' => 'create',
				'subject' => $subject->getGuid(),	
				'format' => 'smoothbox',
			),
		);
		
	}

	//Invite Friends Tab
	public function onMenuInitialize_AdvgroupProfileInvite()
	{
		// Get group and viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		// Must be a group
		if ($subject -> getType() !== 'group')
		{
			throw new Group_Model_Exception('Whoops, not a group!');
		}

		// Checking invite permission
		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "invite"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "invite"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "invite"))
		{
			return false;
		}
		$class = 'smoothbox';
		$format = 'smoothbox';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';
			$format = '';
		}
		return array(
		    'label' => Zend_Registry::get("Zend_Translate")->_("Invite Member"),
			'icon' => 'application/modules/Advgroup/externals/images/member/invite.png',
			'class' => $class,
			'route' => 'group_extended',
			'params' => array(
				//'module' => 'group',
				'controller' => 'member',
				'action' => 'invite',
				'group_id' => $subject -> getIdentity(),
				'format' => $format,
			),
		);
	}

	//Invite All Users Tab
	public function onMenuInitialize_AdvgroupProfileInviteAll()
	{
		return false;
	}

	// Invite Management Tab
	public function onMenuInitialize_AdvgroupProfileInviteManage()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		//check auth if can manage
	  	$canManage = $subject -> authorization() -> isAllowed(null, 'invitation');
		$allow_manage = "";
		$levelManage = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'invitation');
		if ($canManage && $levelManage) {
			$allow_manage = true;
		} else {
			$allow_manage = false;
		}
		
		//$allow_manage = Engine_Api::_() -> authorization() -> getAdapter("levels") -> getAllowed('group', $viewer, 'invitation');

		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		if ($subject -> is_subgroup)
		{
			if (!$subject -> isParentGroupOwner($viewer) && !$subject -> isOwner($viewer) && !$allow_manage)
			{
				return false;
			}
		}
		else
		if (!$subject -> isOwner($viewer) && !$allow_manage)
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Invitations Management"),
			'icon' => 'application/modules/Advgroup/externals/images/member/invite.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'invite-manage',
				'action' => 'manage',
				'group_id' => $subject -> getIdentity(),
			)
		);
	}

	//Share Tab
	public function onMenuInitialize_AdvgroupProfileShare()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Share Group"),
			'icon' => 'application/modules/Advgroup/externals/images/share.png',
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
	
	
	public function onMenuInitialize_AdvgroupProfileMessage()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if (!$viewer -> getIdentity() || !$subject -> membership() -> isMember($viewer))
		{
			
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Message Members"),
			'icon' => 'application/modules/Messages/externals/images/send.png',
			'route' => 'messages_general',
			'params' => array(
				'action' => 'compose',
				'to' => $subject -> getIdentity(),
				'multi' => 'advgroup'
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileInvitenew()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			return false;
		}

		if (!$subject -> authorization() -> isAllowed($viewer, "invite"))
		{
			return false;
		}
		$class = 'smoothbox';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';
		}
		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Invite by email"),
			'icon' => 'application/modules/Advgroup/externals/images/member/invite.png',
			'route' => 'group_extended',
			'class' => $class,
			'params' => array(
				//'module' => 'group',
				'controller' => 'invite',
				'action' => 'invite',
				'group_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_AdvgroupProfileMusic()
	{
		
		$music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('music');
		
		if (!$music_enable)
		{
			
			return false;
		
		}
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Musics"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'music',
				'type' => 'music',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}
	
	public function onMenuInitialize_AdvgroupProfileMp3Music()
	{
		
	
		$mp3music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('mp3music');
		
		
		if (!$mp3music_enable)
		{
			return false;
		}
		
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Mp3 Musics"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'music',
				'type' => 'mp3music',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}
	
	public function onMenuInitialize_AdvgroupProfileAlbum()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Albums"),
			'icon' => 'application/modules/Advgroup/externals/images/photo/view.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'album',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileDiscussion()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Discussions"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'topic',
				'action' => 'index',
				'subject' => $subject -> getGuid(),
			)
		);
	}
	public function onMenuInitialize_AdvgroupProfilePoll()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Polls"),
			'icon' => 'application/modules/Advgroup/externals/images/poll/poll.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'poll',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileVideo()
	{
		//Check ynvideo plugin is installed and enabled
		$video_enable = Engine_Api::_() -> hasItemType('video');
		if (!$video_enable)
			return false;

		//Get viewer and subject
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		//Group privacy checking
		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Videos"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'video',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileUsefulLink()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			if (!$subject -> isOwner($viewer) && !$subject -> isParentGroupOwner($viewer))
			{
				return false;
			}
		}
		else
		if (!$subject -> isOwner($viewer))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Useful Links"),
			'icon' => 'application/modules/Advgroup/externals/images/edit.png',
			'route' => 'group_link',
			'params' => array(
				'action' => 'manage',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileCreateSubGroup()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		if ($subject -> is_subgroup)
		{
			return false;
		}
        

        if (!$subject -> authorization() -> isAllowed($viewer, "view"))
        {
            return false;
        }

		if (!$subject -> authorization() -> isAllowed(null, 'sub_group'))
		{
			return false;
		}
		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Create Sub Group"),
			'icon' => 'application/modules/Advgroup/externals/images/create.png',
			'route' => 'group_general',
			'params' => array(
				'action' => 'create',
				'parent' => $subject -> getIdentity(),
			)
		);

	}

	public function onMenuInitialize_AdvgroupProfileTransfer()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
		if (!$viewer -> isAdmin() && !$subject -> isOwner($viewer) && !$subject -> isParentGroupOwner($viewer))
		{
			return false;
		}
		$class = 'smoothbox';

		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';

		}
		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Transfer Owner"),
			'icon' => 'application/modules/Advgroup/externals/images/member/join.png',
			'route' => 'group_specific',
			'class' => $class,
			'params' => array(
				'action' => 'transfer',
				'group_id' => $subject -> getIdentity(),
			),
		);
	}

	public function onMenuInitialize_AdvgroupProfileWiki()
	{
		//Check ynvideo plugin is installed and enabled
		$wiki_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynwiki');
		if (!$wiki_enable)
			return false;

		//Get viewer and subject
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		//Group privacy checking
		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}
		//generate Group Wiki Menu
		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Wikis"),
			'icon' => 'application/modules/Advgroup/externals/images/wiki/page-icon.png',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'wiki',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileActivity()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
		if ($subject -> is_subgroup)
		{
			if (!$viewer -> getIdentity() || (!$subject -> isParentGroupOwner($viewer) && !$subject -> authorization() -> isAllowed($viewer, 'edit')))
			{
				return false;
			}
		}
		else
		{
			if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit'))
			{
				return false;
			}
		}
		$class = 'smoothbox';
		$format = 'smoothbox';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';
			$format = '';
		}
		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Activities"),
			'icon' => 'application/modules/Activity/externals/images/activity/post.png',
			'class' => $class,
			'route' => 'group_activity',
			'params' => array(
				'action' => 'activity',
				'group_id' => $subject -> getIdentity(),
				'format' => $format,
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileInviteContactImport()
	{
		return false;
	}
	
	public function onMenuInitialize_AdvgroupProfileEvent()
	{
		//Check ynvideo plugin is installed and enabled
		$event_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynevent');
		if (!$event_enable)
			return false;

		//Get viewer and subject
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		//Group privacy checking
		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Events"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'event',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}
	
	public function onMenuInitialize_AdvgroupFileSharing()
	{
		
		//Check ynvideo plugin is installed and enabled
		$file_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynfilesharing');
		
		if (!$file_enable)
			return false;

		//Get viewer and subject
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group')
		{
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}

		//Group privacy checking
		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
			else
			if (!$subject -> authorization() -> isAllowed($viewer, "view"))
			{
				return false;
			}
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, "view"))
		{
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group File Sharing"),
			'icon' => 'application/modules/Advgroup/externals/images/file/file.gif',
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'file',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}
	
	public function onMenuInitialize_AdvgroupProfileListing() {
        
        $listing_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynlistings');
        
        if (!$listing_enable){
            return false;
        }
    
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = Engine_Api::_() -> core() -> getSubject();
        if ($subject -> getType() !== 'group') {
            throw new Advgroup_Model_Exception('Whoops, not a group!');
        }
        if ($subject -> is_subgroup) {
            $parent_group = $subject -> getParentGroup();
            if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
                return false;
            }
            else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
                return false;
            }
        }
        else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
            return false;
        }

        return array(
            'label' => Zend_Registry::get("Zend_Translate")->_("Group Listings"),
            'icon' => 'application/modules/Advgroup/externals/images/icon-listing.png',
            'route' => 'group_extended',
            'params' => array(
                'controller' => 'listings',
                'action' => 'list',
                'subject' => $subject -> getGuid(),
            )
        );
    }

	public function onMenuInitialize_AdvgroupProfileSocialMusicAlbum() {
        
        $music_enable = Engine_Api::_() -> hasModuleBootstrap('ynmusic');
        
        if (!$music_enable){
            return false;
        }
    
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = Engine_Api::_() -> core() -> getSubject();
        if ($subject -> getType() !== 'group') {
            throw new Advgroup_Model_Exception('Whoops, not a group!');
        }
        if ($subject -> is_subgroup) {
            $parent_group = $subject -> getParentGroup();
            if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
                return false;
            }
            else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
                return false;
            }
        }
        else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
            return false;
        }

        return array(
            'label' => Zend_Registry::get("Zend_Translate")->_("Group Social Music Albums"),
            'route' => 'group_extended',
            'params' => array(
                'controller' => 'social-music',
                'action' => 'list',
                'type' => 'album',
                'subject' => $subject -> getGuid(),
            )
        );
    }

	public function onMenuInitialize_AdvgroupProfileSocialMusicSong() {
        
        $music_enable = Engine_Api::_() -> hasModuleBootstrap('ynmusic');
        
        if (!$music_enable){
            return false;
        }
    
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = Engine_Api::_() -> core() -> getSubject();
        if ($subject -> getType() !== 'group') {
            throw new Advgroup_Model_Exception('Whoops, not a group!');
        }
        if ($subject -> is_subgroup) {
            $parent_group = $subject -> getParentGroup();
            if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
                return false;
            }
            else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
                return false;
            }
        }
        else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
            return false;
        }

        return array(
            'label' => Zend_Registry::get("Zend_Translate")->_("Group Social Music Songs"),
            'route' => 'group_extended',
            'params' => array(
                'controller' => 'social-music',
                'action' => 'list',
                'type' => 'song',
                'subject' => $subject -> getGuid(),
            )
        );
    }

	public function onMenuInitialize_AdvgroupProfileUltimatevideo() {

		$ultimatvideo_enable = Engine_Api::_() -> hasModuleBootstrap('ynultimatevideo');

		if (!$ultimatvideo_enable){
			return false;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'group') {
			throw new Advgroup_Model_Exception('Whoops, not a group!');
		}
		if ($subject -> is_subgroup) {
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
			else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
				return false;
			}
		}
		else if (!$subject -> authorization() -> isAllowed($viewer, "view")) {
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group Ultimate Videos"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'ynultimatevideo',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}

	public function onMenuInitialize_AdvgroupProfileVideochannel()
	{
		if (!Engine_Api::_()->hasModuleBootstrap('ynvideochannel')) {
			return false;
		}

		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		if ($subject->is_subgroup) {
			$parent_group = $subject->getParentGroup();
			if (!$parent_group->authoziration()->isAllowed($viewer, 'view')) {
				return false;
			}
			else if (!$subject->authorization()->isAllowed($viewer, 'view')) {
				return false;
			}
		}
		else if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return false;
		}

		return array(
			'label' => Zend_Registry::get("Zend_Translate")->_("Group VideoChannel Videos"),
			'route' => 'group_extended',
			'params' => array(
				'controller' => 'ynvideochannel',
				'action' => 'list',
				'subject' => $subject -> getGuid(),
			)
		);
	}
}
