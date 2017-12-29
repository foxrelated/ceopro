UPDATE `engine4_core_modules` SET `version` = '4.03p1' where 'name' = 'ultimatenews';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'subscribe' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

ALTER TABLE  `engine4_ultimatenews_categories` ADD  `subscribe` text default NULL;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) 
VALUES ('ultimatenews_admin_main_level', 'ultimatenews', 'Member Level Settings', '', '{"route":"admin_default","module":"ultimatenews","controller":"level"}', 'ultimatenews_admin_main', '', 6);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('subscribe_new_new', 'ultimatenews', '{item:$subject} has an update from {item:$object}', 1, 1, 1, 1, 1, 1);