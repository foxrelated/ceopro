UPDATE `engine4_core_modules` SET `version` = '4.05p1' where 'name' = 'ultimatenews';

ALTER TABLE  `engine4_ultimatenews_contents` ADD  `approved` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `is_active` ;
ALTER TABLE  `engine4_ultimatenews_categories` ADD  `approved` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `is_active` ;
ALTER TABLE  `engine4_ultimatenews_categories` ADD  `owner_id` INT( 11 ) NOT NULL AFTER `is_active` ;
ALTER TABLE  `engine4_ultimatenews_categories` ADD  `meta_keywords` TEXT NULL AFTER  `characters` ;
ALTER TABLE  `engine4_ultimatenews_contents` ADD  `meta_keywords` TEXT NULL AFTER  `is_active` ;

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ultimatenews_main', 'standard', 'Ultimatenews Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ultimatenews_main_browsenews', 'ultimatenews', 'Browse News', '', '{"route":"ultimatenews_general","action":"index"}', 'ultimatenews_main', '', 1),
('ultimatenews_main_rsssubscription', 'ultimatenews', 'My RSS Subscription', '', '{"route":"ultimatenews_general","action":"your-subscribe"}', 'ultimatenews_main', '', 2),
('ultimatenews_main_favoritenews ', 'ultimatenews', 'My Favourite News', '', '{"route":"ultimatenews_general","action":"favorite"}', 'ultimatenews_main', '', 3),
('ultimatenews_main_feedmanagement ', 'ultimatenews', 'Manage Feeds', 'Ultimatenews_Plugin_Menus::canManageFeeds', '{"route":"ultimatenews_general","action":"manage-feed"}', 'ultimatenews_main', '', 4),
('ultimatenews_main_myfeeds ', 'ultimatenews', 'My Feeds', 'Ultimatenews_Plugin_Menus::canCreateFeeds', '{"route":"ultimatenews_general","action":"my-feed"}', 'ultimatenews_main', '', 5),
('ultimatenews_main_addfeed ', 'ultimatenews', 'Add Feed', 'Ultimatenews_Plugin_Menus::canCreateFeeds', '{"route":"ultimatenews_general","action":"create-feed"}', 'ultimatenews_main', '', 6),
('ultimatenews_main_newsmanagement ', 'ultimatenews', 'Manage News', 'Ultimatenews_Plugin_Menus::canManageNews', '{"route":"ultimatenews_general","action":"manage"}', 'ultimatenews_main', '', 7),
('ultimatenews_main_mynews ', 'ultimatenews', 'My News', 'Ultimatenews_Plugin_Menus::canCreateNews', '{"route":"ultimatenews_general","action":"my-news"}', 'ultimatenews_main', '', 8),
('ultimatenews_main_addnews ', 'ultimatenews', 'Add News', 'Ultimatenews_Plugin_Menus::canCreateNews', '{"route":"ultimatenews_general","action":"create-news"}', 'ultimatenews_main', '', 9);

DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'ultimatenews_admin_main_users';
UPDATE `engine4_core_menuitems` SET  `order` =  '0' WHERE `engine4_core_menuitems`.`name` = 'ultimatenews_admin_main_settings';
DROP TABLE `engine4_ultimatenews_params`;

CREATE TABLE IF NOT EXISTS `engine4_ultimatenews_favorites`(
	`favorite_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`content_id` INT(11) UNSIGNED NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`favorite_id`),
	KEY `newsId` (`content_id`),
	KEY `userId` (`user_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'approve_rss' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'approve_news' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'manage_feed' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'manage_news' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'create_feed' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'create_news' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');