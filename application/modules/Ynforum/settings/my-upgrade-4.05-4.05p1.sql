UPDATE `engine4_core_modules` SET `version` = '4.05p1' where 'name' = 'ynforum';
UPDATE `engine4_core_modules` SET  `title` =  'YNC - Advanced Forum' WHERE `engine4_core_modules`.`name` =  'ynforum';
UPDATE `engine4_core_menuitems` SET `params` =  '{"route":"ynforum_general","module":"ynforum","icon":"fa-inbox"}' WHERE `name` = 'core_main_ynforum';
UPDATE `engine4_younetcore_license` SET  `title` =  'YNC - Advanced Forum' WHERE `name` = 'ynforum';
