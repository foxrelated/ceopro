UPDATE `engine4_core_modules` SET `version` = '4.07' WHERE `name` = 'advgroup';
ALTER TABLE  `engine4_group_groups` ADD  `location` TEXT NOT NULL AFTER  `category_id`;
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advgroup_event_create', 'advgroup', '{item:$subject} created a new event: {body:$body}', 1, 3, 1, 1, 1, 1);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_event', 'advgroup', 'Group Events', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 20);
