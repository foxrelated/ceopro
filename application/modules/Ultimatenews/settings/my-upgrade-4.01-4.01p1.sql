INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) 
VALUES ('ultimatenews_admin_main_settings', 'ultimatenews', 'Global Settings', '', '{"route":"admin_default","module":"ultimatenews","controller":"settings"}', 'ultimatenews_admin_main', '', 6);

ALTER TABLE `engine4_ultimatenews_contents` ADD COLUMN `photo_id` INT(11) NULL AFTER `image`;
