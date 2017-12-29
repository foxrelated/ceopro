UPDATE `engine4_core_modules` SET `version` = '4.09p2' WHERE `name` = 'advgroup';
UPDATE `engine4_core_menuitems` SET `label` = 'YNC - Advanced Groups' WHERE `name` = 'core_admin_main_plugins_advgroup';
UPDATE `engine4_core_menuitems` SET `params` = '{"route":"group_general", "icon":"fa-users"}' WHERE `name` = 'core_main_advgroup';
UPDATE `engine4_younetcore_license` SET `title` = 'YNC - Advanced Groups' WHERE `name` = 'advgroup';