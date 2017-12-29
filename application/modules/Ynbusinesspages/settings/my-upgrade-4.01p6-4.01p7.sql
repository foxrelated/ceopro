UPDATE `engine4_core_modules` SET `version` = '4.01p7' WHERE `name` = 'ynbusinesspages';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynbusinesspages_main_create_business', 'ynbusinesspages', 'Create', 'Ynbusinesspages_Plugin_Menus', '{"route":"ynbusinesspages_general","controller":"index","action":"create"}', 'ynbusinesspages_main', '', 1, 0, 5);
