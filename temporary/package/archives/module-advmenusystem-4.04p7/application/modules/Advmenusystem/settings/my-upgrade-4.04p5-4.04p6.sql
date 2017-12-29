UPDATE `engine4_core_modules` SET `version` = '4.04p6' WHERE `engine4_core_modules`.`name` = 'advmenusystem' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
('advmenusystem_admin_global_settings', 'advmenusystem', 'Global Settings', '', '{"route":"admin_default","module":"advmenusystem","controller":"global"}', 'advmenusystem_admin_main', '', 1);

UPDATE `engine4_core_menuitems` SET `order` = 2 WHERE `name` = 'advmenusystem_admin_main_styles';
UPDATE `engine4_core_menuitems` SET `order` = 3, `label` = 'Manage Menus', `order` = 3 WHERE `name` = 'advmenusystem_admin_main_menus';
UPDATE `engine4_core_menuitems` SET `order` = 4, `label` = 'Manage Content' WHERE `name` = 'advmenusystem_admin_contents_menu';
UPDATE `engine4_core_menuitems` SET `order` = 5, `label` = 'Manage Social Links' WHERE `name` = 'advmenusystem_admin_socials_menu';