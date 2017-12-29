UPDATE `engine4_core_modules` SET `version` = '4.08p5' WHERE `name` = 'advgroup';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_social_music_album', 'advgroup', 'Group Social Music Albums', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 29);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_social_music_song', 'advgroup', 'Group Social Music Songs', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 30);

-- ultimate video
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'ynultimatevideo_video' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'group' as `type`,
    'ynultimatevideo_video' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_ultimatevideo', 'advgroup', 'Group Ultimate Videos', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 30);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('advgroup_profile_videochannel', 'advgroup', 'Group VideoChannel Videos', 'Advgroup_Plugin_Menus', '', 'advgroup_profile', '', 31);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advgroup_ynmusic_song_create', 'advgroup', '{item:$subject} has uploaded a new song:', 1, 3, 1, 1, 1, 1),
('advgroup_ynmusic_album_create', 'advgroup', '{item:$subject} has created a new album:', 1, 3, 1, 1, 1, 1);