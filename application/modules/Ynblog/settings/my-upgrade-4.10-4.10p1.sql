UPDATE `engine4_core_modules` SET `version` = '4.10p1' where 'name' = 'ynblog';
ALTER TABLE `engine4_blog_blogs` ADD `share_count` int(11) unsigned DEFAULT '0' AFTER `become_count`;

ALTER TABLE  `engine4_blog_categories` ADD  `parent_id` INT( 10 ) NOT NULL DEFAULT  '0' AFTER `category_name` ,
ADD  `level` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `parent_id` ;

INSERT INTO `engine4_blog_categories` (`category_id`, `user_id`, `category_name`, `parent_id`, `level`) VALUES ('0', '1', 'All Categories', '-1', '0');
UPDATE `engine4_blog_categories` SET  `category_id` =  '0' WHERE `engine4_blog_categories`.`parent_id` = -1;

