ALTER TABLE `engine4_ultimatenews_categories` 
   ADD COLUMN `full_content` SMALLINT(6) DEFAULT '1' NULL AFTER `updated_at`, 
   ADD COLUMN `characters` INT(11) DEFAULT '0' NULL AFTER `full_content`;
   
UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"ultimatenews","controller":"manage","action":"feed"}'
WHERE `module` = 'ultimatenews' AND `name` = 'ultimatenews_admin_main_category';