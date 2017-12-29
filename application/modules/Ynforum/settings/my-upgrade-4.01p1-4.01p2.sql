UPDATE `engine4_core_modules` SET `version` = '4.01p2' WHERE `name` = 'ynforum';

--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynforum_owner_post_approved', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_post_wait_approval', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_promote', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_reply', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_reputation', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_response', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_thank', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]')
;

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynforum_topic_create', 'ynforum', '{item:$subject} has created a {item:$object:topic} on the {itemParent:$object::forum} you are watching.', 0, ''),
('ynforum_topic_reply_forum_watch', 'ynforum', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::forum topic}.', 0, '')
;

CREATE TABLE IF NOT EXISTS `engine4_forum_forumwatches` (
  `forum_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `watch` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`forum_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;