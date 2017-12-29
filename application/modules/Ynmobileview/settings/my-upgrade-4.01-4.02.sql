UPDATE `engine4_core_menuitems` SET `name` = 'core_footer_ynmobile' WHERE `engine4_core_menuitems`.`name` = "core_footer_mobile";
UPDATE `engine4_core_menuitems` set `module` = 'ynmobileview' WHERE `engine4_core_menuitems`.`name` = 'core_footer_ynmobile';
update `engine4_core_menuitems` set plugin = 'Ynmobileview_Plugin_Menus' where `engine4_core_menuitems`.`name` = 'core_footer_ynmobile';

UPDATE `engine4_core_modules` SET `version` = '4.02' where 'name' = 'ynmobileview';