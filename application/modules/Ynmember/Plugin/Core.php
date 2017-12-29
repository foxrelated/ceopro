<?php
class Ynmember_Plugin_Core
{
	/**
	 * Hook to earn credits for action user after create an item.
	 */
	public function onItemCreateAfter($event)
	{
		$allowCreate = array(
			'blog_new', 'ynblog_new',
			'video_new', 'ynvideo_video_new',
			'group_create', 'advgroup_create',
			'event_create', 'ynevent_create',
		);
		$allowJoin = array(
			'event_join', 'ynevent_join',
			'group_join', 'advgroup_join',
		);
		
		// GETTING PAYLOAD
		$payload = $event -> getPayload();
		if (!is_object($payload))
		{
			return;
		}

		// CHECKING PAYLOAD TYPE
		if ($payload -> getType() == 'activity_action')
		{
			// GETTING SUBJECT
			$user = $fromUser = Engine_Api::_()->getItem('user', $payload->subject_id);
			if (!$user->getIdentity())
			{
				return;
			}
			
			// CHECKING NUMBER OF GOT NOTIFICATION USERS
			$notificationTbl = Engine_Api::_()->getDbTable('notifications', 'ynmember');
			$receivers = $notificationTbl -> getAllUsers($user);
			if (!count($receivers))
			{
				return;
			}
			
			// CREATE ITEM TYPE
			if (in_array($payload -> type, $allowCreate))
			{
				$object = $payload->getObject();
				if (is_null($object))
				{
					return;
				}
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				foreach ($receivers as $toUser)
				{
					$notifyApi->addNotification($toUser, $fromUser, $object, 'ynmember_notification_create', array(
						'type' => Engine_Api::_()->ynmember()->getItemTitle($payload -> type),
					));
				}
			}
			// STATUS TYPE
			else if ($payload -> type == 'status')
			{
				$object = $payload->getObject();
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				foreach ($receivers as $toUser)
				{
					$notifyApi->addNotification($toUser, $fromUser, $object, 'ynmember_notification_status', array());
				}
			}
			// POST 
			else if ($payload -> type == 'post')
			{
				$object = $payload->getObject();
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				foreach ($receivers as $toUser)
				{
					$notifyApi->addNotification($toUser, $fromUser, $object, 'ynmember_notification_post', array());
				}
			}
			// JOIN TYPE
			else if (in_array($payload -> type, $allowJoin))
			{
				$object = $payload->getObject();
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				$type = str_replace("_join", "", $payload -> type);
				$type = str_replace("yn", "", $type);
				$type = str_replace("adv", "", $type);
				foreach ($receivers as $toUser)
				{
					$notifyApi->addNotification($toUser, $fromUser, $object, 'ynmember_notification_join', array(
						'type' => $type,
					));
				}
			}
			// FRIEND TOGETHER 
			else if ($payload -> type == 'friends')
			{
				$object = $payload->getObject();
				$subject = $payload->getSubject();
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				foreach ($receivers as $toUser)
				{
					$notifyApi->addNotification($toUser, $subject, $object, 'ynmember_notification_friends', array());
				}
			}
		}
	}
}
