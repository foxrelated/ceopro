INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynforum', 'ynforum', 'Forum', '', '{"route":"ynforum_general","module":"ynforum","icon":"fa-inbox"}', 'core_main', '', 999),
('core_sitemap_ynforum', 'ynforum', 'Forum', '', '{"route":"ynforum_general"}', 'core_sitemap', '', 999),
('core_admin_main_plugins_ynforum', 'ynforum', 'YNC - Advanced Forum', '', '{"route":"ynforum_admin_default"}', 'core_admin_main_plugins', '', 999),
('ynforum_admin_main_manage', 'ynforum', 'Manage Forums', '', '{"route":"admin_default","module":"ynforum","controller":"manage"}', 'ynforum_admin_main', '', 1),
('ynforum_admin_main_settings', 'ynforum', 'Global Settings', '', '{"route":"admin_default","module":"ynforum","controller":"settings"}', 'ynforum_admin_main', '', 2),
('ynforum_admin_main_level', 'ynforum', 'Member Level Settings', '', '{"route":"admin_default","module":"ynforum","controller":"level"}', 'ynforum_admin_main', '', 3),
('authorization_admin_level_ynforum', 'ynforum', 'Forums', '', '{"route":"admin_default","module":"ynforum","controller":"level","action":"index"}', 'authorization_admin_level', '', 999),
('mobi_browse_ynforum', 'ynforum', 'Forum', '', '{"route":"ynforum_general"}', 'mobi_browse', '', 999);

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynforum', 'Advanced Forum', 'Advanced Forum', '4.04p1', 1, 'extra') ;

ALTER TABLE `engine4_forum_categories` 
ADD `level` INT NULL DEFAULT '0',
ADD `photo_id` INT NULL ,
ADD `owner_id` INT(11) UNSIGNED ,
ADD `parent_category_id` INT NULL;

CREATE TABLE IF NOT EXISTS `engine4_forum_category_lists` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `child_count` int(11) NOT NULL,
  PRIMARY KEY (`list_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `engine4_forum_category_listitems` (
  `listitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  PRIMARY KEY (`listitem_id`),
  KEY `list_id` (`list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_forum_forums` 
ADD `approved_topic_count` INT NOT NULL DEFAULT '0',
ADD `approved_post_count` INT NOT NULL DEFAULT '0',
ADD `parent_forum_id` INT NULL,
ADD `photo_id` INT NOT NULL DEFAULT '0',
ADD `level` INT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `engine4_forum_photos` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_forum_posts` 
ADD `approved` TINYINT(1) NOT NULL DEFAULT '1',
ADD `thanked_count` INT NOT NULL DEFAULT '0';

ALTER TABLE `engine4_forum_topics` 
ADD `approved_post_count` int(11) NOT NULL DEFAULT '0',
ADD `approved` TINYINT(1) NOT NULL DEFAULT '1',
ADD `firstpost_id` INT NULL;

CREATE TABLE IF NOT EXISTS `engine4_forum_reputations` (
  `reputation_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`reputation_id`),
  KEY `post_id` (`post_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_forum_signatures` 
ADD `approved_post_count` INT NULL DEFAULT '0',
ADD `thanks_count` INT NULL DEFAULT '0',
ADD `thanked_count` INT NULL DEFAULT '0',
ADD `reputation` INT NULL DEFAULT '0',
ADD `signature` TEXT NULL;


CREATE TABLE IF NOT EXISTS `engine4_forum_thanks` (
  `thank_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`thank_id`),
  KEY `subject_id` (`user_id`,`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_forum_forumwatches` (
  `forum_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `watch` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`forum_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.approve' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.close' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.move' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.sticky' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

--
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.approve' as `name`,
    4 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.close' as `name`,
    4 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.delete' as `name`,
    4 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.edit' as `name`,
    4 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.move' as `name`,
    4 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'yntopic.sticky' as `name`,
    4 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynforum_promote', 'ynforum', '{item:$subject} has been made a moderator for the forum {item:$object}', 1, 5, 1, 1, 1, 1),
('ynforum_topic_create', 'ynforum', '{item:$subject} posted a {item:$object:topic} in the forum {itemParent:$object:forum}: {body:$body}', 1, 5, 1, 1, 1, 1),
('ynforum_topic_reply', 'ynforum', '{item:$subject} replied to a {item:$object:topic} in the forum {itemParent:$object:forum}: {body:$body}', 1, 5, 1, 1, 1, 1),
('ynforum_post_thank', 'ynforum', '{item:$subject} thanked to a post in the  {item:$object:topic} in the {itemParent:$object:forum}: {body:$body}', 1, 5, 1, 1, 1, 1)
;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynforum_owner_post_approved', 'ynforum', 'Your {item:$object:post} on a {itemParent:$object::forum topic} has just approved.', 0, ''),
('ynforum_post_wait_approval', 'ynforum', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::forum topic} and waited for your approval.', 0, ''),
('ynforum_promote', 'ynforum', 'You were promoted to moderator in the forum {item:$object}.', 0, ''),
('ynforum_topic_reply', 'ynforum', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::forum topic} you posted on.', 0, ''),
('ynforum_topic_response', 'ynforum', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::forum topic} you created.', 0, ''),
('ynforum_topic_thank', 'ynforum', '{item:$subject} thank you for your {item:$object:post} on the {itemParent:$object::forum topic}.', 0, ''),
('ynforum_topic_reputation', 'ynforum', '{item:$subject} added reputation for your {item:$object:post} on the {itemParent:$object::forum topic}.', 0, ''),
('ynforum_topic_create', 'ynforum', '{item:$subject} has created a {item:$object:topic} on the {itemParent:$object::forum} you are watching.', 0, ''),
('ynforum_topic_reply_forum_watch', 'ynforum', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::forum topic}.', 0, '')
;

--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynforum_owner_post_approved', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_post_wait_approval', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_promote', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_reply', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_create', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_reputation', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_response', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_thank', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynforum_topic_reply_forum_watch', 'ynforum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]')
;


--
-- Add the menu item on the menu inside the user setting page 
--
INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) 
VALUES ('user_settings_signature', 'ynforum', 'Forum Signature', '', '{"route":"default", "module":"ynforum", "controller":"index", "action":"signature"}', 'user_settings', '', 1, 0, 999);


--
-- Table structure for table `engine4_ynforum_topic_ratings`
--

CREATE TABLE `engine4_ynforum_topic_ratings` (
  `topicrating_id` int(11) unsigned NOT NULL auto_increment,
  `topic_id` int(11) unsigned NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `rate_number` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`topicrating_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_forum_signatures` ADD `positive` INT( 11 ) NOT NULL DEFAULT '0' AFTER `reputation` ,
ADD `neg_positive` INT( 11 ) NOT NULL DEFAULT '0' AFTER `positive`;

DROP TABLE IF EXISTS `engine4_ynforum_attachments`; 
CREATE TABLE IF NOT EXISTS `engine4_ynforum_attachments` (
  `attachment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `file_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(256) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`attachment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `engine4_forum_topicviews` ADD `last_post_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `topic_id`;


DROP TABLE IF EXISTS `engine4_ynforum_userviews`;
CREATE TABLE IF NOT EXISTS `engine4_ynforum_userviews` (
  `userview_id` int(11) unsigned NOT NULL auto_increment,
  `owner_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userview_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynforum_admin_main_manage_posts', 'ynforum', 'Manage Posts', '', '{"route":"admin_default","module":"ynforum","controller":"manage", "action":"posts"}', 'ynforum_admin_main', '', 4),
('ynforum_admin_main_manage_reports', 'ynforum', 'Manage Abuse Reports', '', '{"route":"admin_default","module":"ynforum","controller":"manage", "action":"reports"}', 'ynforum_admin_main', '', 5),
('ynforum_admin_main_manage_icons', 'ynforum', 'Manage icons', '', '{"route":"admin_default","module":"ynforum","controller":"manage", "action":"icons"}', 'ynforum_admin_main', '', 6);

CREATE TABLE IF NOT EXISTS `engine4_ynforum_icons` (
  `icon_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo_id` int(11) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`icon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `engine4_ynforum_announcements` (
  `announcement_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `forum_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `highlight` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_forum_memberlevelpermission` (
  `level_id` int(11) unsigned NOT NULL,
  `type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` tinyint(3) NOT NULL DEFAULT '0',  
  `forum_id` int(11) NOT NULL,  
  PRIMARY KEY (`forum_id`,`level_id`,`type`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fevent.create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fevent.edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');
    
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fevent.delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fgroup.create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fgroup.edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fgroup.delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fpoll.create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fpoll.edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'fpoll.delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'ynannoun.create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'ynannoun.edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'forum' as `type`,
    'ynannoun.delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');  


CREATE TABLE IF NOT EXISTS`engine4_ynforum_postalbums` (
  `postalbum_id` int(11) unsigned NOT NULL auto_increment,
  `post_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `postphoto_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`postalbum_id`),
  KEY `FK_engine4_ynforum_postalbums_engine4_ynforum_posts` (`post_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS`engine4_ynforum_postphotos` (
  `postphoto_id` int(11) unsigned NOT NULL auto_increment,
  `post_id` int(11) unsigned NOT NULL,
  `postalbum_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `image_title` varchar(128) NOT NULL,
  `image_description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `parent_type` varchar(64) DEFAULT 'forum_post',
  PRIMARY KEY  (`postphoto_id`),
  KEY `FK_engine4_ynforum_postphotos_engine4_ynforum_posts` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynforum_highlights` (
  `highlight_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `forum_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `highlight` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`highlight_id`, `forum_id`, `item_id`),
  KEY `user_id` (`forum_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_forum_topics` 
ADD `icon_id` INT(11) DEFAULT 0;
 
ALTER TABLE `engine4_forum_posts` 
ADD `icon_id` INT(11) DEFAULT 0,
ADD `title` varchar(128) DEFAULT NULL,
ADD `photo_id` int(11) unsigned NOT NULL default '0';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynforum_category' as `type`,
    'forumcat.view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'ynforum_category' as `resource_type`,
    `category_id` as `resource_id`,
    'forumcat.view' as `action`,
    'everyone' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_forum_categories`;
