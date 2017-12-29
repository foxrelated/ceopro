--
-- Table structure for table `engine4_ynadvsearch_contenttypes`
--
CREATE TABLE IF NOT EXISTS `engine4_ynadvsearch_contenttypes` (
	`contenttype_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`module` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`title` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`photo_id` int(11) unsigned NOT NULL DEFAULT '0',
	`show` tinyint(1) NOT NULL DEFAULT '1',
	`search` tinyint(1) NOT NULL DEFAULT '1',
	`original_style` tinyint(1) NOT NULL DEFAULT '0',
	`order` int(11) NOT NULL DEFAULT '99',
	PRIMARY KEY (`contenttype_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynadvsearch_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynadvsearch_faqs` (
  `faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynadvsearch_keywords`
--

CREATE TABLE IF NOT EXISTS `engine4_ynadvsearch_keywords` (
  `keyword_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL DEFAULT 1,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`keyword_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynadvsearch_main', 'standard', 'YN Advanced Search Main Navigation Menu', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynadvsearch', 'ynadvsearch', 'Advanced Search', '', '{"route":"ynadvsearch_search"}', 'core_main', '', 999),

('ynadvsearch_admin_main_global', 'ynadvsearch', 'Global Settings', '', '{"route":"admin_default","module":"ynadvsearch","controller":"settings","action":"global"}', 'ynadvsearch_admin_main', '', '1'),
('ynadvsearch_admin_main_contenttypes', 'ynadvsearch', 'Manage Content Types', '', '{"route":"admin_default","module":"ynadvsearch","controller":"content-types", "action":"index"}', 'ynadvsearch_admin_main', '', 2),
('ynadvsearch_admin_main_settings', 'ynadvsearch', 'Manage Plug-ins', '', '{"route":"admin_default","module":"ynadvsearch","controller":"settings", "action":"index"}', 'ynadvsearch_admin_main', '', 3),
('ynadvsearch_admin_main_faqs', 'ynadvsearch', 'Manage FAQs', '', '{"route":"admin_default","module":"ynadvsearch","controller":"faqs", "action":"index"}', 'ynadvsearch_admin_main', '', 4),

('ynadvsearch_main_search', 'ynadvsearch', 'Search Page', '', '{"route":"ynadvsearch_search","module":"ynadvsearch","controller":"search","action":"index"}', 'ynadvsearch_main', '', 1),
('ynadvsearch_main_faqs', 'ynadvsearch', 'FAQs', '', '{"route":"ynadvsearch_faqs","module":"ynadvsearch","controller":"faqs","action":"index"}', 'ynadvsearch_main', '', 2);


INSERT INTO `engine4_ynadvsearch_contenttypes` (`type`, `module`, `title`, `show`, `search`, `order`) VALUES
('user', 'Members', 'Users', 1, 1, 0),
('event', 'Events', 'Events', 1, 1, 1),
('album', 'Albums', 'Albums', 1, 1, 2),
('group', 'Group', 'Groups', 1, 1, 3);

UPDATE `engine4_core_menuitems` SET  `label` = 'Manage Plugins' WHERE `engine4_core_menuitems`.`name` = 'ynadvsearch_admin_main_settings';
