UPDATE `engine4_core_modules` SET `version` = '4.01p2' WHERE `name` = 'ynmultilisting';

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'ynultimatevideo_video' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'ynultimatevideo_video' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

ALTER TABLE `engine4_ynmultilisting_listings` ADD
`video_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci default NULL;
