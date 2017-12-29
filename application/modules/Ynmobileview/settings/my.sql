
--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('ynmobileview', 'Mobile View', 'Mobile View', '4.02', 1, 'extra');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ynmobileview_profile', 'standard', 'YouNet Mobile Profile Options Menu'),
('ynmobileview_group', 'standard', 'YouNet Mobile Group Options Menu'),
('ynmobileview_event', 'standard', 'YouNet Mobile Event Options Menu')
;

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` 
(`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobileview_profile_message', 'ynmobileview', 'Send Message', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_profile', '', 1),
('ynmobileview_profile_friend', 'ynmobileview', 'Friends', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_profile', '', 2),
('ynmobileview_group_join', 'ynmobileview', 'Join Group', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_group', '', 1),
('ynmobileview_group_share', 'ynmobileview', 'Share Group', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_group', '', 2),
('ynmobileview_event_join', 'ynmobileview', 'Join Event', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_event', '', 1),
('ynmobileview_event_delete', 'ynmobileview', 'Delete Event', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_event', '', 2),
('ynmobileview_event_share', 'ynmobileview', 'Share This Event', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_event', '', 3),
('core_footer_ynmobile', 'ynmobileview', 'Mobile Site', 'Ynmobileview_Plugin_Menus', '', 'core_footer', '', 4),
('core_admin_main_plugins_ynmobileview', 'ynmobileview', 'YouNet Mobile View', '', '{"route":"admin_default","module":"ynmobileview","controller":"styles"}', 'core_admin_main_plugins', '', 999),
('ynmobileview_admin_main_styles', 'ynmobileview', 'Manage Mobile Custom Styles', '', '{"route":"admin_default","module":"ynmobileview","controller":"styles"}', 'ynmobileview_admin_main', '', 1);

DROP TABLE IF EXISTS `engine4_ynmobileview_styles`;
CREATE TABLE IF NOT EXISTS `engine4_ynmobileview_styles` (
  `style_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(128) NOT NULL,
  `css_obj` text,
  `css` text,
  `active` tinyint(1) unsigned default 0,
  PRIMARY KEY (`style_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_core_menuitems` 
(`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobileview_admin_main_menus', 'ynmobileview', 'Manage Menus', '', '{"route":"admin_default","module":"ynmobileview","controller":"menus"}', 'ynmobileview_admin_main', '', 2);

CREATE TABLE IF NOT EXISTS `engine4_ynmobileview_menuitems` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) character set latin1 collate latin1_general_ci NOT NULL,
  `module` varchar(32) character set latin1 collate latin1_general_ci NOT NULL,
  `label` varchar(32) collate utf8_unicode_ci NOT NULL,
  `plugin` varchar(128) character set latin1 collate latin1_general_ci default NULL,
  `params` text collate utf8_unicode_ci NOT NULL,
  `menu` VARCHAR( 32 ) character set latin1 collate latin1_general_ci default NULL,
  `submenu` varchar(32) character set latin1 collate latin1_general_ci default NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  `custom` tinyint(1) NOT NULL default '0',
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `LOOKUP` (`name`,`order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynmobileview_menuitems`
--

INSERT IGNORE INTO `engine4_ynmobileview_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)  SELECT `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order` FROM engine4_core_menuitems WHERE menu = 'core_main';

INSERT IGNORE INTO `engine4_core_menuitems` 
(`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobileview_profile_edit', 'ynmobileview', 'Update Info', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_profile', '', 3),
('ynmobileview_profile_cover', 'ynmobileview', 'Edit Cover', 'Ynmobileview_Plugin_Menus', '', 'ynmobileview_profile', '', 4);