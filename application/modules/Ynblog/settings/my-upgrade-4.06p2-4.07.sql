DELETE FROM `engine4_core_menuitems` where `name` = 'ynblog_main_import';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynblog_main_import', 'ynblog', 'Import Blogs', 'YnBlog_Plugin_Menus::canCreateBlogs', '{"route":"blog_import","action":"import"}', 'ynblog_main', '', 4);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynblog_import', 'ynblog', '{item:$subject} imported a new blog entry:', 1, 5, 1, 3, 1, 1);

CREATE TABLE IF NOT EXISTS `engine4_blog_links` (
  `link_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `link_url` varchar(300) collate utf8_unicode_ci default NULL,
  `last_run` datetime default NULL,
  `cronjob_enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`link_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;