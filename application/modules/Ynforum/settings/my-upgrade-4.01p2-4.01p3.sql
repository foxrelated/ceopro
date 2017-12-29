ALTER TABLE `engine4_forum_signatures` 
ADD `signature` TEXT NULL;

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) 
VALUES ('user_settings_signature', 'ynforum', 'Forum Signature', '', '{"route":"default", "module":"ynforum", "controller":"index", "action":"signature"}', 'user_settings', '', 1, 0, 999);