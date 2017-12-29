ALTER TABLE `engine4_group_groups` ADD COLUMN `is_subgroup` tinyint(1) NOT NULL DEFAULT '0' AFTER `featured`;
ALTER TABLE `engine4_group_groups` ADD COLUMN `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `is_subgroup`;
ALTER TABLE `engine4_group_categories` ADD COLUMN `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_sub_group' as `name`,
    5 as `value`,
    '["member", "officer", "owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_create_sub_group', 'advgroup', 'Create Sub Group', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 3),
('advgroup_profile_delete', 'advgroup', 'Delete Group', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 4),
('advgroup_profile_transfer', 'advgroup', 'Transfer Owner', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 5)
;

-- Activity Action Type --
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advgroup_sub_create', 'advgroup', '{item:$subject} created a new sub-group:', 1, 3, 1, 1, 1, 1),
('advgroup_transfer', 'advgroup', '{item:$subject} has became the owner of the group {item:$object}', 1, 3, 1, 1, 1, 1);
-- ActiNotification Action Type --
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('advgroup_transfer', 'advgroup', 'You were set to become the owner of the group {item:$object}.', 0, '')
;

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_authorization_permissions`
--

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_sub_group' as `name`,
    5 as `value`,
    '["member", "officer", "owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
-- -- Moderator & Admin -- --
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'sub_group' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
    
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
   level_id as `level_id`,
    'group' as `type`,
    'numberSubgroup' as `name`,
    3 as `value`,
    10 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
-- -- User -- --
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'sub_group' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'numberSubgroup' as `name`,
    3 as `value`,
    5 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');