UPDATE `engine4_core_modules` SET `version` = '4.05p4' WHERE `name` = 'advgroup';
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('advgroup_link_new', 'advgroup', '{item:$subject} created a new link: {body:$body}', 1, 3, 1, 1, 1, 1);