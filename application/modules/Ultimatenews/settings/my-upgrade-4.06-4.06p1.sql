UPDATE `engine4_core_modules` SET `version` = '4.06p1', `title` =  'YNC - Ultimate News' where `name` = 'ultimatenews';
UPDATE `engine4_core_menuitems` SET  `label` =  'YNC - Ultimate News' WHERE `name` = 'core_admin_main_plugins_ultimatenews';
UPDATE `engine4_core_menuitems` SET `params` =  '{"route":"ultimatenews_extended","module":"ultimatenews","icon":"fa-newspaper-o"}' WHERE `name` = 'core_main_ultimatenews';
UPDATE `engine4_younetcore_license` SET  `title` =  'YNC - Ultimate News' WHERE `name` = 'ultimatenews';
