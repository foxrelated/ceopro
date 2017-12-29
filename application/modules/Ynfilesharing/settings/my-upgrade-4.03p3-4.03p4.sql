UPDATE `engine4_core_modules` SET `version` = '4.03p4' WHERE `name` = 'ynfilesharing';
UPDATE  `engine4_core_menuitems` SET `params` = '{"route":"ynfilesharing_general","icon":"fa-files-o"}' WHERE  `name` = 'core_main_ynfilesharing';
UPDATE  `engine4_core_menuitems` SET `label` =  'YNC - File Sharing' WHERE  `name` = 'core_admin_plugins_ynfilesharing';
