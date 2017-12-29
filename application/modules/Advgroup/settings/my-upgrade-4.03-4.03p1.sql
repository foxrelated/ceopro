-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advgroup_video_create', 'advgroup', '{item:$subject} posted a new video:', 1, 3, 1, 1, 1, 1);

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} posted a new topic:'
WHERE `type` = 'advgroup_topic_create' and `module` = 'advgroup';

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} replied to the topic {body:$body}'
WHERE `type` = 'advgroup_topic_reply' and `module` = 'advgroup';
-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_video', 'advgroup', 'Group Videos', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 14);
-- --------------------------------------------------------
--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_video' as `name`,
    5 as `value`,
    '["registered", "member", "officer"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
-- ADMIN, MODERATOR
-- video
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'video' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'video' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');