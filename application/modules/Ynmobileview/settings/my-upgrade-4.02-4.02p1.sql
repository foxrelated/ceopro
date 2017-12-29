INSERT IGNORE INTO `engine4_core_menuitems` 
(`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobileview_admin_main_menus', 'ynmobileview', 'Manage Menus', '', '{"route":"admin_default","module":"ynmobileview","controller":"menus"}', 'ynmobileview_admin_main', '', 2);
UPDATE `engine4_core_menuitems` SET  `module` =  'ynmobileview' WHERE  `engine4_core_menuitems`.`name` = 'ynmobileview_admin_main_styles';
UPDATE `engine4_core_modules` SET `version` = '4.02p1' where 'name' = 'ynmobileview';
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