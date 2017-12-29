--
-- INSERT MODULE
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynfbpp', 'YNC -  Profile Popup', 'Profile Popup', '4.01p6', 1, 'extra') ;

-- 
-- INSERT MENU - MENU ITEMS
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('core_admin_main_plugins_ynfbpp', 'ynfbpp', 'YNC - Profile Popup', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 1, 0, 999),
('ynfbpp_admin_main_settings', 'ynfbpp', 'Global Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"index"}', 'ynfbpp_admin_main', '', 1, 0, 1),
('ynfbpp_admin_main_user', 'ynfbpp', 'User Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"user"}', 'ynfbpp_admin_main', '', 1, 0, 2),
('ynfbpp_admin_main_fields', 'ynfbpp', 'User Fields Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"fields"}', 'ynfbpp_admin_main', '', 1, 0, 3),
('ynfbpp_admin_main_group', 'ynfbpp', 'Group Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"group"}', 'ynfbpp_admin_main', '', 1, 0, 4),
('ynfbpp_admin_main_event', 'ynfbpp', 'Event Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"event"}', 'ynfbpp_admin_main', '', 1, 0, 5),
('ynfbpp_admin_main_business', 'ynfbpp', 'Business Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"business"}', 'ynfbpp_admin_main', '', 1, 0, 6),
('ynfbpp_admin_main_company', 'ynfbpp', 'Company Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"company"}', 'ynfbpp_admin_main', '', 1, 0, 7),
('ynfbpp_admin_main_store', 'ynfbpp', 'Store Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"store"}', 'ynfbpp_admin_main', '', 1, 0, 8),
('ynfbpp_admin_main_listing', 'ynfbpp', 'Listing Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"listing"}', 'ynfbpp_admin_main', '', 1, 0, 9),
('ynfbpp_admin_main_multilisting', 'ynfbpp', 'Multiple Listings Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"multilisting"}', 'ynfbpp_admin_main', '', 1, 0, 10)
;

--
-- CREATE TABLE
CREATE TABLE `engine4_ynfbpp_popup` (
  `field_id` int(11) unsigned NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'user',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

