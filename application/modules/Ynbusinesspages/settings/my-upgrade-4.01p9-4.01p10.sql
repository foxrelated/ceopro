UPDATE `engine4_core_modules` SET `version` = '4.01p10' WHERE `name` = 'ynbusinesspages';
INSERT IGNORE INTO `engine4_ynbusinesspages_modules` (`title`, `item_type`) VALUES
('Video Channel', 'ynvideochannel_video');
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynbusinesspages_ynvideochannel_video_create', 'ynbusinesspages', '{item:$subject} add a video.', 1, 3, 1, 1, 1, 1);