INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('ultimatenews', 'YNC - Ultimate News', 'Get data from remote servers', '4.05p1', 1, 'extra');

CREATE TABLE IF NOT EXISTS `engine4_ultimatenews_categoryparents` (
  `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200) DEFAULT NULL,
  `category_description` text ,
  `is_active` smallint(1) DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM COLLATE='utf8_unicode_ci';

--
-- Table structure for table `engine4_ultimatenews_category`
--

CREATE TABLE IF NOT EXISTS `engine4_ultimatenews_categories` (
  `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200),
  `category_parent_id` int(11) NOT NULL DEFAULT '0',
  `url_resource` varchar(500),
  `posted_date` DATETIME DEFAULT NULL,
  `category_logo` TEXT,
  `logo` TEXT,
  `is_active` smallint(1) DEFAULT '1',
  `approved` TINYINT( 1 ) NOT NULL DEFAULT  1,
  `owner_id` INT( 11 ) NOT NULL,
  `mini_logo` tinyint(1) NOT NULL DEFAULT 1,
  `display_logo` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` int(11) DEFAULT '0' NULL,
  `full_content` SMALLINT(6) DEFAULT '1' NULL, 
  `characters` INT(11) DEFAULT '0' NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM COLLATE='utf8_unicode_ci';


--
-- Table structure for table `engine4_ultimatenews_content`
--

CREATE TABLE IF NOT EXISTS `engine4_ultimatenews_contents` (
  `content_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `owner_type` varchar(50) DEFAULT 'user',
  `owner_id` int(11) UNSIGNED DEFAULT 1,
  `title` varchar(300) DEFAULT NULL,
  `description` TEXT,
  `content` TEXT,
  `image` varchar(300) DEFAULT NULL,
  `photo_id` int(11) NULL,
  `link_detail` varchar(300) DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `pubDate` varchar(100) DEFAULT NULL,
  `pubDate_parse` varchar(255) DEFAULT NULL,
  `posted_date` datetime DEFAULT NULL,
  `is_active` smallint(6) DEFAULT 1,
  `approved` TINYINT( 1 ) NOT NULL DEFAULT  1,
  `is_featured` smallint(6) DEFAULT 0,
  `count_view` int(11) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM COLLATE='utf8_unicode_ci';

--
-- Table structure for table `engine4_ultimatenews_timeframe`
--

CREATE TABLE IF NOT EXISTS`engine4_ultimatenews_timeframe` (
  `timeframe_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `minutes` varchar(2) DEFAULT '*',
  `hour` varchar(2) DEFAULT '*',
  `month` varchar(2) DEFAULT '*',
  `day` varchar(2) DEFAULT '*',
  `weekday` varchar(10) DEFAULT '*',
  PRIMARY KEY (`timeframe_id`)
) ENGINE=MyISAM  COLLATE='utf8_unicode_ci';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ultimatenews' as `type`,
    'subscribe' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

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

ALTER TABLE  `engine4_ultimatenews_categories` ADD  `subscribe` text default NULL;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) 
VALUES ('ultimatenews_admin_main_level', 'ultimatenews', 'Member Level Settings', '', '{"route":"admin_default","module":"ultimatenews","controller":"level"}', 'ultimatenews_admin_main', '', 6);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('subscribe_new_new', 'ultimatenews', '{item:$subject} has an update from {item:$object}', 1, 1, 1, 1, 1, 1);

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ultimatenews_main', 'standard', 'YNC - Ultimatenews Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ultimatenews_main_browsenews', 'ultimatenews', 'Browse News', '', '{"route":"ultimatenews_general","action":"index"}', 'ultimatenews_main', '', 1),
('ultimatenews_main_rsssubscription', 'ultimatenews', 'My RSS Subscription', 'Ultimatenews_Plugin_Menus', '{"route":"ultimatenews_general","action":"your-subscribe"}', 'ultimatenews_main', '', 2),
('ultimatenews_main_favoritenews ', 'ultimatenews', 'My Favourite News', 'Ultimatenews_Plugin_Menus', '{"route":"ultimatenews_general","action":"favorite"}', 'ultimatenews_main', '', 3),
('ultimatenews_main_feedmanagement ', 'ultimatenews', 'Manage Feeds', 'Ultimatenews_Plugin_Menus::canManageFeeds', '{"route":"ultimatenews_general","action":"manage-feed"}', 'ultimatenews_main', '', 4),
('ultimatenews_main_myfeeds ', 'ultimatenews', 'My Feeds', 'Ultimatenews_Plugin_Menus::canCreateFeeds', '{"route":"ultimatenews_general","action":"my-feed"}', 'ultimatenews_main', '', 5),
('ultimatenews_main_addfeed ', 'ultimatenews', 'Add Feed', 'Ultimatenews_Plugin_Menus::canCreateFeeds', '{"route":"ultimatenews_general","action":"create-feed"}', 'ultimatenews_main', '', 6),
('ultimatenews_main_newsmanagement ', 'ultimatenews', 'Manage News', 'Ultimatenews_Plugin_Menus::canManageNews', '{"route":"ultimatenews_general","action":"manage"}', 'ultimatenews_main', '', 7),
('ultimatenews_main_mynews ', 'ultimatenews', 'My News', 'Ultimatenews_Plugin_Menus::canCreateNews', '{"route":"ultimatenews_general","action":"my-news"}', 'ultimatenews_main', '', 8),
('ultimatenews_main_addnews ', 'ultimatenews', 'Add News', 'Ultimatenews_Plugin_Menus::canCreateNews', '{"route":"ultimatenews_general","action":"create-news"}', 'ultimatenews_main', '', 9);

CREATE TABLE IF NOT EXISTS `engine4_ultimatenews_favorites`(
	`favorite_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`content_id` INT(11) UNSIGNED NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`favorite_id`),
	KEY `newsId` (`content_id`),
	KEY `userId` (`user_id`)
	)ENGINE=MyISAM DEFAULT CHARSET=utf8;