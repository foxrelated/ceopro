UPDATE `engine4_core_modules` SET `version` = '4.01p2' WHERE `name` = 'ynwiki';

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