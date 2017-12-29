INSERT IGNORE INTO `engine4_core_menuitems` 
(`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobileview_profile_edit', 'ynmobileview', 'Update Info', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_profile', '', 3),
('ynmobileview_profile_cover', 'ynmobileview', 'Edit Cover', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_profile', '', 4);

UPDATE `engine4_core_modules` SET `version` = '4.03' where 'name' = 'ynmobileview';
