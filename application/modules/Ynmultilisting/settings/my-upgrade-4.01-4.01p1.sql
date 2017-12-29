UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `name` = 'ynmultilisting';
--
-- Table structure for table `engine4_ynmultilisting_moduleimports`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_moduleimports` (
  `moduleimport_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creation_date` datetime NOT NULL,
  `module_id` int(11) NULL,
  `owner_id` int( 11 ) NULL,
  `item_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  PRIMARY KEY (`moduleimport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

UPDATE `engine4_activity_notificationtypes` SET `body` = '{item:$subject} is following your listing.' WHERE `engine4_activity_notificationtypes`.`type` = 'ynmultilisting_listing_follow_owner';
