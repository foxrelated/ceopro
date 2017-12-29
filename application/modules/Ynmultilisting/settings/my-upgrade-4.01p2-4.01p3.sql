UPDATE `engine4_core_modules` SET `version` = '4.01p3', `title` = 'YNC - Multiple Listings' WHERE `name` = 'ynmultilisting';

UPDATE  `engine4_core_menuitems` SET `params` =  '{"route":"ynmultilisting_general","icon":"fa-database"}' WHERE  `name` = 'core_main_ynmultilisting';
UPDATE  `engine4_core_menuitems` SET `label` = 'YNC - Multiple Listing' WHERE  `name` = 'core_admin_main_plugins_ynmultilisting';

UPDATE `engine4_younetcore_license` SET `title` = 'YNC - Multiple Listings' WHERE `name` = 'ynmultilisting';

ALTER TABLE `engine4_ynmultilisting_photos` ADD
`view_count` int(11) unsigned NOT NULL DEFAULT 0;

ALTER TABLE `engine4_ynmultilisting_photos` ADD
`like_count` int(11) unsigned NOT NULL DEFAULT 0;

ALTER TABLE `engine4_ynmultilisting_photos` ADD
`comment_count` int(11) unsigned NOT NULL DEFAULT 0;

ALTER TABLE `engine4_ynmultilisting_albums` ADD
`like_count` int(11) unsigned NOT NULL DEFAULT '0';