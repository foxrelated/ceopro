UPDATE `engine4_core_modules` SET `version` = '4.04p7', `title` = 'YNC - Advanced Menu System' WHERE `engine4_core_modules`.`name` = 'advmenusystem' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `label` = 'YNC - Advanced Menu System' WHERE `name` = 'core_admin_main_plugins_advmenusystem';

UPDATE `engine4_younetcore_license` SET `title` = 'YNC - Advanced Menu System' WHERE `name` = 'advmenusystem';