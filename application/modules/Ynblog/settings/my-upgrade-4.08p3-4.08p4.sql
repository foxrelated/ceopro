UPDATE `engine4_core_modules` SET `version` = '4.08p4' where 'name' = 'ynblog';
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynblog_admin_main_manageurl', 'ynblog', 'Manage Urls', '', '{"route":"admin_default","module":"ynblog","controller":"manage","action":"urls"}', 'ynblog_admin_main', '', 2);