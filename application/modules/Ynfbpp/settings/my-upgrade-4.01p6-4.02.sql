UPDATE `engine4_core_modules` SET `version` = '4.02' WHERE `name` = 'ynfbpp';

--
-- INSERT MENU - MENU ITEMS
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('ynfbpp_admin_main_business', 'ynfbpp', 'Business Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"business"}', 'ynfbpp_admin_main', '', 1, 0, 6),
('ynfbpp_admin_main_company', 'ynfbpp', 'Company Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"company"}', 'ynfbpp_admin_main', '', 1, 0, 7),
('ynfbpp_admin_main_store', 'ynfbpp', 'Store Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"store"}', 'ynfbpp_admin_main', '', 1, 0, 8),
('ynfbpp_admin_main_listing', 'ynfbpp', 'Listing Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"listing"}', 'ynfbpp_admin_main', '', 1, 0, 9),
('ynfbpp_admin_main_multilisting', 'ynfbpp', 'Multiple Listings Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"multilisting"}', 'ynfbpp_admin_main', '', 1, 0, 10)
;
