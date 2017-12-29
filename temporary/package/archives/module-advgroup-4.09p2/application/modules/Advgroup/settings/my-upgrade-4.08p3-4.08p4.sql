INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advgroup_listing_create', 'advgroup', '{item:$subject} posted a new listing:', 1, 3, 1, 1, 1, 1);

UPDATE `engine4_core_modules` SET `version` = '4.08p4' WHERE `name` = 'advgroup';

 -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --  auth_listing
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_listing' as `name`,
    5 as `value`,
    '["registered", "member", "officer"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_listing' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'auth_listing' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
   -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- listing
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'listing' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'listing' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
    -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
    
 -- listing
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'listing' as `action`,
    'member' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'group' as `resource_type`,
    `group_id` as `resource_id`,
    'listing' as `action`,
    'advgroup_list' as `role`,
    `list_id` as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_group_groups`
  LEFT JOIN engine4_group_lists ON `engine4_group_lists`.`owner_id` = `engine4_group_groups`.`group_id`;
  
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_listing', 'advgroup', 'Group Listings', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 28);

-- change widget for mobile adv group detail page
UPDATE  `engine4_core_content` SET  `name` =  'advgroup.profile-cover' WHERE (
	(`name` =  'advgroup.profile-photo') AND (`page_id` = (
		SELECT `page_id` 
		FROM `engine4_core_pages` 
		WHERE `name` = 'advgroup_mobiprofile_index'
		LIMIT 1)
	)
);