INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('yngroupactivity_advgroup_activity', 'yngroupactivity', 'Group Activities', 'Yngroupactivity_Plugin_Menus', '', 'advgroup_profile', '', 15);

-- -------------------------------------------------------
-- Table structure for table `engine4_group_reports`
--
CREATE TABLE IF NOT EXISTS `engine4_group_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`),
  KEY `topic_id` (`topic_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -------------------------------------------------------
-- Table structure for table `engine4_advgroup_public_activities`
--
CREATE TABLE IF NOT EXISTS `engine4_advgroup_public_activities`(
  `group_id` int(11) unsigned NOT NULL,
  `public_types` varchar(128) NOT NULL,
  PRIMARY KEY  (`group_id`,`public_types`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------
-- Dumping data for table `engine4_core_menuitems`
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_wiki', 'advgroup', 'Group Wikis', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 21),
('advgroup_profile_invite_all', 'advgroup', 'Invite All Users', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 12),
('advgroup_profile_invitemanage', 'advgroup', 'Invitations Management', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 15),
('advgroup_profile_activity', 'advgroup', 'Group Activities', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 22),

('advgroup_admin_main_reports', 'advgroup', 'Manage Reports', '', '{"route":"admin_default","module":"advgroup","controller":"report","action":"manage"}', 'advgroup_admin_main', '', 6)
;

-- Dumping data for table `engine4_activity_actiontypes`
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advgroup_wiki_create', 'advgroup', '{item:$subject} posted a new page:', 1, 3, 1, 1, 1, 1),
('advgroup_wiki_update', 'advgroup', '{item:$subject} has updated:', 1, 3, 1, 1, 1, 1),
('advgroup_wiki_move', 'advgroup', '{item:$subject} has moved:', 1, 3, 1, 1, 1, 1);

-- Dumping data for table `engine4_authorization_permissions`
-- auth_wiki
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_wiki' as `name`,
    5 as `value`,
    '["registered", "member", "officer"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
-- Admin
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'wiki' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
-- User
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'wiki' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
-- ---------------------------------------------------------
-- Dumping data for table `engine4_activity_notificationtypes`
--
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('advgroup_cancel_invite', 'advgroup' , 'The group {item:$subject} invitation has been cancel, please contact group owner for more information.',0,'')
;

-- -----------------------------------------------------------
-- Add column rejected-ignored on table `engine4_activity_notificationtypes`
--
ALTER TABLE `engine4_group_membership`  ADD COLUMN `rejected_ignored` tinyint(1) NOT NULL DEFAULT '0';
  