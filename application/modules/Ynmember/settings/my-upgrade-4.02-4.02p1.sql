UPDATE `engine4_core_modules` SET `version` = '4.02p1', `title` = 'YNC - Advanced Members' WHERE `name` = 'ynmember';
UPDATE `engine4_core_menuitems` SET `label` = 'YNC - Advanced Member' WHERE `name` = 'core_admin_main_plugins_ynmember';
UPDATE `engine4_core_menuitems` SET `params` = '{"route":"ynmember_general", "icon":"fa-user-circle-o"}' WHERE `name` = 'ynmember_main_browse';
UPDATE `engine4_younetcore_license` SET `title` = 'YNC - Advanced Member' WHERE `name` = 'ynmember';
