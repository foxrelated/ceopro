UPDATE `engine4_core_modules` SET `version` = '4.02p2' where 'name' = 'ynultimatevideo';

UPDATE `engine4_core_menuitems` SET `label` = 'YNC - Ultimate Videos' WHERE `name` = 'core_admin_main_plugins_ynultimatevideo';

UPDATE `engine4_younetcore_license` SET `title` = 'YNC - Ultimate Videos' WHERE `name` = 'ynultimatevideo';

UPDATE  `engine4_core_menuitems` SET `params` =  '{"route":"ynultimatevideo_general","module":"ynultimatevideo","icon":"fa-video-camera"}' WHERE `name` = 'core_main_ynultimatevideo';