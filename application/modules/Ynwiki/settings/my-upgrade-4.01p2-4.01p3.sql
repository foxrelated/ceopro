UPDATE `engine4_core_modules` SET `version` = '4.01p3' WHERE `name` = 'ynwiki';

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

UPDATE `engine4_activity_actiontypes`
SET body = '{item:$subject} has created a new wiki'
WHERE type = 'ynwiki_new';

