<?php

class Advgroup_Plugin_Signup
{

	public function onUserCreateAfter($payload)
	{
		$user = $payload -> getPayload();
		$session = new Zend_Session_Namespace('invite_nonmembers');
		$inviteTable = Engine_Api::_() -> getDbtable('invites', 'advgroup');
		if ($session -> verified == 1)
		{
			$table = Engine_Api::_() -> getDbtable('users', 'user');
			$select = $table -> select() -> where('user_id = ?', $user -> getIdentity());
			$row = $table -> fetchRow($select);
			$row -> verified = 1;
			$row -> save();
			$user -> verified = 1;
		}
		$codes = array();
		if (!empty($session -> invite_code))
		{
			$codes[] = $session -> invite_code;
		}
		if (!empty($session -> signup_code))
		{
			$codes[] = $session -> signup_code;
		}
		$codes = array_unique($codes);

		$emails = array();
		if (!empty($session -> invite_email))
		{
			$emails[] = $session -> invite_email;
		}
		if (!empty($session -> signup_email))
		{
			$emails[] = $session -> signup_email;
		}
		$emails = array_unique($emails);

		if (empty($codes) && empty($emails))
		{
			return;
		}

		$select = $inviteTable -> select();

		if (!empty($codes))
		{
			$select -> orWhere('code IN(?)', $codes);
		}

		if (!empty($emails))
		{
			$select -> orWhere('recipient IN(?)', $emails);
		}

		$updateInviteIds = array();
		$group_ids = array();
		$invite_userids = array();
		foreach ($inviteTable->fetchAll($select) as $invite)
		{
			if (0 == $invite -> new_user_id)
			{
				$updateInviteIds[] = $invite -> id;
				$group_ids[] = $invite -> group_id;
				$invite_userids[] = $invite -> user_id;
			}
		}
		//        $group_ids = array_unique($group_ids);
		if (!empty($updateInviteIds))
		{
			$inviteTable -> update(array('new_user_id' => $user -> getIdentity(), ), array(
				'id IN(?)' => $updateInviteIds,
				'new_user_id = ?' => 0,
			));
		}
		//add to group
		if (!empty($session -> group_id))
		{
			$group_id = $session -> group_id;
			$viewer = Engine_Api::_() -> getItem('user', $user -> getIdentity());
			$subject = Engine_Api::_() -> getItem('group', $group_id);
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
				$subject -> membership() -> setResourceApproved($viewer);
				// Add activity
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activityApi -> addActivity($viewer, $subject, 'advgroup_join');
				$groupids_handled = array();
				$groupids_handled[] = $group_id;
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				for ($i = 0; $i < count($group_ids); $i++)
				{
					$gid = $group_ids[$i];
					$invite_userid = $invite_userids[$i];
					if (!in_array($gid, $groupids_handled))
					{
						$group = Engine_Api::_() -> getItem('group', $gid);
						if ($group)
						{
							$inviter = Engine_Api::_() -> getItem('user', $invite_userid);
							$group -> membership() -> addMember($viewer) -> setResourceApproved($viewer);
							$notifyApi -> addNotification($viewer, $inviter, $group, 'advgroup_invite');
						}
					}
					$groupids_handled[] = $gid;
				}
				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
		}
		// Clean session
		$session -> unsetAll();
	}

}
