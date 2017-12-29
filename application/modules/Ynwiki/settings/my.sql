INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynwiki', 'YNC - Wiki', 'Wiki Plugin', '4.02p3', 1, 'extra') ;


-- Dumping structure for table engine4_ynwiki_follows
DROP TABLE IF EXISTS `engine4_ynwiki_follows`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_follows` (
  `follow_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `page_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`follow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `engine4_ynwiki_favourites`; 
 CREATE TABLE IF NOT EXISTS `engine4_ynwiki_favourites` (
  `favourite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `page_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`favourite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table engine4_ynwiki_pages
DROP TABLE IF EXISTS `engine4_ynwiki_pages`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_pages` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_page_id` int(11) unsigned NOT NULL DEFAULT '0',
  `revision_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'referal to ynwiki_revistions',
  `user_id` int(11) unsigned NOT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  `creator_id` int(11) unsigned NOT NULL,
  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `slug` varchar(256) NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `notify` tinyint(1) NOT NULL DEFAULT '1',
  `draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `follow_count` int(11) unsigned NOT NULL DEFAULT '0',
  `rate_ave` float(10,4) unsigned NOT NULL DEFAULT '0.0000',
  `favourite_count` int(11) unsigned NOT NULL DEFAULT '0',
  `rate_count` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_id`),
  KEY `parent_page_id` (`parent_page_id`),
  KEY `user_id` (`user_id`),
  KEY `slug` (`slug`),
  KEY `view_count` (`view_count`),
  KEY `comment_count` (`comment_count`),
  KEY `search` (`search`),
  KEY `draft` (`draft`),
  KEY `category_id` (`category_id`),
  KEY `follow_count` (`follow_count`),
  KEY `rate_ave` (`rate_ave`),
  KEY `favourite_count` (`favourite_count`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- Data exporting was unselected.


-- Dumping structure for table engine4_ynwiki_rates
DROP TABLE IF EXISTS `engine4_ynwiki_rates`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_rates` (
  `rate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `rate_number` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rate_id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


CREATE TABLE `engine4_ynwiki_views` (
 `view_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `page_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `creation_date` DATETIME NOT NULL,
 PRIMARY KEY (`view_id`),
 UNIQUE INDEX `user_id_page_id` (`user_id`, `page_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `engine4_ynwiki_edits` (
 `edit_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `page_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `creation_date` DATETIME NOT NULL,
 PRIMARY KEY (`edit_id`),
 UNIQUE INDEX `user_id_page_id` (`user_id`, `page_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

-- Dumping structure for table engine4_ynwiki_revisions
DROP TABLE IF EXISTS `engine4_ynwiki_revisions`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_revisions` (
  `revision_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(256) NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY `user_id` (`user_id`),
  KEY `creation_date` (`creation_date`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `engine4_ynwiki_attachments`; 
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_attachments` (
  `attachment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `file_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(256) NOT NULL DEFAULT '0',
  `description` varchar(256) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`attachment_id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE `engine4_ynwiki_faqs` (
    `faq_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('show','hide') NOT NULL DEFAULT 'hide',
    `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `owner_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `question` VARCHAR(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `creation_date` DATETIME NOT NULL,
    PRIMARY KEY (`faq_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;


CREATE TABLE `engine4_ynwiki_helppages` (
    `helppage_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('show','hide') NOT NULL,
    `ordering` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '999',
    `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `owner_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,                                                                                                                                                                                                                                         
    `creation_date` DATETIME NOT NULL,
    PRIMARY KEY (`helppage_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;

CREATE TABLE `engine4_ynwiki_permissions` (
 `permission_id` INT(11) UNSIGNED NOT NULL,
 `page_id` INT(11) UNSIGNED NOT NULL,
 `name` VARCHAR(50) NOT NULL,
 `excludes` TEXT NOT NULL,
 `includes` TEXT NOT NULL,
 PRIMARY KEY (`permission_id`),
 INDEX `page_id` (`page_id`),
 INDEX `page_id_name` (`page_id`, `name`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `engine4_ynwiki_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('ynwiki_main', 'standard', 'Wiki Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynwiki', 'ynwiki', 'Wiki', '', '{"route":"ynwiki_general","action":"browse","icon":"fa-file-text-o","icon_hover_action":"fa-file-text-o"}', 'core_main', '', 5),

('ynwiki_main_browse', 'ynwiki', 'Browse Wikis', '', '{"route":"ynwiki_general","action":"browse"}', 'ynwiki_main', '', 1),
('ynwiki_main_following', 'ynwiki', 'Following Wikis', 'Ynwiki_Plugin_Menus::canFollowWiki', '{"route":"ynwiki_general","action":"manage-follow"}', 'ynwiki_main', '', 3),
('ynwiki_main_favourite', 'ynwiki', 'Favourite Wikis', 'Ynwiki_Plugin_Menus::canFavouriteWiki', '{"route":"ynwiki_general","action":"manage-favourite"}', 'ynwiki_main', '', 4),
('ynwiki_main_createspace', 'ynwiki', 'Add A New Space', 'Ynwiki_Plugin_Menus::canCreateSpace', '{"route":"ynwiki_general","action":"create"}', 'ynwiki_main', '', 5),
('ynwiki_main_faqs', 'ynwiki', 'FAQs', 'Ynwiki_Plugin_Menus::canFaqs', '{"route":"ynwiki_extended","controller":"faqs"}', 'ynwiki_main', '', 6),
('ynwiki_main_helps', 'ynwiki', 'Help', 'Ynwiki_Plugin_Menus::canHelp', '{"route":"ynwiki_extended","controller":"help"}', 'ynwiki_main', '', 7),

('core_admin_main_plugins_ynwiki', 'ynwiki', 'YNC - Wiki', '', '{"route":"admin_default","module":"ynwiki","controller":"manage"}', 'core_admin_main_plugins', '', 999),
('ynwiki_admin_main_manage', 'ynwiki', 'Manage Pages', '', '{"route":"admin_default","module":"ynwiki","controller":"manage"}', 'ynwiki_admin_main', '', 14),
('ynwiki_admin_main_settings', 'ynwiki', 'Global Settings', '', '{"route":"admin_default","module":"ynwiki","controller":"settings"}', 'ynwiki_admin_main', '', 15),
('ynwiki_admin_main_level', 'ynwiki', 'Member Level Settings', '', '{"route":"admin_default","module":"ynwiki","controller":"level"}', 'ynwiki_admin_main', '', 16),
('ynwiki_admin_main_report', 'ynwiki', 'Manage Reports', '', '{"route":"admin_default","module":"ynwiki","controller":"report"}', 'ynwiki_admin_main', '', 17),
('ynwiki_admin_main_helps', 'ynwiki', 'Help', '', '{"route":"admin_default","module":"ynwiki","controller":"helps"}', 'ynwiki_admin_main', '', 18),
('ynwiki_admin_main_faqs', 'ynwiki', 'FAQs', '', '{"route":"admin_default","module":"ynwiki","controller":"faqs"}', 'ynwiki_admin_main', '', 19);


INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynwiki_new', 'ynwiki', '{item:$subject} has created a new wiki', 1, 5, 1, 3, 1, 1),
('ynwiki_update', 'ynwiki', '{item:$subject} has updated a wiki', 1, 5, 1, 3, 1, 1),
('ynwiki_move', 'ynwiki', '{item:$subject} has moved a wiki', 1, 5, 1, 3, 1, 1),
('comment_ynwiki', 'ynwiki', '{item:$subject} commented on {item:$owner}''s {item:$object:Page}: {body:$body}', 1, 1, 1, 1, 1, 0);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynwiki_update', 'ynwiki', '{item:$subject} has updated {item:$object:$label}.', 0, '', 1),
('ynwiki_move', 'ynwiki', '{item:$subject} has moved {item:$object:$label}.', 0, '', 1);


--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_restrict' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_edit' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_delete' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, table, tbody, tr, td, caption, iframe' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'restrict' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'createspase' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynwiki_admin_main_recycle', 'ynwiki', 'Recycle Bin', '', '{"route":"admin_default","module":"ynwiki","controller":"recycle"}', 'ynwiki_admin_main', '', 20);

DROP TABLE IF EXISTS `engine4_ynwiki_recycles`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_recycles` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_page_id` int(11) unsigned NOT NULL DEFAULT '0',
  `revision_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'referal to ynwiki_revistions',
  `user_id` int(11) unsigned NOT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  `creator_id` int(11) unsigned NOT NULL,
  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `slug` varchar(256) NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `notify` tinyint(1) NOT NULL DEFAULT '1',
  `draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `follow_count` int(11) unsigned NOT NULL DEFAULT '0',
  `rate_ave` float(10,4) unsigned NOT NULL DEFAULT '0.0000',
  `favourite_count` int(11) unsigned NOT NULL DEFAULT '0',
  `rate_count` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_id`),
  KEY `parent_page_id` (`parent_page_id`),
  KEY `user_id` (`user_id`),
  KEY `slug` (`slug`),
  KEY `view_count` (`view_count`),
  KEY `comment_count` (`comment_count`),
  KEY `search` (`search`),
  KEY `draft` (`draft`),
  KEY `category_id` (`category_id`),
  KEY `follow_count` (`follow_count`),
  KEY `rate_ave` (`rate_ave`),
  KEY `favourite_count` (`favourite_count`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- update 4.01p3
UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_view';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_comment';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_restrict';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_edit';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_delete';



DROP TABLE IF EXISTS `engine4_ynwiki_membership`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `resource_approved` tinyint(1) NOT NULL default '0',
  `user_approved` tinyint(1) NOT NULL default '0',
  `message` text NULL,
  `title` text NULL,
  `rejected_ignored` tinyint(1) NOT NULL DEFAULT '0',  
  PRIMARY KEY  (`resource_id`, `user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

DROP TABLE IF EXISTS `engine4_ynwiki_lists`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_lists` (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL default '',
  `owner_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`list_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

ALTER TABLE `engine4_ynwiki_pages` ADD COLUMN `approval` tinyint(1) NOT NULL DEFAULT '0' AFTER parent_id;

DROP TABLE IF EXISTS `engine4_ynwiki_listitems`;
CREATE TABLE IF NOT EXISTS `engine4_ynwiki_listitems` (
  `listitem_id` int(11) unsigned NOT NULL auto_increment,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`listitem_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO  `engine4_core_pages` (`name`, `displayname`, `title` ,`description`, `custom`) VALUES (
 'ynwiki_index_view',  'Wikis Profile', 'Wikis Profile',  'This is Wikis Profile page.', '1');
 
 
 INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynwiki_invite', 'ynwiki', '{item:$subject} has invited you to the wiki {item:$object}.', 1, 'ynwiki.widget.request-wiki'),
('ynwiki_approve', 'ynwiki', '{item:$subject} has requested to join the wiki {item:$object}.', 0, ''),
('ynwiki_accepted', 'ynwiki', 'Your request to join the wiki {item:$subject} has been approved.', 0, ''),
('ynwiki_promote', 'ynwiki', 'You were promoted to officer in the wiki space {item:$object}.', 0, ''),
('ynwiki_cancel_invite', 'ynwiki' , 'The wiki {item:$subject} invitation has been cancel, please contact wiki owner for more information.',0,'')
;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynwiki_join', 'ynwiki', '{item:$subject} joined the wiki {item:$object}', 1, 3, 1, 1, 1, 1)
;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_file' as `name`,
    3 as `value`,
    'zip, rar, pdf, txt, html, php, tpl, doc, docx, xls, xlsx, pptx, exe, gif, png, jpg, jpeg, mp3, wav, xmind, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

UPDATE `engine4_core_content` SET  `params` =  '{"title":"Wiki Profile Detail"}' WHERE `engine4_core_content`.`name` = 'ynwiki.profile-detail';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('ynwiki_main', 'standard', 'Wiki Main Navigation Menu');