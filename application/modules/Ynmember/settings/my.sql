-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_studyplaces`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_studyplaces` (
  `studyplace_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `location` text COLLATE utf8_unicode_ci NOT NULL,
  `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `current` BOOLEAN NOT NULL DEFAULT 0,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`studyplace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_liveplaces`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_liveplaces` (
  `liveplace_id` int(11) NOT NULL AUTO_INCREMENT,
  `location` text COLLATE utf8_unicode_ci NOT NULL,
  `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `current` BOOLEAN NOT NULL DEFAULT 0,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`liveplace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_workplaces`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_workplaces` (
  `workplace_id` int(11) NOT NULL AUTO_INCREMENT,
  `company` text COLLATE utf8_unicode_ci NOT NULL,
  `location` text COLLATE utf8_unicode_ci NOT NULL,
  `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `current` BOOLEAN NOT NULL DEFAULT 0,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`workplace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_ratings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `rating_type` int(11) unsigned NOT NULL,
  `rating` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `review_id` int(11) NOT NULL,
  PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_reviews`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` text COLLATE utf8_unicode_ci,
  `summary` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `helpful_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmember_useful`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_usefuls` (
  `useful_id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`useful_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmember_features`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_features` (
  `feature_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_orders` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `gateway_id` int(11) unsigned NOT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
  `creation_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `currency` char(3),
  `number_day` int(11) unsigned NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `state` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_transactions` (
`transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`payment_transaction_id` varchar(128),
`creation_date` date NOT NULL,
`status` enum('initialized','expired','pending','completed','canceled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`description` text NOT NULL,
`gateway_id` int(11) NOT NULL,
`amount` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`user_id` int(11) NOT NULL,
PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_ratingtypes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_ratingtypes` (
  `ratingtype_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ratingtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_user_relationships`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_relationships` (
  `relationship_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` text COLLATE utf8_unicode_ci,
  `with_member` tinyint(1) NOT NULL DEFAULT '0',
  `appear_feed` tinyint(1) NOT NULL DEFAULT '0',
  `user_approved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`relationship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynmember_relationships`
--

INSERT IGNORE INTO `engine4_ynmember_relationships` (`status`, `with_member`, `appear_feed`, `user_approved`) VALUES
('Single', 0, 1, 1),
('In a relationship', 1, 1, 1),
('Engaged', 1, 1, 1),
('Married', 1, 1, 1),
('In an open relationship', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_review_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_review_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynmember_review_fields_maps`
--

INSERT IGNORE INTO `engine4_ynmember_review_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_review_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_review_fields_meta` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text COLLATE utf8_unicode_ci,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynmember_review_fields_meta`
--

INSERT IGNORE INTO `engine4_ynmember_review_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_review_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_review_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynmember_review_fields_options`
--

INSERT IGNORE INTO `engine4_ynmember_review_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
(1, 1, 'Review', 999);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_review_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_review_fields_search` (
  `item_id` int(11) unsigned NOT NULL,
  `profile_type` enum('1','4') COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` smallint(6) unsigned DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `gender` (`gender`),
  KEY `birthdate` (`birthdate`),
  KEY `profile_type` (`profile_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_review_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_review_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_linkages`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_linkages` (
  `linkage_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` INT(11) UNSIGNED,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `relationship_id` INT(11) UNSIGNED NOT NULL,
  `active` TINYINT(1) DEFAULT NULL,
  `resource_approved` TINYINT(1) DEFAULT NULL,
  `user_approved` TINYINT(1) DEFAULT NULL,
  `anniversary` DATETIME DEFAULT NULL,
  PRIMARY KEY (`linkage_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmember_notifications`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmember_notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `active` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- change table permissions (change length of column type)
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_authorization_permissions` MODIFY `name` VARCHAR(64);

-- add feature member menu
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_profile_feature',  'ynmember',  'Feature Profile',  'Ynmember_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');

-- add rate member menu
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_rating_member',  'ynmember',  'Rate this member',  'Ynmember_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');

-- add like member menu
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_like_member',  'ynmember',  'Like this member',  'Ynmember_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');

-- add share member menu
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_share_member',  'ynmember',  'Share this member',  'Ynmember_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');

-- add direction member menu
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_direction_member',  'ynmember',  'Get Direction',  'Ynmember_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');

-- add suggest friend menu
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_suggest_friend_member',  'ynmember',  'Suggest Friends',  'Ynmember_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');

-- add get notification menu
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_notification_member',  'ynmember',  'Get Notification',  'Ynmember_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');

-- add workplace liveplace
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('user_edit_place', 'ynmember', 'Edit My Places', 'Ynmember_Plugin_Menus', '{"route":"ynmember_extended","module":"ynmember","controller":"edit","action":"place"}', 'user_edit', '', 5);

-- add relationship
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('user_edit_relationship', 'ynmember', 'Family and Relationships', 'Ynmember_Plugin_Menus', '{"route":"ynmember_extended","module":"ynmember","controller":"edit","action":"relationship"}', 'user_edit', '', 6);

-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynmember_main', 'standard', 'YNC Advanced Member Main Navigation Menu', 999);

-- insert admin menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynmember', 'ynmember', 'YNC - Advanced Member', '', '{"route":"admin_default","module":"ynmember","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynmember_admin_settings_global', 'ynmember', 'Global Settings', '', '{"route":"admin_default","module":"ynmember","controller":"settings", "action":"global"}', 'ynmember_admin_main', '', 1),
('ynmember_admin_settings_level', 'ynmember', 'Member Level Settings', '', '{"route":"admin_default","module":"ynmember","controller":"settings", "action":"level"}', 'ynmember_admin_main', '', 2),
('ynmember_admin_main_relationships', 'ynmember', 'Relationships', '', '{"route":"admin_default","module":"ynmember","controller":"relationships"}', 'ynmember_admin_main', '', 3),
('ynmember_admin_main_review_fields', 'ynmember', 'Review and Rating Settings', '', '{"route":"admin_default","module":"ynmember","controller":"review-fields"}', 'ynmember_admin_main', '', 4),
('ynmember_admin_manage_member', 'ynmember', 'Manage Members', '', '{"route":"admin_default","module":"ynmember","controller":"manage-members"}', 'ynmember_admin_main', '', 5),
('ynmember_admin_main_transactions', 'ynmember', 'Manage Transactions', '', '{"route":"admin_default","module":"ynmember","controller":"transactions"}', 'ynmember_admin_main', '', 6),
('ynmember_admin_main_reviews', 'ynmember', 'Manage Reviews', '', '{"route":"admin_default","module":"ynmember","controller":"reviews"}', 'ynmember_admin_main', '', 7);

-- insert front menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynmember_main_browse', 'ynmember', 'Browse Members', '', '{"route":"ynmember_general", "icon":"fa-user-circle-o"}', 'ynmember_main', '', 1, 0, 900),
('ynmember_main_myfriend', 'ynmember', 'My Friends', '', '{"route":"ynmember_extended","controller":"member","action":"myfriend"}', 'ynmember_main', '', 1, 0, 901),
('ynmember_main_featured_member', 'ynmember', 'Featured Members', '', '{"route":"ynmember_extended","controller":"member","action":"feature"}', 'ynmember_main', '', 1, 0, 902),
('ynmember_main_browse_review', 'ynmember', 'Browse Reviews', '', '{"route":"ynmember_extended","controller":"review"}', 'ynmember_main', '', 1, 0, 903),
('ynmember_main_member_rating', 'ynmember', 'Member Rating', '', '{"route":"ynmember_extended","controller":"member","action":"rating"}', 'ynmember_main', '', 1, 0, 904),
('ynmember_main_browse_birthday', 'ynmember', 'Browse Birthday', '', '{"route":"ynmember_extended","controller":"member","action":"birthday"}', 'ynmember_main', '', 1, 0, 905);

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'can_review_members' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'can_review_oneself' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_edit_own_review' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'can_like_members' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_share_reviews' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_report_reviews' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_delete_own_reviews' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'feature_fee' as `name`,
    3 as `value`,
    10 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'can_review_members' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'can_review_oneself' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_edit_own_review' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'can_like_members' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_share_reviews' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_report_reviews' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'can_delete_own_reviews' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'feature_fee' as `name`,
    3 as `value`,
    10 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- permission for member review
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id AS `level_id`,
    'ynmember_review' AS `type`,
    'auth_comment' AS `name`,
    5 AS `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' AS `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmember_review' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- RELATIONSHIP

-- MODERATOR, ADMIN

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_linkage' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_linkage' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmember_linkage' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- STUDY PLACE

-- MODERATOR, ADMIN

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_studyplace' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_studyplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmember_studyplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- WORK PLACE

-- MODERATOR, ADMIN

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_workplace' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_workplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmember_workplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- LIVING PLACE

-- MODERATOR, ADMIN

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_liveplace' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmember_liveplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmember_liveplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- Main menu
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmember_main_browse_review', 'ynmember', 'Browse Reviews', '', '{"route":"ynmember_general","controller":"review"}', 'ynmember_main', '', 1);

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmember_settings_privacy', 'ynmember', 'Member Privacy', '', '{"route":"ynmember_extended", "controller":"index", "action":"privacy"}', 'user_settings', '', 999);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmember_user' as `type`,
    'auth_get_notification' as `name`,
    5 as `value`,
    '["registered","network","member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, edit, delete, view, comment, block, search, status, username, style
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'get_notification' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'get_notification' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'get_notification' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynmember_liked', 'ynmember', '{item:$subject} likes {item:$object:$label}.', 0, ''),
('ynmember_shared', 'ynmember', '{item:$subject} has shared {item:$object:$label}.', 0, ''),
('ynmember_rated', 'ynmember', '{item:$subject} has post a {var:$text} of {item:$object:$label}.', 0, ''),
('ynmember_suggested', 'ynmember', '{item:$subject} has just suggested {item:$object} to you.', 0, ''),
('ynmember_notification_status', 'ynmember', '{item:$subject} has posted something on activity feed.', 0, ''),
('ynmember_notification_post', 'ynmember', '{item:$subject} has posted something on {item:$object}''s profile.', 0, ''),
('ynmember_notification_join', 'ynmember', '{item:$subject} has join {var:$type} {item:$object}.', 0, ''),
('ynmember_notification_friends', 'ynmember', '{item:$subject} is now friend with {item:$object}.', 0, ''),
('ynmember_notification_create', 'ynmember', '{item:$subject} has created a new {var:$type} {item:$object}.', 0, ''),
('ynmember_notification_linkage', 'ynmember', '{item:$subject} {var:$text}', 0, ''),
('ynmember_notification_change_relationship', 'ynmember', '{item:$subject} {var:$text}', 0, '');


--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynmember_liked', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_shared', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_rated', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_suggested', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_notification_status', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_notification_post', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_notification_join', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_notification_friends', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_notification_create', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_notification_linkage', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmember_notification_change_relationship', 'ynmember', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');


INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('ynmember_share', 'ynmember', '{item:$subject} shared {item:$object}. {body:$body}', 1, 5, 1, 1, 0, 1),
('ynmember_relationship', 'ynmember', '{item:$subject} {body:$body}', 1, 5, 1, 1, 1, 0);


INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Ynmember Check Feature Member', 'ynmember', 'Ynmember_Plugin_Task_CheckFeatureMember', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);