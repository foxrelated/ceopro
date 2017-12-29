-- --------------------------------------------------------

--
-- Table structure for table `engine4_blog_becomes`
--
CREATE TABLE `engine4_blog_becomes` (
`become_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`blog_id` INT( 11 ) NOT NULL DEFAULT '0',
`user_id` INT( 11 ) NOT NULL DEFAULT '0',
PRIMARY KEY ( `become_id` ),
CONSTRAINT blog_user UNIQUE (blog_id, user_id)
) ENGINE = InnoDB ;

ALTER TABLE `engine4_blog_blogs` ADD `is_featured` tinyint(1) NOT NULL default '0' AFTER `draft`;
ALTER TABLE `engine4_blog_blogs` ADD `is_approved` tinyint(1) NOT NULL default '0' AFTER `draft`;
ALTER TABLE `engine4_blog_blogs` ADD `add_activity` tinyint(1) NOT NULL default '0' AFTER `is_approved`;
ALTER TABLE `engine4_blog_blogs` ADD `share_count` int(11) unsigned DEFAULT '0';

UPDATE `engine4_blog_blogs` SET `is_approved` = '1';
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('ynblog', 'Advanced Blog', '', '4.09', 1, 'extra');
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_tasks`
--
INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
('Rebuild Blog Privacy', 'ynblog_maintenance_rebuild_privacy', 'ynblog', 'Ynblog_Plugin_Job_Maintenance_RebuildPrivacy', 50);
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ynblog_main', 'standard', 'Advanced Blog Main Navigation Menu'),
('ynblog_quick', 'standard', 'Advanced Blog Quick Navigation Menu'),
('ynblog_gutter', 'standard', 'Advanced Blog Gutter Navigation Menu');
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynblog', 'ynblog', 'Blogs', '', '{"route":"blog_general"}', 'core_main', '', 4),
('core_sitemap_ynblog', 'ynblog', 'Blogs', '', '{"route":"blog_general"}', 'core_sitemap', '', 4),

('ynblog_main_browse', 'ynblog', 'Browse Entries', 'Ynblog_Plugin_Menus::canViewBlogs', '{"route":"blog_general"}', 'ynblog_main', '', 1),
('ynblog_main_manage', 'ynblog', 'My Entries', 'Ynblog_Plugin_Menus::canCreateBlogs', '{"route":"blog_general","action":"manage"}', 'ynblog_main', '', 2),
('ynblog_main_create', 'ynblog', 'Write New Entry', 'Ynblog_Plugin_Menus::canCreateBlogs', '{"route":"blog_general","action":"create"}', 'ynblog_main', '', 3),

('ynblog_quick_create', 'ynblog', 'Write New Entry', 'Ynblog_Plugin_Menus::canCreateBlogs', '{"route":"blog_general","action":"create","class":"buttonlink icon_blog_new"}', 'ynblog_quick', '', 1),
('ynblog_quick_style', 'ynblog', 'Edit Blog Style', 'Ynblog_Plugin_Menus', '{"route":"blog_general","action":"style","class":"smoothbox buttonlink icon_blog_style"}', 'ynblog_quick', '', 2),

('ynblog_gutter_list', 'ynblog', 'View All Entries', 'Ynblog_Plugin_Menus', '{"route":"blog_view","class":"buttonlink icon_blog_viewall"}', 'ynblog_gutter', '', 1),
('ynblog_gutter_create', 'ynblog', 'Write New Entry', 'Ynblog_Plugin_Menus', '{"route":"blog_general","action":"create","class":"buttonlink icon_blog_new"}', 'ynblog_gutter', '', 2),
('ynblog_gutter_style', 'ynblog', 'Edit Blog Style', 'Ynblog_Plugin_Menus', '{"route":"blog_general","action":"style","class":"smoothbox buttonlink icon_blog_style"}', 'ynblog_gutter', '', 3),
('ynblog_gutter_edit', 'ynblog', 'Edit This Entry', 'Ynblog_Plugin_Menus', '{"route":"blog_specific","action":"edit","class":"buttonlink icon_blog_edit"}', 'ynblog_gutter', '', 4),
('ynblog_gutter_delete', 'ynblog', 'Delete This Entry', 'Ynblog_Plugin_Menus', '{"route":"blog_specific","action":"delete","class":"buttonlink smoothbox icon_blog_delete"}', 'ynblog_gutter', '', 5),
('ynblog_gutter_share', 'ynblog', 'Share', 'Ynblog_Plugin_Menus', '{"route":"default","module":"activity","controller":"index","action":"share","class":"buttonlink smoothbox icon_comments"}', 'ynblog_gutter', '', 6),
('ynblog_gutter_report', 'ynblog', 'Report', 'Ynblog_Plugin_Menus', '{"route":"default","module":"core","controller":"report","action":"create","class":"buttonlink smoothbox icon_report"}', 'ynblog_gutter', '', 7),
('ynblog_gutter_subscribe', 'ynblog', 'Subscribe', 'Ynblog_Plugin_Menus', '{"route":"default","module":"ynblog","controller":"subscription","action":"add","class":"buttonlink smoothbox icon_blog_subscribe"}', 'ynblog_gutter', '', 8),

('core_admin_main_plugins_ynblog', 'ynblog', 'Blogs', '', '{"route":"admin_default","module":"ynblog","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('ynblog_admin_main_manage', 'ynblog', 'Manage Blogs', '', '{"route":"admin_default","module":"ynblog","controller":"manage"}', 'ynblog_admin_main', '', 1),
('ynblog_admin_main_manageurl', 'ynblog', 'Manage Urls', '', '{"route":"admin_default","module":"ynblog","controller":"manage","action":"urls"}', 'ynblog_admin_main', '', 2),
('ynblog_admin_main_settings', 'ynblog', 'Global Settings', '', '{"route":"admin_default","module":"ynblog","controller":"settings"}', 'ynblog_admin_main', '', 3),
('ynblog_admin_main_level', 'ynblog', 'Member Level Settings', '', '{"route":"admin_default","module":"ynblog","controller":"level"}', 'ynblog_admin_main', '', 4),
('ynblog_admin_main_categories', 'ynblog', 'Categories', '', '{"route":"admin_default","module":"ynblog","controller":"settings", "action":"categories"}', 'ynblog_admin_main', '', 5),
('ynblog_admin_main_addthis', 'ynblog', 'AddThis Settings', '', '{"route":"admin_default","module":"ynblog","controller":"addthis"}', 'ynblog_admin_main', '', 6),

('authorization_admin_level_ynblog', 'ynblog', 'Blogs', '', '{"route":"admin_default","module":"ynblog","controller":"level","action":"index"}', 'authorization_admin_level', '', 999),
('mobi_browse_ynblog', 'ynblog', 'Blogs', '', '{"route":"blog_general"}', 'mobi_browse', '', 3);
-- --------------------------------------------------------

DELETE FROM `engine4_core_menuitems` where `name` = 'ynblog_main_import';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynblog_main_import', 'ynblog', 'Import Blogs', 'YnBlog_Plugin_Menus::canCreateBlogs', '{"route":"blog_import","action":"import"}', 'ynblog_main', '', 4);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ('ynblog_main_export', 'ynblog', 'Export Blogs', 'YnBlog_Plugin_Menus::canExportBlogs','{"route":"blog_export","action":"index"}','ynblog_main','',5);

CREATE TABLE `engine4_blog_links` (
  `link_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `link_url` varchar(300) collate utf8_unicode_ci default NULL,
  `last_run` datetime default NULL,
  `cronjob_enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`link_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Support sub-category
--

ALTER TABLE  `engine4_blog_categories` ADD  `parent_id` INT( 10 ) NOT NULL DEFAULT  '0' AFTER `category_name` ,
ADD  `level` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `parent_id` ;

INSERT INTO `engine4_blog_categories` (`category_id`, `user_id`, `category_name`, `parent_id`, `level`) VALUES ('0', '1', 'All Categories', '-1', '0');
UPDATE `engine4_blog_categories` SET  `category_id` =  '0' WHERE `engine4_blog_categories`.`parent_id` = -1;

--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynblog_import', 'ynblog', '{item:$subject} imported a new blog entry:', 1, 5, 1, 3, 1, 1),
('ynblog_new', 'ynblog', '{item:$subject} wrote a new blog entry:', 1, 5, 1, 3, 1, 1),
('comment_ynblog', 'ynblog', '{item:$subject} commented on {item:$owner}''s {item:$object:blog entry}: {body:$body}', 1, 1, 1, 1, 1, 0),
('like_ynblog', 'ynblog', '{item:$subject} liked {item:$owner}''s {item:$object:blog entry}: {body:$body}', 1, 1, 1, 1, 1, 0);
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynblog_subscribed_new', 'ynblog', '{item:$subject} has posted a new blog entry: {item:$object}.', 0, ''),
('ynblog_approved', 'ynblog', 'Your entry {item:$object} has been approved.', 0, ''),
('ynblog_unapproved', 'ynblog', 'Your entry {item:$object} has been unapproved.', 0, '')
;
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynblog_subscribed_new', 'ynblog', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--
INSERT IGNORE INTO `engine4_core_settings`(`name`,`value`) VALUES
('ynblog.moderation','0'),
('ynblog.captcha','0');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_admin_blog_moderation', 'ynblog', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');
