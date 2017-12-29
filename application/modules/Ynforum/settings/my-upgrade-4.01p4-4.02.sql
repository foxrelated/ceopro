--
-- Table structure for table `engine4_ynforum_topic_ratings`
--
DROP TABLE IF EXISTS `engine4_ynforum_topic_ratings`;
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

ALTER TABLE `engine4_forum_topicviews` ADD `last_post_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `topic_id` ;

DROP TABLE IF EXISTS `engine4_ynforum_userviews`;
CREATE TABLE IF NOT EXISTS `engine4_ynforum_userviews` (
  `userview_id` int(11) unsigned NOT NULL auto_increment,
  `owner_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userview_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
