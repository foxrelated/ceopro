-- --------------------------------------------------------

--
-- Table structure for table `engine4_album_ratings`
--

CREATE TABLE IF NOT EXISTS `engine4_album_ratings` (
	`rating_id` INT(11) NOT NULL AUTO_INCREMENT,
	`subject_id` INT(11) UNSIGNED NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`rating` TINYINT(1) UNSIGNED NOT NULL,
	`type` ENUM('photo','album') NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
-- --------------------------------------------------------
--
-- Table structure for table `engine4_album_albums`
--

CREATE TABLE IF NOT EXISTS `engine4_album_albums` (
	`album_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` MEDIUMTEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`owner_type` VARCHAR(64) NOT NULL COLLATE 'latin1_general_ci',
	`owner_id` INT(11) UNSIGNED NOT NULL,
	`category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`creation_date` DATETIME NOT NULL,
	`modified_date` DATETIME NOT NULL,
	`photo_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`view_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`comment_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`search` TINYINT(1) NOT NULL DEFAULT '1',
	`type` ENUM('wall','profile','message','blog') NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`album_id`),
	INDEX `owner_type` (`owner_type`, `owner_id`),
	INDEX `category_id` (`category_id`),
	INDEX `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `engine4_album_albums` ADD COLUMN `rating` FLOAT NOT NULL DEFAULT '0';
ALTER TABLE `engine4_album_albums` ADD COLUMN `featured` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `engine4_album_albums` ADD COLUMN `virtual` TINYINT(1) NULL DEFAULT '0';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_album_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_album_categories` (
  `category_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_album_categories`
--

INSERT IGNORE INTO `engine4_album_categories` (`category_id`, `user_id`, `category_name`) VALUES
(1, 1, 'Arts & Culture'),
(2, 1, 'Business'),
(3, 1, 'Entertainment'),
(5, 1, 'Family & Home'),
(6, 1, 'Health'),
(7, 1, 'Recreation'),
(8, 1, 'Personal'),
(9, 1, 'Shopping'),
(10, 1, 'Society'),
(11, 1, 'Sports'),
(12, 1, 'Technology'),
(13, 1, 'Others');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_album_photos`
--
CREATE TABLE IF NOT EXISTS `engine4_album_photos` (
	`photo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`album_id` INT(11) UNSIGNED NOT NULL,
	`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
	`description` MEDIUMTEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`creation_date` DATETIME NOT NULL,
	`modified_date` DATETIME NOT NULL,
	`order` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`owner_type` VARCHAR(64) NOT NULL COLLATE 'latin1_general_ci',
	`owner_id` INT(11) UNSIGNED NOT NULL,
	`file_id` INT(11) UNSIGNED NOT NULL,
	`view_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`comment_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`photo_id`),
	INDEX `album_id` (`album_id`),
	INDEX `owner_type` (`owner_type`, `owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `engine4_album_photos` ADD COLUMN `rating` FLOAT NOT NULL DEFAULT '0';
ALTER TABLE `engine4_album_photos` ADD COLUMN `taken_date` DATE NULL;
ALTER TABLE `engine4_album_photos` ADD COLUMN `location` TEXT NULL COLLATE 'utf8_unicode_ci';

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('advalbum_main', 'standard', 'Album Main Navigation Menu'),
('advalbum_quick', 'standard', 'Album Quick Navigation Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'user_home_advalbum', 'advalbum', 'My Photos', 'Advalbum_Plugin_Menus::canViewAlbums', '{"route":"album_general","controller":"index","action":"browsebyuser","module":"advalbum"}', 'user_home', '', 1, 0, 6),
( 'user_profile_advalbum', 'advalbum', 'Photos', 'Advalbum_Plugin_Menus', '', 'user_profile', '', 1, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_advalbum', 'advalbum', 'Albums', '', '{"route":"album_general","action":"browse"}', 'core_main', '', 3),

('core_sitemap_advalbum', 'advalbum', 'Albums', '', '{"route":"album_general","action":"browse"}', 'core_sitemap', '', 3),

('advalbum_main_browse', 'advalbum', 'Home', 'Advalbum_Plugin_Menus::canViewAlbums', '{"route":"album_general","action":"browse"}', 'advalbum_main', '', 1),
('advalbum_main_listing', 'advalbum', 'Album Listing', 'Advalbum_Plugin_Menus::canViewAlbums', '{"route":"album_general","action":"listing"}', 'advalbum_main', '', 2),
('advalbum_main_manage', 'advalbum', 'My Albums', 'Advalbum_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"manage"}', 'advalbum_main', '', 3),
('advalbum_main_upload', 'advalbum', 'Add New Photos', 'Advalbum_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"upload"}', 'advalbum_main', '', 4),

('advalbum_quick_upload', 'advalbum', 'Add New Photos', 'Advalbum_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"upload","class":"buttonlink icon_photos_new"}', 'advalbum_quick', '', 1),

('core_admin_main_plugins_advalbum', 'advalbum', 'YNC - Advanced Album', '', '{"route":"admin_default","module":"advalbum","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 999),

('advalbum_admin_main_manage', 'advalbum', 'View Albums', '', '{"route":"admin_default","module":"advalbum","controller":"manage"}', 'advalbum_admin_main', '', 1),
('advalbum_admin_main_settings', 'advalbum', 'Global Settings', '', '{"route":"admin_default","module":"advalbum","controller":"settings"}', 'advalbum_admin_main', '', 3),
('advalbum_admin_main_level', 'advalbum', 'Member Level Settings', '', '{"route":"admin_default","module":"advalbum","controller":"level"}', 'advalbum_admin_main', '', 4),
('advalbum_admin_main_categories', 'advalbum', 'Categories', '', '{"route":"admin_default","module":"advalbum","controller":"settings", "action":"categories"}', 'advalbum_admin_main', '', 5)
;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('advalbum', 'Photo Albums', 'This plugin gives your users their own personal photo albums. These albums can be configured to store photos, videos, or any other file types you choose to allow. Users can interact by commenting on each others photos and viewing their friends', '4.11', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advalbum_photo_new', 'advalbum', '{item:$subject} added {var:$count} photo(s) to the album {item:$object}:', 1, 5, 1, 3, 1, 1),
('comment_advalbum', 'advalbum', '{item:$subject} commented on {item:$owner}''s {item:$object:album}: {body:$body}', 1, 1, 1, 1, 1, 0),
('comment_advalbum_photo', 'advalbum', '{item:$subject} commented on {item:$owner}''s {item:$object:photo}: {body:$body}', 1, 1, 1, 1, 1, 0);


INSERT INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('send_image', 'advalbum', '[host],[email],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo]');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_tag
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'auth_tag' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- USERS
-- view, comment, tag, create, edit, delete
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'tag' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- ADMIN, MODERATOR
-- view, comment, tag, create, edit, delete
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'tag' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- PUBLIC
-- view, tag
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'tag' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

CREATE TABLE IF NOT EXISTS`engine4_album_params` (
  `param_id` int(11) NOT NULL default '1',
  `search` varchar(256) default NULL,
  `category_id` int(11) NOT NULL,
  `sort` varchar(255) default NULL,
  `page` int(11) NOT NULL default '1',
  PRIMARY KEY  (`param_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT IGNORE INTO `engine4_album_params` (`param_id`, `search`, `category_id`, `sort`, `page`) VALUES
(1, NULL, 0, NULL, 1);

CREATE TABLE IF NOT EXISTS `engine4_album_features` (
  `feature_id` int(11) unsigned NOT NULL auto_increment,
  `photo_id` int(11) NOT NULL,
  `photo_good` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`feature_id`),
  KEY `photo_id` (`photo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advalbum_admin_main_photos', 'advalbum', 'View Photos', '', '{"route":"admin_default","module":"advalbum","controller":"manage" , "action":"photos"}', 'advalbum_admin_main', '', 2);

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'user_home_advalbum', 'advalbum', 'My Photos', 'Advalbum_Plugin_Menus::canViewAlbums', '{"route":"album_general","controller":"index","action":"browsebyuser","module":"advalbum"}', 'user_home', '', 1, 0, 6),
( 'user_profile_advalbum', 'advalbum', 'Photos', 'Advalbum_Plugin_Menus', '', 'user_profile', '', 1, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUE
('advalbum_admin_main_color', 'advalbum', 'Color Settings', '', '{"route":"admin_default","module":"advalbum","controller":"color"}', 'advalbum_admin_main', '', 6);

CREATE TABLE IF NOT EXISTS `engine4_advalbum_colors` (
	`color_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(64) NOT NULL COLLATE 'utf8_unicode_ci',
	`hex_value` VARCHAR(8) NOT NULL COLLATE 'utf8_unicode_ci',
	`default_hex_value` VARCHAR(8) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`color_id`),
	UNIQUE KEY (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_advalbum_colors` ( `title`, `hex_value`, `default_hex_value`) VALUES
('RED', '#FF0000', '#FF0000'),
('ORANGE', '#FA9A4D', '#FA9A4D'),
('YELLOW', '#F8E275', '#F8E275'),
('GREEN', '#62854F', '#62854F'),
('TURQUIOSE', '#2CCACD', '#2CCACD'),
('BLUE', '#1D329D', '#1D329D'),
('PURPLE', '#A746B1', '#A746B1'),
('PINK', '#FAACA8', '#FAACA8'),
('WHITE', '#FFFFFF', '#FFFFFF'),
('GRAY', '#777777', '#777777'),
('BLACK', '#000000', '#000000'),
('BROWN', '#815B10', '#815B10');

CREATE TABLE IF NOT EXISTS `engine4_advalbum_photocolors` (
	`photocolor_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`photo_id` INT(11) NOT NULL DEFAULT 0,
	`color_title` VARCHAR(64) NOT NULL COLLATE 'utf8_unicode_ci',
	`pixel_count` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`photocolor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advalbum_add_virtualalbum', 'advalbum', 'Add New Virtual Album', 'Advalbum_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"create-virtual-album"}', 'advalbum_main', '', 5);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advalbum_main_listing_photo', 'advalbum', 'Photo Listing', 'Advalbum_Plugin_Menus::canViewAlbums', '{"route":"album_general","action":"listing-photo"}', 'advalbum_main', '', 2);

CREATE TABLE IF NOT EXISTS `engine4_advalbum_virtualphotos` (
	`virtualphoto_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`photo_id` INT(11) NOT NULL DEFAULT 0,
	`album_id` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`virtualphoto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'auth_add_photo' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'addphoto' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'advalbum_album' as `type`,
    'addphoto' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');