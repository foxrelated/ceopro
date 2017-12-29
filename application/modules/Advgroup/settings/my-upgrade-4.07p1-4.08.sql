UPDATE `engine4_core_modules` SET `version` = '4.08' WHERE `name` = 'advgroup';

--
-- Table structure for table `engine4_group_sponsors`
--

CREATE TABLE IF NOT EXISTS `engine4_group_sponsors` (
  `sponsor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sponsor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_advgroup_announcement_marks`
--

CREATE TABLE IF NOT EXISTS `engine4_advgroup_announcement_marks` (
  `mark_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`mark_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_advgroup_blacklists`
--

CREATE TABLE IF NOT EXISTS `engine4_advgroup_blacklists` (
  `group_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_advgroup_highlights`
--

CREATE TABLE IF NOT EXISTS `engine4_advgroup_highlights` (
  `highlight_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `highlight` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`highlight_id`,`group_id`,`item_id`),
  KEY `user_id` (`group_id`,`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_advgroup_mappings`
--

CREATE TABLE IF NOT EXISTS `engine4_advgroup_mappings` (
  `mapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`mapping_id`,`group_id`,`item_id`),
  KEY `user_id` (`group_id`,`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

ALTER TABLE `engine4_group_groups` ADD COLUMN `cover_photo` int(11) UNSIGNED DEFAULT NULL AFTER `view_count`;
ALTER TABLE `engine4_group_photos` ADD COLUMN `is_featured` tinyint(1) NOT NULL DEFAULT '0' AFTER `comment_count`;

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_menuitems`

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_music', 'advgroup', 'Group Musics', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 24),
('advgroup_profile_mp3_music', 'advgroup', 'Group Mp3 Musics', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 25),
('advgroup_file_sharing', 'advgroup', 'Group File Sharing', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '' , 26),
('advgroup_profile_message_to_owner\r\n', 'advgroup', 'Message To Owner', 'Advgroup_Plugin_Menus::canSendMessageToOwner', '', 'advgroup_profile', '', 27);

-- --------------------------------------------------------

 -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --  manage member
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'photo.edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'photo.edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- announcement
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'announcement' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'announcement' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
    -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- invitation
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'invitation' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'invitation' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
   -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- edit
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
   -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- style
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'style' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
   -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- sponsor
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'sponsor' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'sponsor' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
   -- file.edit
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'file.edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'file.edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- create group permission  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

 -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --  auth_music
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_music' as `name`,
    5 as `value`,
    '["registered", "member", "officer"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_music' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_music' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --  auth_folder
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_folder' as `name`,
    5 as `value`,
    '["registered", "member", "officer"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
   INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_folder' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_folder' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --  file upload
    INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_file_upload' as `name`,
    5 as `value`,
    '["registered", "member", "officer"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
   INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_file_upload' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_file_upload' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- file download
    INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_file_down' as `name`,
    5 as `value`,
    '["registered", "member", "officer"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_file_down' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_file_down' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
 
 -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- folder
 
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'folder' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'folder' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

   -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- music
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'music' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'music' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
 
-- update auth allow for existing group 

 -- announcement 
	INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'announcement' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
 -- file.edit 
	INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'file.edit' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
  -- file_down
	
	INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'file_down' as `action`,
    'member' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'file_down' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
   -- file_upload
 
 	INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'file_upload' as `action`,
    'member' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'file_upload' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
   -- folder
 
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'folder' as `action`,
    'member' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'folder' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
 -- invitation
	
	INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'invitation' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
-- invite
	
	INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'invite' as `action`,
    'member' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'invite' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;

-- member.edit
  
  INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'member.edit' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
 -- music
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'music' as `action`,
    'member' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'music' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
-- sponsor
	
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'sponsor' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
 -- style
	
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'style' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- upload
 
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'file_upload' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'file_upload' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
    -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- download
 
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'file_down' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'file_down' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  