<?php
class Advgroup_MemberController extends Core_Controller_Action_Standard
{
	public function init()
	{
		
		if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
		{
			Engine_Api::_() -> core() -> setSubject($group);
		}
		$this->_helper->requireUser();
    	$this->_helper->requireSubject('group');
	}

	public function addtoblacklistAction()
	 {
	 	$params = $this -> _getAllParams();
		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> form = $form = new Advgroup_Form_Member_AddBlackList( array(
			'group' => $group -> getIdentity()
		));

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$subject = Engine_Api::_() -> core() -> getSubject();

	 	// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;


 	    $table = Engine_Api::_()->getItemTable('advgroup_blacklist');
		$db = Engine_Api::_() -> getDbtable('blacklists', 'advgroup') -> getAdapter();

			$db -> beginTransaction();
			try
			{
					$users = explode(',', $params['memberIds']);
					foreach ($users as $user)
					{
						$user = Engine_Api::_() -> getItem('user', $user);

						if (!$user || !$user -> getIdentity())
						{
							continue;
						}
						if (!$group -> membership() -> isMember($user))
						{
							continue;
						}
						$this->remove($user -> getIdentity());
						$row = $table -> createRow();
						$row -> setFromArray(array(
									       'group_id' => $subject->getIdentity(),
									       'user_id' => $user -> getIdentity(),
									       'creation_date' => date('Y-m-d H:i:s'),
									       'modified_date' => date('Y-m-d H:i:s'),
									       ));
						$row -> save();
					}
					$db -> commit();
					return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Added BlackList')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
					));
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

 	}

	public function removefromblacklistAction()
   {
   		$params = $this -> _getAllParams();
		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> form = $form = new Advgroup_Form_Member_RemoveBlackList( array(
			'group' => $group -> getIdentity()
		));

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$subject = Engine_Api::_() -> core() -> getSubject();

		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

 	    $table = Engine_Api::_()->getItemTable('advgroup_blacklist');
		$tableName = $table->info('name');
		$db = Engine_Api::_() -> getDbtable('blacklists', 'advgroup') -> getAdapter();

			$db -> beginTransaction();
			try
			{
				$users = explode(',', $params['memberIds']);
				foreach ($users as $user)
				{
					$db->delete($tableName, array(
					    'group_id = ?' => $subject->getIdentity(),
					    'user_id = ?' => $user
					));
				}
				$db -> commit();
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Removed BlackList')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
					));
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
   }

	public function addtosubgroupAction()
	{
		$params = $this -> _getAllParams();

		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> form = $form = new Advgroup_Form_Member_SubGroup( array(
			'group' => $group -> getIdentity()
		));

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();


		$subgroup = Engine_Api::_() -> getItem('group', $params['subgroup']);

			$owner = $subject -> getOwner();
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$users = explode(',', $params['memberIds']);
			$db -> beginTransaction();
				try
				{
					foreach ($users as $user)
					{
						$user = Engine_Api::_() -> getItem('user', $user);

						if (!$user || !$user -> getIdentity())
						{
							continue;
						}
						if (!$group -> membership() -> isMember($user))
						{
							continue;
						}
						if ($subgroup -> membership() -> isMember($user))
						{
							continue;
						}
						else {
							$subgroup -> membership() -> addMember($user) -> setUserApproved($user);
							Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($owner, $user, $subgroup, 'advgroup_approve');
						}

					}
					$db -> commit();
					return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Add member(s) to subgroup successfully')),
						'layout' => 'default-simple',
						'parentRefresh' => true,
					));
				}
				catch( Exception $e )
				{
					$db -> rollBack();

				}
	}

	// user join in group in public group case
	public function joinAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_Join();

		// If member is already part of the group
		if ($subject -> membership() -> isMember($viewer, true))
		{

			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				// Set the request as handled
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'advgroup_invite');
				if ($notification)
				{
					$notification -> mitigated = true;
					$notification -> save();
				}
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You are already a member of this group.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}

		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> membership() -> isMember($viewer, 1))
			{
				$message = Zend_Registry::get('Zend_Translate') -> _("You must be a member of group") . " <a href='" . $parent_group -> getHref() . "' target ='_top'>" . $parent_group -> getTitle() . "</a> " . Zend_Registry::get('Zend_Translate') -> _("before you can join this group.");
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array($message),
					'layout' => 'default-simple',
					//        'parentRefresh' => true,
				));
			}
		}

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				if ($subject -> membership() -> isMember($viewer))
				{
					$subject -> membership() -> setUserApproved($viewer);
				}
				else
				{
					$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
				}
				// Set the request as handled
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'advgroup_invite');
				if ($notification)
				{
					$notification -> mitigated = true;
					$notification -> save();
				}

				// Add activity
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activityApi -> addActivity($viewer, $subject, 'advgroup_join');

				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You are now a member of this group.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	// request join group for user in private group case
	public function requestAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_Request();

		if ($subject -> is_subgroup)
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> membership() -> isMember($viewer, 1))
			{
				$message = Zend_Registry::get('Zend_Translate') -> _("You must be a member of group.") . " <a href='" . $parent_group -> getHref() . "' target ='_top'>" . $parent_group -> getTitle() . "</a> " . Zend_Registry::get('Zend_Translate') -> _("before you can join this group.");
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array($message),
					'layout' => 'default-simple',
					//        'parentRefresh' => true,
				));
			}
		}

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$owner = $subject -> getOwner();
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				if ($subject -> membership() -> isMember($viewer))
				{
					$subject -> membership() -> requestAgain($viewer);
				}
				else
				{
					$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
				}

				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($owner, $viewer, $subject, 'advgroup_approve');
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Group membership request sent')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	//cancel invitation for user in private group case
	public function cancelAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_Cancel();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$subject -> membership() -> removeMember($viewer);

				// Remove the notification?
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($subject -> getOwner(), $subject, 'advgroup_approve');
				if ($notification)
				{
					$notification -> delete();
				}

				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Group membership request cancelled.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	//leave group
	public function leaveAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> isOwner($viewer))
			return;

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_Leave();

		if (!$subject -> is_subgroup && count($subject -> getAllSubGroups()) > 0)
		{
			$form -> setDescription('Are you sure you want to leave this group? You will leave all it\'s sub-groups too.');
			if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
			{
				$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
				$db -> beginTransaction();
				try
				{
					//Remove membership in sub-groups
					foreach ($subject -> getAllSubGroups() as $group)
					{
						//Transfer owner to parent group owner if user is owner of a sub-group
						if ($group -> isOwner($viewer))
						{
							$parent_owner = $subject -> getOwner();

							if (!$group -> membership() -> isMember($parent_owner, 1))
							{
								$group -> membership() -> addMember($parent_owner) -> setUserApproved($parent_owner);
							}

							$group -> user_id = $parent_owner -> getIdentity();

							$group -> save();
						}
						if ($group -> membership() -> isMember($viewer))
						{
							$list = $group -> getOfficerList();
							// remove from officer list
							$list -> remove($viewer);
							$group -> membership() -> removeMember($viewer);
						}
					}

					//Remove membership in parent-groups
					$list = $subject -> getOfficerList();
					// remove from officer list
					$list -> remove($viewer);
					$subject -> membership() -> removeMember($viewer);
					$db -> commit();
				}
				catch( Exception $e )
				{
					$db -> rollBack();
					throw $e;
				}
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have successfully left this group.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}
		else
		{
			// Process form
			if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
			{
				$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
				$db -> beginTransaction();

				try
				{
					$list = $subject -> getOfficerList();
					// remove from officer list
					$list -> remove($viewer);

					$subject -> membership() -> removeMember($viewer);
					$db -> commit();
				}
				catch( Exception $e )
				{
					$db -> rollBack();
					throw $e;
				}
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have successfully left this group.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}
	}

	//accept invitation
	public function acceptAction()
	{

		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_Accept();

		// Process form
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Method');
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Data');
			return;
		}

		// Process
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$subject -> membership() -> setUserApproved($viewer);

			// Set the request as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'advgroup_invite');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> save();
			}

			// Add activity
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($viewer, $subject, 'advgroup_join');

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> error = false;

		$message = Zend_Registry::get('Zend_Translate') -> _('You have accepted the invite to the group %s');
		$message = sprintf($message, $subject -> __toString());
		$this -> view -> message = $message;


		if (null === $this -> _helper -> contextSwitch -> getCurrentContext())
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array($message),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}

	}

	//reject invitation
	public function rejectAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;
		// Get user
		if ((0 === ($user_id = (int) $this->_getParam('user_id')) ||
        	null === ($user = Engine_Api::_()->getItem('user', $user_id)))
		&& !Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if(!$user)
		{
			$user = Engine_Api::_() -> user() -> getViewer();
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_Reject();

		// Process form
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Method');
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Data');
			return;
		}

		// Process
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			//$subject -> membership() -> removeMember($user);
			$subject -> membership() -> rejectedInvite($user);
			// Set the request as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($user, $subject, 'advgroup_invite');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> save();
			}

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> error = false;
		$message = Zend_Registry::get('Zend_Translate') -> _('You have ignored the invite to the group %s');
		$message = sprintf($message, $subject -> __toString());
		$this -> view -> message = $message;

		if (null === $this -> _helper -> contextSwitch -> getCurrentContext())
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array($message),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}

	}

	//promote user
	public function promoteAction()
	{
		$params = $this -> _getAllParams();
		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> form = $form = new Advgroup_Form_Member_Promote( array(
			'group' => $group -> getIdentity()
		));

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$table = $list -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$users = explode(',', $params['memberIds']);
			foreach ($users as $user)
			{
				$user = Engine_Api::_() -> getItem('user', $user);

				if (!$user || !$user -> getIdentity())
				{
					continue;
				}
				if (!$group -> membership() -> isMember($user))
				{
					continue;
				}
				$list -> add($user);
				// Add notification
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($user, $viewer, $group, 'advgroup_promote');

				// Add activity
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activityApi -> addActivity($user, $group, 'advgroup_promote');
			}
			$db -> commit();
			return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Promoted')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
			));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			$this -> view -> error_msg = $this -> view -> translate("Promote unsuccessfully");
			return;
		}


	}

	//demote user
	public function demoteAction()
	{
		$params = $this -> _getAllParams();
		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> form = $form = new Advgroup_Form_Member_Demote( array(
			'group' => $group -> getIdentity()
		));

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

			$table = $list -> getTable();
			$db = $table -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$users = explode(',', $params['memberIds']);
				foreach ($users as $user)
				{
					// Get user
					$user = Engine_Api::_() -> getItem('user', $user);
					if (!$user || !$user -> getIdentity())
					{
						continue;
					}

					if (!$group -> membership() -> isMember($user))
					{
						continue;
					}
					$list -> remove($user);

				}
				$db -> commit();
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Demoted')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
					));
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				$this -> view -> error_msg = $this -> view -> translate("Demote unsuccessfully");
			}

	}

	public function ajaxRemoveAction()
	{
		$subject = Engine_Api::_() -> core() -> getSubject();
		$list = $subject -> getOfficerList();
		$user_id = (int)$this -> _getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);
		$fsubgroup = (int)$this -> _getParam('ftitle');

		if ($fsubgroup == 1)
		{
			$sub_groups = $subject -> getAllSubGroups();

			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();
			try
			{
				//Remove membership in sub-groups
				foreach ($sub_groups as $group)
				{
					//Transfer owner to parent group owner if user is owner of a sub-group
					if ($group -> isOwner($user))
					{
						$parent_owner = $subject -> getOwner();

						if (!$group -> membership() -> isMember($parent_owner, 1))
						{
							$group -> membership() -> addMember($parent_owner) -> setUserApproved($parent_owner);
						}

						$group -> user_id = $parent_owner -> getIdentity();
						$group -> save();
					}
					if ($group -> membership() -> isMember($user))
					{
						$list = $group -> getOfficerList();
						// remove from officer list
						$list -> remove($user);
						$group -> membership() -> removeMember($user);
					}
				}

				//Remove membership in parent-groups
				$list = $subject -> getOfficerList();
				// remove from officer list
				$list -> remove($user);
				$subject -> membership() -> removeMember($user);

				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

		}
		else
		{
			// Process form

			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				// Remove as officer first (if necessary)
				$list -> remove($user);

				// Remove membership
				$subject -> membership() -> removeMember($user);

				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
		}

		$content_id = (int)$this -> _getParam('content_id');
		$this -> _forward('index', 'widget', 'core', array('content_id' => $content_id));
	}

	public function ajaxDemoteAction()
	{

		$group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$user_id = (int)$this -> _getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$table = $list -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$list -> remove($user);

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$content_id = (int)$this -> _getParam('content_id');
		$this -> _forward('index', 'widget', 'core', array('content_id' => $content_id));
	}

	public function ajaxPromoteAction()
	{
		$group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$user_id = (int)$this -> _getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$table = $list -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$list -> add($user);

			// Add notification
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($user, $viewer, $group, 'advgroup_promote');

			// Add activity
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($user, $group, 'advgroup_promote');

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$content_id = (int)$this -> _getParam('content_id');
		$this -> _forward('index', 'widget', 'core', array('content_id' => $content_id));

	}

	// remove member from group
	public function removeAction()
	{	$params = $this -> _getAllParams();
		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$subject = $group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();


		// Check auth

		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

			$sub_groups = $subject -> getAllSubGroups();

			if (count($sub_groups) > 0)
			{
				$title = 1;
				//$title = "";
			}
			else
			{
				$title = 2;
				//$title = "";
			}

			// Make form
			$this -> view -> form = $form = new Advgroup_Form_Member_Remove( array(
				'group' => $subject -> getIdentity(),
				'ftitle' => $title,
			));
			if (count($sub_groups) > 0)
			{
				$form -> setDescription("Are you sure you want to remove this member from the group? It will remove this member from all it 's sub-groups too.");
			}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
	$users = explode(',', $params['memberIds']);
	$i = 0;
	foreach ($users as $user)
	{
		//print_r($user);die();
		$user = Engine_Api::_() -> getItem('user', $user);
		$i++;

		if (!$user || !$user -> getIdentity())
		{
					continue;
		}
		if (!$subject -> membership() -> isMember($user))
		{
					continue;
		}

		if (count($sub_groups) > 0)
		{
				$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
				$db -> beginTransaction();
				try
				{
						//Remove membership in sub-groups
						foreach ($sub_groups as $group)
						{
							//Transfer owner to parent group owner if user is owner of a sub-group
							if ($group -> isOwner($user))
							{
								$parent_owner = $subject -> getOwner();

								if (!$group -> membership() -> isMember($parent_owner, 1))
								{
									$group -> membership() -> addMember($parent_owner) -> setUserApproved($parent_owner);
								}

								$group -> user_id = $parent_owner -> getIdentity();
								$group -> save();
							}
							if ($group -> membership() -> isMember($user))
							{
								$list = $group -> getOfficerList();
								// remove from officer list
								$list -> remove($user);
								$group -> membership() -> removeMember($user);
							}
						}

						//Remove membership in parent-groups
						$list = $subject -> getOfficerList();
						// remove from officer list
						$list -> remove($user);
						$subject -> membership() -> removeMember($user);

					$db -> commit();
				}
				catch( Exception $e )
				{
					$db -> rollBack();

					throw $e;
				}

			}
		else
		{
			// Process form

				$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
				$db -> beginTransaction();
				try
				{
						// Remove as officer first (if necessary)
						$list -> remove($user);

						// Remove membership
						$subject -> membership() -> removeMember($user);
						$db -> commit();

				}
				catch( Exception $e )
				{
					$db -> rollBack();
					throw $e;
				}



		}
	}

					return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Group member removed.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
					));

	}

	//invite friends of group owner
	public function inviteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;
		// @todo auth

		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> friends = $friends = $this -> getUserFriends($viewer);
		$this -> view -> users = $users = $this -> getMembersAll($viewer);

		// Prepare form
		$this -> view -> friend_form = $friend_form = new Advgroup_Form_Invite();
		$this -> view -> user_form = $user_form = new Advgroup_Form_Allinvite();

		// Do not allow viewer to sellect all invite if they are not admins
		if (!$viewer -> isAdmin())
		{
			$user_form -> removeElement('all');
		}

		$this -> view -> friend_count = $friend_count = 0;
		$this -> view -> user_count = $user_count = 0;

		if ($group -> is_subgroup)
		{
			$parent_group = $group -> getParentGroup();
			$this -> view -> users = $users = $parent_group -> membership() -> getMembers();

			foreach ($friends as $friend)
			{
				if (!$parent_group -> membership() -> isMember($friend, 1))
					continue;
				if (!$group -> membership() -> ignoredInvite($friend, 1))
					continue;
				$friend_form -> friends -> addMultiOption($friend -> getIdentity(), $friend -> getTitle());
				$friend_count++;
			}

			foreach ($users as $user)
			{
				if (!$parent_group -> membership() -> isMember($user, 1))
				{
					continue;
				}

				if (!$group -> membership() -> ignoredInvite($user, 1))
					continue;
				$user_form -> users -> addMultiOption($user -> getIdentity(), $user -> getTitle());
				$user_count++;

			}

		}
		else
		{
			$this -> view -> users = $users = $this -> getMembersAll($viewer);

			foreach ($friends as $friend)
			{
				if (!$group -> membership() -> ignoredInvite($friend, 1))
					continue;
				$friend_form -> friends -> addMultiOption($friend -> getIdentity(), $friend -> getTitle());
				$friend_count++;
			}
			foreach ($users as $user)
			{
				if (!$group -> membership() -> ignoredInvite($user, 1))
					continue;
				$user_form -> users -> addMultiOption($user -> getIdentity(), $user -> getTitle());
				$user_count++;
			}
		}
		$session = new Zend_Session_Namespace('mobile');
		// throw notice if count = 0
		if ($friend_count == 0 && $user_count == 0)
		{

			if ($session -> mobile)
			{
				$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => $callbackUrl,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no members you can invite.'))
				));
			}
			else
			{
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no members you can invite.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}

		}

		$this -> view -> friend_count = $friend_count;
		$this -> view -> user_count = $user_count;

		// Not posting
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		// Process
		$table = $group -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$friendsIds = $this -> getRequest() -> getPost('friends');
			$usersIds = $this -> getRequest() -> getPost('users');

			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			if (!empty($friendsIds))
			{
				$friends = Engine_Api::_() -> getItemMulti('user', $friendsIds);
				foreach ($friends as $friend)
				{
					if ($group -> membership() -> isMember($friend, null))
					{
						$group -> membership() -> setReinvite($friend);
					}
					else
					{
						$group -> membership() -> addMember($friend) -> setResourceApproved($friend);
					}
					$notifyApi -> addNotification($friend, $viewer, $group, 'advgroup_invite');
				}
			}
			if (!empty($usersIds))
			{
				$users = Engine_Api::_() -> getItemMulti('user', $usersIds);
				foreach ($users as $user)
				{
					if ($group -> membership() -> isMember($user, null))
					{
						$group -> membership() -> setReinvite($user);
					}
					else
					{
						$group -> membership() -> addMember($user) -> setResourceApproved($user);
					}
					$notifyApi -> addNotification($user, $viewer, $group, 'advgroup_invite');
				}
			}
			$db -> commit();
			if (!empty($friendsIds) || !empty($usersIds))
			{
				$session = new Zend_Session_Namespace('mobile');
				if ($session -> mobile)
				{
					$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
					$this -> _forward('success', 'utility', 'core', array(
						'smoothboxClose' => true,
						'parentRedirect' => $callbackUrl,
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited'))
					));
				}
				else
				{
					return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited')),
						'layout' => 'default-simple',
						'parentRefresh' => true,
					));
				}
			}
			else
			{
				if ($session -> mobile)
				{
					$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
					$this -> _forward('success', 'utility', 'core', array(
						'smoothboxClose' => true,
						'parentRedirect' => $callbackUrl,
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('No members invited'))
					));
				}
				else
				{
					return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('No members invited')),
						'layout' => 'default-simple',
						'parentRefresh' => true,
					));
				}
			}
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

	}

	//cancel invite for group owner
	public function cancelInviteAction()
	{

		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$params = $this -> _getAllParams();
		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$subject = $group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(isset($params['reject']))
		{
			$this -> view -> form = $form = new Advgroup_Form_Member_Reject( array(
				'group' => $group -> getIdentity()
			));
		}
		else {
			$this -> view -> form = $form = new Advgroup_Form_Member_CancelInvite( array(
				'group' => $group -> getIdentity()
			));
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$owner = $subject -> getOwner();


		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$users = explode(',', $params['memberIds']);
				foreach ($users as $user)
				{
					$user = Engine_Api::_() -> getItem('user', $user);

					if (!$user || !$user -> getIdentity())
					{
						continue;
					}
					if (!$group -> membership() -> isMember($user))
					{
						continue;
					}
				// Remove membership
				$subject -> membership() -> removeMember($user);
				if ($subject -> membership() -> isResourceApprovalRequired())
				{
					Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $group, $owner, 'advgroup_cancel_invite', array('group_title' => $group -> getTitle()));
				}
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($user, $subject, 'advgroup_invite');
				if ($notification)
				{
					$notification -> mitigated = true;
					$notification -> save();
				}
				}
				$db -> commit();
				if(isset($params['reject']))
				{
					return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Reject member successfully.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,));
				}
				else {
					return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Group invite canceled.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,));
				}

			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}


		}



	}

	public function ajaxCancelInviteAction()
	{

		$subject = $group = Engine_Api::_() -> core() -> getSubject();
		$owner = $subject -> getOwner();
		$user_id = (int)$this -> _getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);

		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Remove membership
			$subject -> membership() -> removeMember($user);
			if ($subject -> membership() -> isResourceApprovalRequired())
			{
				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $group, $owner, 'advgroup_cancel_invite', array('group_title' => $group -> getTitle()));
			}
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($user, $subject, 'advgroup_invite');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> save();
			}

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$content_id = (int)$this -> _getParam('content_id');
		$this -> _forward('index', 'widget', 'core', array('content_id' => $content_id));

		//return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate')
		// -> _('Group invite canceled.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));

	}

	//approve user for group owner
	public function approveAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;


		$params = $this -> _getAllParams();

		if(empty($params['memberIds']))
		{
			$this -> view -> error_msg = $this -> view -> translate("Please select at least one member");
			return;
		}

		$subject = $group = Engine_Api::_() -> core() -> getSubject();
		$list = $group -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> form = $form = new Advgroup_Form_Member_Approve( array(
			'group' => $subject -> getIdentity(),
		));

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();

			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$users = explode(',', $params['memberIds']);
				foreach ($users as $user)
				{
					$user = Engine_Api::_() -> getItem('user', $user);

					if (!$user || !$user -> getIdentity())
					{
						continue;
					}
					if (!$group -> membership() -> isMember($user))
					{
						continue;
					}
					$subject -> membership() -> setResourceApproved($user);

					if ($group -> membership() -> isMember($user, true))
					{
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $subject, $viewer, 'advgroup_accepted');

						// Add activity
						$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
						$action = $activityApi -> addActivity($user, $subject, 'advgroup_join');
					}
				}
				$db -> commit();
				return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Group request approved')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
				));

			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
		}

	}

	public function ajaxApproveAction()
	{

		$subject = Engine_Api::_() -> core() -> getSubject();
		$user_id = (int)$this -> _getParam('user_id');
		$user = Engine_Api::_() -> getItem('user', $user_id);

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$subject -> membership() -> setResourceApproved($user);

			Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $subject, $viewer, 'advgroup_accepted');

			// Add activity
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($user, $subject, 'advgroup_join');

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$content_id = (int)$this -> _getParam('content_id');
		$this -> _forward('index', 'widget', 'core', array('content_id' => $content_id));

		//return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate')
		// -> _('Group request approved')), 'layout' => 'default-simple', 'parentRefresh' => true, ));

	}

	public function editAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		// Get user
		if (0 === ($user_id = (int)$this -> _getParam('user_id')) || null === ($user = Engine_Api::_() -> getItem('user', $user_id)))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$group = Engine_Api::_() -> core() -> getSubject('group');
		$memberInfo = $group -> membership() -> getMemberInfo($user);

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_Edit();

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate(array('title' => $memberInfo -> title));
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$db = $group -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$memberInfo -> setFromArray($form -> getValues());
			$memberInfo -> save();

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member title changed')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}

	public function suggestAction()
	{
		$group_id = $this -> _getParam('group_id');
		$group = Engine_Api::_() -> getItem('group', $group_id);
		if (!$group)
		{
			$data = null;
		}
		else
		{
			$table_user = Engine_Api::_() -> getDbtable('users', 'user');
			$table_user_name = $table_user -> info('name');
			$data = array();

			$table = Engine_Api::_() -> getItemTable('group');
			$select = $group -> membership() -> getMembersObjectSelect();

			if (0 < ($limit = (int)$this -> _getParam('limit', 10)))
			{
				$select -> limit($limit);
			}

			if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value'))))
			{
				// fix for SEGROUP-193: Owner can invite himself
				$select -> where('displayname LIKE ?', '%' . $text . '%') -> where("$table_user_name.user_id <> ?", $group -> getOwner() -> getIdentity());
			}

			foreach ($select->getTable()->fetchAll($select) as $friend)
			{
				$data[] = array(
					'type' => 'user',
					'id' => $friend -> getIdentity(),
					'guid' => $friend -> getGuid(),
					'label' => $friend -> getTitle(),
					'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'),
					'url' => $friend -> getHref(),
				);
			}
		}
		return $this -> _helper -> json($data);
	}

	protected function getFriendIds(User_Model_User $user)
	{
		$ids = array(0);
		$friends = $user -> membership() -> getMembers();
		foreach ($user -> membership() -> getMembersInfo() as $row)
		{
			$ids[] = $row -> user_id;
		}
		return $ids;
	}

	protected function getUserFriends(User_Model_User $user, $name = null)
	{
		$friendIds = $this -> getFriendIds($user);
		$user_table = Engine_Api::_() -> getItemTable('user');

		$select = $user_table -> select() -> where('user_id IN (?)', $friendIds) -> where('displayname is NOT NULL') -> where('displayname <> ?', '') -> order('displayname ASC');

		if (!empty($name))
		{
			$select -> where('displayname like ?', "%" . $name . "%");
		}
		return $user_table -> fetchAll($select);
	}

	protected function getMembersNotFriend(User_Model_User $user, $name = null)
	{
		$friendIds = $this -> getFriendIds($user);
		$user_table = Engine_Api::_() -> getItemTable('user');
		$select = $user_table -> select() -> where('user_id NOT IN (?)', $friendIds) -> where('displayname is NOT NULL') -> where('displayname <> ?', '') -> order('displayname ASC') -> limit(50);

		if (!empty($name))
		{
			$select -> where('displayname like ?', "%" . $name . "%");
		}
		return $user_table -> fetchAll($select);
	}

	protected function getMembersAll(User_Model_User $user, $name = null, $group_id = null)
	{
		$user_table = Engine_Api::_() -> getItemTable('user');
		$select = $user_table -> select() -> where('displayname is NOT NULL') -> where('displayname <> ?', '');

		if ($group_id)
		{
			$select -> where('user_id <> ?', $group_id);
		}
		$select -> order('displayname ASC') -> limit(50);

		if (!empty($name))
		{
			$select -> where('displayname like ?', "%" . $name . "%");
		}

		return $user_table -> fetchAll($select);
	}

	public function ajaxAction()
	{
		// Disable layout
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		$text = $this -> _getParam('text');
		$mode = $this -> _getParam('mode');

		$group_id = $this -> _getParam('group_id');
		$group = Engine_Api::_() -> getItem('group', $group_id);

		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($mode == "users")
		{
			$items = $this -> getMembersAll($viewer, $text, $group_id);
		}
		else
		{
			$items = $this -> getUserFriends($viewer, $text);
		}

		$item_arr = array();
		if (count($items) > 0)
		{
			if ($group -> is_subgroup)
			{
				$parent_group = $group -> getParentGroup();
				foreach ($items as $item)
				{
					if (!$parent_group -> membership() -> isMember($item, 1))
						continue;
					if (!$group -> membership() -> ignoredInvite($item, 1))
						continue;
					$item_arr[] = array(
						'id' => $item -> getIdentity(),
						'title' => $item -> getTitle()
					);
				}
			}
			else
			{
				foreach ($items as $item)
				{
					if (!$group -> membership() -> ignoredInvite($item, 1))
						continue;
					$item_arr[] = array(
						'id' => $item -> getIdentity(),
						'title' => $item -> getTitle()
					);
				}

			}
		}
		$this -> view -> rows = $item_arr;
		$this -> view -> total = count($item_arr);
	}

	//cancel invite for group owner
	public function cancelOneinviteAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$subject = $group = Engine_Api::_() -> core() -> getSubject();

		// Get user
		if (0 === ($user_id = (int)$this -> _getParam('user_id')))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (null === ($user = Engine_Api::_() -> getItem('user', $user_id)))
		{

		}

		$owner = $subject -> getOwner();
		if (!$subject -> membership() -> isMember($user))
		{
			throw new Group_Model_Exception('Cannot remove a non-invite');
		}

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Member_CancelInvite( array(
			'group' => $subject -> getIdentity(),
			'user' => $user_id,
		));

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				// Remove membership
				$subject -> membership() -> removeMember($user);
				if ($subject -> membership() -> isResourceApprovalRequired())
				{
					Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $group, $owner, 'advgroup_cancel_invite', array('group_title' => $group -> getTitle()));
				}
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($user, $subject, 'advgroup_invite');
				if ($notification)
				{
					$notification -> mitigated = true;
					$notification -> save();
				}
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Group invite canceled.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}

	}

	public function remove($user_id)
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$user = Engine_Api::_() -> getItem('user', $user_id);

		$subject = Engine_Api::_() -> core() -> getSubject();
		$list = $subject -> getOfficerList();

		if (!$subject -> membership() -> isMember($user))
		{
			throw new Group_Model_Exception('Cannot remove a non-member');
		}

		$sub_groups = $subject -> getAllSubGroups();

		if (count($sub_groups) > 0)
		{
			$title = 1;
			//$title = "";
		}
		else
		{
			$title = 2;
			//$title = "";
		}



		if (count($sub_groups) > 0)
		{

				$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
				$db -> beginTransaction();
				try
				{
					//Remove membership in sub-groups
					foreach ($sub_groups as $group)
					{
						//Transfer owner to parent group owner if user is owner of a sub-group
						if ($group -> isOwner($user))
						{
							$parent_owner = $subject -> getOwner();

							if (!$group -> membership() -> isMember($parent_owner, 1))
							{
								$group -> membership() -> addMember($parent_owner) -> setUserApproved($parent_owner);
							}

							$group -> user_id = $parent_owner -> getIdentity();
							$group -> save();
						}
						if ($group -> membership() -> isMember($user))
						{
							$list = $group -> getOfficerList();
							// remove from officer list
							$list -> remove($user);
							$group -> membership() -> removeMember($user);
						}
					}

					//Remove membership in parent-groups
					$list = $subject -> getOfficerList();
					// remove from officer list
					$list -> remove($user);
					$subject -> membership() -> removeMember($user);

					$db -> commit();
				}
				catch( Exception $e )
				{
					$db -> rollBack();
					throw($e);
				}


		}
		else
		{
				$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
				$db -> beginTransaction();

				try
				{
					// Remove as officer first (if necessary)
					$list -> remove($user);

					// Remove membership
					$subject -> membership() -> removeMember($user);

					$db -> commit();
				}
				catch( Exception $e )
				{
					$db -> rollBack();
					throw($e);
				}


		}

	}
}
