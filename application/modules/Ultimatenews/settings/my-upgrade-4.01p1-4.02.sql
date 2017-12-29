ALTER TABLE `engine4_ultimatenews_categories` 
   ADD COLUMN `updated_at` int(11) DEFAULT '0' NULL AFTER `display_logo`;
   
ALTER TABLE  `engine4_ultimatenews_nusers` 
   ADD COLUMN `userid` int(11) NULL AFTER `username`,
   CHANGE `user_id` `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;