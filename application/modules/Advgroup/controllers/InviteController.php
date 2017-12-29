<?php

class Advgroup_InviteController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
		{
			Engine_Api::_() -> core() -> setSubject($group);
		}

		$this -> _helper -> requireUser();
		$this -> _helper -> requireSubject('group');
	}

	public function inviteAction()
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		if ($settings -> getSetting('user.signup.inviteonly') == 1)
		{
			if (!$this -> _helper -> requireAdmin() -> isValid())
			{
				return;
			}
		}
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;
		$group_id = $this -> _getParam('group_id');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();

		if ($group -> is_subgroup)
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$this -> view -> form = $form = new Advgroup_Form_Inviter();
		$this -> view -> settings = $settings = Engine_Api::_() -> getApi('settings', 'core');

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$allparams = $this -> _getAllParams();
		$csv_upload = @$allparams['csv_upload'];
		if ($csv_upload)
		{
			$api = Engine_Api::_() -> getApi('core', 'Advgroup');

			$import_result = $api -> uploadContactFile(Engine_Api::_() -> user() -> getViewer());

			if ($import_result['is_error'] != 0)
			{
				$this -> view -> ers = $import_result['error_message'];
			}
			else
			{
				$contacts = $import_result['contacts'];
				$recipients = array();
				foreach ($contacts as $email => $name)
				{
					$recipients[] = $email;
				}
				if (!empty($recipients))
				{
					$recipients = implode(",", $recipients);
					$inviteTable = Engine_Api::_() -> getDbtable('invites', 'advgroup');
					$db = $inviteTable -> getAdapter();
					$db -> beginTransaction();
					try
					{
						$emailsSent = $inviteTable -> sendInvites_CSV($viewer, $recipients, $allparams['message'], $group_id);
						$db -> commit();
					}
					catch (Exception $e)
					{
						$db -> rollBack();
						if (APPLICATION_ENV == 'development')
						{
							throw $e;
						}
					}
					$alreadyMembers = array();
					foreach ($emailsSent['alreadyMemberIds'] as $id)
					{
						$already_user = Engine_Api::_() -> getItem('user', $id);

						if ($already_user)
						{
							if ($group -> membership() -> isMember($already_user, 1))
							{
								$alreadyMembers[] = $already_user;
							}
						}
					}
					$this -> view -> already_members = $alreadyMembers;
					$this -> view -> emails_sent = $emailsSent['emailsSent'];
				}
			}
		}
		else
		{
			if (!$form -> isValid($this -> getRequest() -> getPost()))
			{
				return;
			}
			$values = $form -> getValues();
			$inviteTable = Engine_Api::_() -> getDbtable('invites', 'advgroup');
			$db = $inviteTable -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$emailsSent = $inviteTable -> sendUnlimitedInvites($viewer, $values['recipients'], @$values['message'], $group_id);
				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				if (APPLICATION_ENV == 'development')
				{
					throw $e;
				}
			}
			$alreadyMembers = array();
			foreach ($emailsSent['alreadyMemberIds'] as $id)
			{
				$already_user = Engine_Api::_() -> getItem('user', $id);
				if ($already_user)
				{
					if ($group -> membership() -> isMember($already_user, 1))
					{
						$alreadyMembers[] = $already_user;
					}
				}
			}
			$this -> view -> already_members = $alreadyMembers;
			$this -> view -> emails_sent = $emailsSent['emailsSent'];
		}
		return $this -> render('sent');
	}

}
