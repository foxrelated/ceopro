UPDATE `engine4_core_modules` SET `version` = '4.11' WHERE `name` = 'advalbum';

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

ALTER TABLE `engine4_album_albums` 
ADD COLUMN `virtual` tinyint(1) DEFAULT '0' NULL AFTER `featured`;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advalbum_add_virtualalbum', 'advalbum', 'Add New Virtual Album', 'Advalbum_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"create-virtual-album"}', 'advalbum_main', '', 6);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advalbum_main_listing_photo', 'advalbum', 'Photo Listing', 'Advalbum_Plugin_Menus::canViewAlbums', '{"route":"album_general","action":"listing-photo"}', 'advalbum_main', '', 2);

UPDATE `engine4_core_menuitems` SET  `label` =  'Album Listing' WHERE `engine4_core_menuitems`.`name` = 'advalbum_main_listing';

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