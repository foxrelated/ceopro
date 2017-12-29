-- --------------------------------------------------------

--
-- Change table permissions (change length of column type)
--

ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationsettings` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmultilisting_usefuls`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_usefuls` (
  `useful_id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`useful_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_types`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_listingtypes` (
`listingtype_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(128),
`show` tinyint(1) NOT NULL DEFAULT '1',
`creation_date` datetime NOT NULL,
`list_view` varchar(8),
`grid_view` varchar(8),
`pin_view` varchar(8),
`feature_widget` varchar(8) DEFAULT '0',
`category_widget` varchar(8) DEFAULT '0',
`most_viewed_widget` varchar(8) DEFAULT '0',
`most_liked_widget` varchar(8) DEFAULT '0',
`most_discussed_widget` varchar(8) DEFAULT '0',
`most_commented_widget` varchar(8) DEFAULT '0',
`manage_menu` tinyint(1) NOT NULL DEFAULT '0',
`order` smallint(6) NOT NULL DEFAULT '999',
PRIMARY KEY (`listingtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_transactions` (
`transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`payment_transaction_id` varchar(128),
`creation_date` datetime NOT NULL,
`status` enum('initialized','expired','pending','completed','canceled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`description` text NOT NULL,
`item_id` int(11) NULL,
`gateway_id` int(11) NOT NULL,
`amount` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`user_id` int(11) NOT NULL,
PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_orders` (
`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`gateway_id` int(11) unsigned NOT NULL,
`gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
`status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
`creation_date` datetime NOT NULL,
`payment_date` datetime DEFAULT NULL,
`package_id` int(11) unsigned NOT NULL DEFAULT '0',
`item_id` int(11) unsigned NOT NULL DEFAULT '0',
`price` decimal(16,2) NOT NULL DEFAULT '0',
`featured` tinyint(1) NOT NULL DEFAULT '0',
`feature_day_number` int(11) unsigned NOT NULL DEFAULT '0',
`currency` char(3),
PRIMARY KEY (`order_id`),
KEY `user_id` (`user_id`),
KEY `gateway_id` (`gateway_id`),
KEY `state` (`status`),
KEY `package_id` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `engine4_ynmultilisting_listings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_listings` (
`listing_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(128) NOT NULL,
`user_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL DEFAULT 0,
`package_id` int(11) unsigned NULL,
`listingtype_id` int(11) NOT NULL,
`rating` int(11) NOT NULL DEFAULT 0,
`theme` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`last_payment_date` datetime NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NULL,
`approved_date` datetime NULL,
`approved` tinyint(1) NOT NULL default '0',
`expiration_date` datetime DEFAULT NULL,
`end_date` datetime NULL,
`approved_status` enum('pending','approved','denied') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
`status` enum('closed','open','draft','expired') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`deleted` tinyint(1) NOT NULL DEFAULT '0',
`featured` BOOLEAN NOT NULL DEFAULT 0,
`feature_expiration_date` datetime DEFAULT NULL,
`highlight` BOOLEAN NOT NULL DEFAULT 0,
`location` text COLLATE utf8_unicode_ci NOT NULL,
`longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`short_description` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
`description` text COLLATE utf8_unicode_ci NOT NULL,
`about_us` text COLLATE utf8_unicode_ci NOT NULL,
`photo_id` int(11),
`video_id` int(11),
`video_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci default NULL,
`price` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`search` tinyint(1) NOT NULL DEFAULT '1',
`view_count` int(11) NOT NULL DEFAULT 0,
`like_count` int(11) NOT NULL DEFAULT 0,
`comment_count` int(11) NOT NULL DEFAULT 0,
`review_count` int(11) NOT NULL DEFAULT 0,
`discussion_count` int(11) NOT NULL DEFAULT 0,
`view_time` datetime NOT NULL,
PRIMARY KEY (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_listing_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_listing_fields_maps` (
`field_id` int(11) unsigned NOT NULL,
`option_id` int(11) unsigned NOT NULL,
`child_id` int(11) unsigned NOT NULL,
`order` smallint(6) NOT NULL,
PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynmultilisting_listing_fields_maps`
--

INSERT IGNORE INTO `engine4_ynmultilisting_listing_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_listing_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_listing_fields_meta` (
`field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
`label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
`description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
`alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
`required` tinyint(1) NOT NULL DEFAULT '0',
`display` tinyint(1) unsigned NOT NULL,
`publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
`search` tinyint(1) unsigned NOT NULL DEFAULT '0',
`order` smallint(3) unsigned NOT NULL DEFAULT '999',
`config` text COLLATE utf8_unicode_ci,
`validators` text COLLATE utf8_unicode_ci,
`filters` text COLLATE utf8_unicode_ci,
`style` text COLLATE utf8_unicode_ci,
`error` text COLLATE utf8_unicode_ci,
PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynmultilisting_listing_fields_meta`
--

INSERT IGNORE INTO `engine4_ynmultilisting_listing_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_listing_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_listing_fields_search` (
`item_id` int(11) unsigned NOT NULL,
`profile_type` enum('1','4') COLLATE utf8_unicode_ci DEFAULT NULL,
`first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
`last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
`gender` smallint(6) unsigned DEFAULT NULL,
`birthdate` date DEFAULT NULL,
PRIMARY KEY (`item_id`),
KEY `first_name` (`first_name`),
KEY `last_name` (`last_name`),
KEY `gender` (`gender`),
KEY `birthdate` (`birthdate`),
KEY `profile_type` (`profile_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_listing_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_listing_fields_values` (
`item_id` int(11) unsigned NOT NULL,
`field_id` int(11) unsigned NOT NULL,
`index` smallint(3) unsigned NOT NULL DEFAULT '0',
`value` text COLLATE utf8_unicode_ci NOT NULL,
`privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_listing_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_listing_fields_options` (
`option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`field_id` int(11) unsigned NOT NULL,
`label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`order` smallint(6) NOT NULL DEFAULT '999',
PRIMARY KEY (`option_id`),
KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_categories` (
`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`listingtype_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`parent_id` int(11) unsigned DEFAULT NULL,
`pleft` int(11) unsigned NOT NULL,
`pright` int(11) unsigned NOT NULL,
`level` int(11) unsigned NOT NULL DEFAULT '0',
`title` varchar(64) NOT NULL COLLATE utf8_unicode_ci NOT NULL,
`description` text NULL,
`photo_id` int(11) DEFAULT '0',
`image_id` int(11) DEFAULT '0',
`order` smallint(6) NOT NULL DEFAULT '0',
`option_id` int(11) NOT NULL,
`top_category` tinyint(1) NOT NULL DEFAULT '0',
`more_category` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`category_id`),
KEY `user_id` (`user_id`),
KEY `parent_id` (`parent_id`),
KEY `pleft` (`pleft`),
KEY `pright` (`pright`),
KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynmultilisting_categories`
--

INSERT IGNORE INTO `engine4_ynmultilisting_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Categories','0');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_ratingtypes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_ratingtypes` (
  `ratingtype_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `title` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ratingtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_ratingvalues`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_ratingvalues` (
  `ratingvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `ratingtype_id` int(11) unsigned NOT NULL,
  `rating` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`ratingvalue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_reviewtypes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_reviewtypes` (
  `reviewtype_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `title` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`reviewtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_reviewvalues`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_reviewvalues` (
  `reviewvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `reviewtype_id` int(11) unsigned NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`reviewvalue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_reviews`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `pros` text COLLATE utf8_unicode_ci NOT NULL,
  `cons` text COLLATE utf8_unicode_ci NOT NULL,
  `overal_rating` int(11) unsigned NOT NULL,
  `overal_review` longtext COLLATE utf8_unicode_ci NOT NULL,
  `like_count` int(11) NOT NULL DEFAULT 0,
  `comment_count` int(11) NOT NULL DEFAULT 0,
  `helpful_count` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`review_id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_packages`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_packages` (
`package_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(128) NOT NULL,
`themes` text NOT NULL,
`price` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`valid_amount` int(11) unsigned,
`valid_period` ENUM('day') NOT NULL,
`description` text,
`show` tinyint(1) NOT NULL DEFAULT '1',
`user_id` int(11) NOT NULL,
`max_photos` int(11) NOT NULL DEFAULT '1',
`max_videos` int(11) NOT NULL DEFAULT '1',
`allow_photo_tab` BOOLEAN NOT NULL DEFAULT 0,
`allow_video_tab` BOOLEAN NOT NULL DEFAULT 0,
`allow_discussion_tab` BOOLEAN NOT NULL DEFAULT 0,
`deleted` BOOLEAN NOT NULL DEFAULT 0,
`order` int(11) NOT NULL DEFAULT  '999',
PRIMARY KEY (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_transactions` (
`transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`payment_transaction_id` varchar(128),
`creation_date` datetime NOT NULL,
`status` enum('initialized','expired','pending','completed','canceled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`description` text NOT NULL,
`item_id` int(11) NULL,
`gateway_id` int(11) NOT NULL,
`amount` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`user_id` int(11) NOT NULL,
PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_orders` (
`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`gateway_id` int(11) unsigned NOT NULL,
`gateway_transaction_id` varchar(128) DEFAULT NULL,
`status` enum('pending','completed','cancelled','failed') NOT NULL DEFAULT 'pending',
`creation_date` datetime NOT NULL,
`payment_date` datetime DEFAULT NULL,
`package_id` int(11) unsigned NOT NULL DEFAULT '0',
`item_id` int(11) unsigned NOT NULL DEFAULT '0',
`price` decimal(16,2) NOT NULL DEFAULT '0',
`featured` tinyint(1) NOT NULL DEFAULT '0',
`feature_day_number` int(11) unsigned NOT NULL DEFAULT '0',
`currency` char(3),
PRIMARY KEY (`order_id`),
KEY `user_id` (`user_id`),
KEY `gateway_id` (`gateway_id`),
KEY `state` (`status`),
KEY `package_id` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmultilisting_follows`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_follows` (
  `follow_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`follow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_sentlistings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_sentlistings` (
  `sentlisting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) unsigned NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`sentlisting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_subscribers`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_subscribers` (
  `subscriber_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `within` int(11) NULL,
  `email` text NOT NULL,
  `ip` varbinary(16),
  PRIMARY KEY (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_albums`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  `collectible_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_photos`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) unsigned NOT NULL,
  `album_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `image_title` varchar(128) NOT NULL,
  `image_description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `like_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_mappings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_mappings` (
  `mapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`mapping_id`,`listing_id`,`item_id`),
  KEY `user_id` (`listing_id`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmultilisting_comparisons`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_comparisons` (
`comparison_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`category_id` int(11) unsigned NOT NULL,
`common_fields` text NULL,
`custom_fields` text NULL,
`rating_fields` text NULL,
`review_fields` text NULL,
PRIMARY KEY (`comparison_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmultilisting_promotions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_promotions` (
`promotion_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`listingtype_id` int(11) unsigned NOT NULL,
`title` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
`content` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
`photo_id` int(11) unsigned NOT NULL DEFAULT '0',
`text_color` varchar(32) NOT NULL,
`text_background_color` varchar(32) NOT NULL,
`link` text NOT NULL,
PRIMARY KEY (`promotion_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmultilisting_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_faqs` (
`faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`order` int(11) NOT NULL DEFAULT  '999',
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_modules`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_modules` (
`module_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`table_item` varchar(64) NOT NULL,
`owner_id_column` varchar(64) NOT NULL,
`title_column` varchar(64) NOT NULL,
`short_description_column` varchar(64) NOT NULL,
`description_column` varchar(64) NOT NULL,
`photo_id_column` varchar(64) NULL,
`about_us_column` varchar(64) NOT NULL,
`price_column` varchar(64) NOT NULL,
`currency_column` varchar(64) NULL,
`location_column` varchar(64) NULL,
`long_column` varchar(64) NULL,
`lat_column` varchar(64) NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`module_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_quicklinks`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_quicklinks` (
`quicklink_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`listingtype_id` int(11) unsigned NOT NULL,
`title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`category_ids` text NULL,
`price` text NULL,
`location` text COLLATE utf8_unicode_ci NULL,
`longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`radius` int(11) unsigned NULL,
`expire_from` datetime NULL,
`expire_to` datetime NULL,
`owner_ids` text NULL,
`listing_ids` text NULL,
`show` tinyint(1) NOT NULL DEFAULT '1', 
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`quicklink_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_wishlists`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_wishlists` (
`wishlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`listingtype_id` int(11) unsigned NOT NULL,
`title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
`user_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`wishlist_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_wishlistlistings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_wishlistlistings` (
`wishlistlisting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`wishlist_id` int(11) unsigned NOT NULL,
`listing_id` int(11) unsigned NOT NULL,
PRIMARY KEY (`wishlistlisting_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_editors`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_editors` (
`editor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`listingtype_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
PRIMARY KEY (`editor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynmultilisting_reports`
--
CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`),
  KEY `listing_id` (`listing_id`),
  KEY `topic_id` (`topic_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_posts`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_posts` (
  `post_id` int(11) unsigned NOT NULL auto_increment,
  `topic_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_topics`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_topics` (
  `topic_id` int(11) unsigned NOT NULL auto_increment,
  `listing_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `sticky` tinyint(1) NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  `post_count` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `lastpost_id` int(11) unsigned NOT NULL default '0',
  `lastposter_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_topicwatches`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_topicwatches` (
  `resource_id` int(10) unsigned NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `watch` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`resource_id`,`topic_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_features`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_features` (
`feature_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`listing_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
`modified_date` datetime DEFAULT NULL,
`expiration_date` datetime DEFAULT NULL,
`feature_day_number` int (11) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_mailtemplates`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_mailtemplates` (
`mailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`type` varchar(255) NOT NULL,
`vars` varchar(255) NOT NULL,
PRIMARY KEY (`mailtemplate_id`),
UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `engine4_ynmultilisting_mailtemplates`
--

INSERT IGNORE INTO `engine4_ynmultilisting_mailtemplates` (`type`, `vars`) VALUES
('ynmultilisting_subscribe_listing', ''),
('ynmultilisting_listing_created', '[website_name],[website_link],[listing_name],[listing_link]'),
('ynmultilisting_listing_approved', '[website_name],[website_link],[listing_name],[listing_link]'),
('ynmultilisting_listing_expired', '[website_name],[website_link],[listing_name],[listing_link]'),
('ynmultilisting_listing_reviewed', '[website_name],[website_link],[listing_name],[listing_link]')
;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_memberlevelpermission`
--
CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_memberlevelpermission` (
  `level_id` int(11) unsigned NOT NULL,
  `type` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text NOT NULL,  
  `listingtype_id` int(11) NOT NULL,  
  PRIMARY KEY (`listingtype_id`,`level_id`,`type`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_imports`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_imports` (
  `import_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creation_date` datetime NOT NULL,
  `module_id` int(11) NULL,
  `file_name` text,
  `number_listings` text,
  `list_listings` text,
  PRIMARY KEY (`import_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

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

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynmultilisting_layout_proxies`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmultilisting_layout_proxies` (
`proxy_id` INT(11) NOT NULL AUTO_INCREMENT,
`page_id` INT(11) NOT NULL,
`page_name` VARCHAR(128) NOT NULL COLLATE 'latin1_general_ci',
`subject_type` VARCHAR(64) NOT NULL COLLATE 'latin1_general_ci',
`subject_id` INT(11) NOT NULL,
PRIMARY KEY (`proxy_id`)
)ENGINE=InnoDb;

-- --------------------------------------------------------

--
-- Insert back-end menu items
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynmultilisting_main', 'standard', 'YN Multiple Listings Main Navigation Menu', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynmultilisting', 'ynmultilisting', 'YNC - Multiple Listing', '', '{"route":"admin_default","module":"ynmultilisting","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynmultilisting_admin_settings_global', 'ynmultilisting', 'Global Settings', '', '{"route":"admin_default","module":"ynmultilisting","controller":"settings", "action":"global"}', 'ynmultilisting_admin_main', '', 1),
('ynmultilisting_admin_settings_level', 'ynmultilisting', 'Member Level Settings', '', '{"route":"admin_default","module":"ynmultilisting","controller":"settings", "action":"level"}', 'ynmultilisting_admin_main', '', 2),
('ynmultilisting_admin_main_listingtypes', 'ynmultilisting', 'Manage Listing Types', '', '{"route":"admin_default","module":"ynmultilisting","controller":"listingtype", "action":"index"}', 'ynmultilisting_admin_main', '', 3),
('ynmultilisting_admin_main_categories', 'ynmultilisting', 'Manage Categories', '', '{"route":"admin_default","module":"ynmultilisting","controller":"category", "action":"index"}', 'ynmultilisting_admin_main', '', 4),
('ynmultilisting_admin_main_reviews', 'ynmultilisting', 'Manage Reviews', '', '{"route":"admin_default","module":"ynmultilisting","controller":"reviews", "action":"index"}', 'ynmultilisting_admin_main', '', 5),
('ynmultilisting_admin_main_editors', 'ynmultilisting', 'Manage Review Editors', '', '{"route":"admin_default","module":"ynmultilisting","controller":"editors", "action":"index"}', 'ynmultilisting_admin_main', '', 6),
('ynmultilisting_admin_main_listings', 'ynmultilisting', 'Manage Listings', '', '{"route":"admin_default","module":"ynmultilisting","controller":"listings", "action":"index"}', 'ynmultilisting_admin_main', '', 7),
('ynmultilisting_admin_main_statistic', 'ynmultilisting', 'Statistic', '', '{"route":"admin_default","module":"ynmultilisting","controller":"statistics", "action":"index"}', 'ynmultilisting_admin_main', '', 8),
('ynmultilisting_admin_main_modules', 'ynmultilisting', 'Manage Modules', '', '{"route":"admin_default","module":"ynmultilisting","controller":"module", "action":"index"}', 'ynmultilisting_admin_main', '', 9),
('ynmultilisting_admin_main_packages', 'ynmultilisting', 'Manage Packages', '', '{"route":"admin_default","module":"ynmultilisting","controller":"packages", "action":"index"}', 'ynmultilisting_admin_main', '', 10),
('ynmultilisting_admin_main_import', 'ynmultilisting', 'Import Listings', '', '{"route":"admin_default","module":"ynmultilisting","controller":"import", "action":"file"}', 'ynmultilisting_admin_main', '', 11),
('ynmultilisting_admin_main_transactions', 'ynmultilisting', 'Manage Transactions', '', '{"route":"admin_default","module":"ynmultilisting","controller":"transactions", "action":"index"}', 'ynmultilisting_admin_main', '', 12),
('ynmultilisting_admin_main_emailtemplates', 'ynmultilisting', 'Email Templates', '', '{"route":"admin_default","module":"ynmultilisting","controller":"mail", "action":"templates"}', 'ynmultilisting_admin_main', '', 13),
('ynmultilisting_admin_main_reports', 'ynmultilisting', 'Manage Reports', '', '{"route":"admin_default","module":"ynmultilisting","controller":"report", "action":"manage"}', 'ynmultilisting_admin_main', '', 14),
('ynmultilisting_admin_main_faqs', 'ynmultilisting', 'Manage FAQs', '', '{"route":"admin_default","module":"ynmultilisting","controller":"faqs", "action":"index"}', 'ynmultilisting_admin_main', '', 15);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynmultilisting', 'ynmultilisting', 'Multiple Listing', 'Ynmultilisting_Plugin_Menus::hasListingType', '{"route":"ynmultilisting_general","icon":"fa-database"}', 'core_main', '', 999),
('ynmultilisting_main_home', 'ynmultilisting', 'Home Page', '', '{"route":"ynmultilisting_general","action":"index"}', 'ynmultilisting_main', '', 1),
('ynmultilisting_main_browse', 'ynmultilisting', 'Browse Listings', '', '{"route":"ynmultilisting_general","action":"browse"}', 'ynmultilisting_main', '', 2),
('ynmultilisting_main_manage', 'ynmultilisting', 'My Listings', 'Ynmultilisting_Plugin_Menus::canCreateListing', '{"route":"ynmultilisting_general","action":"manage"}', 'ynmultilisting_main', '', 3),
('ynmultilisting_main_browse_wishlist', 'ynmultilisting', 'All Wish Lists', '', '{"route":"ynmultilisting_wishlist"}', 'ynmultilisting_main', '', 4),
('ynmultilisting_main_manage_wishlist', 'ynmultilisting', 'My Wish Lists', 'Ynmultilisting_Plugin_Menus::canManageWishlist', '{"route":"ynmultilisting_wishlist","action":"manage"}', 'ynmultilisting_main', '', 5),
('ynmultilisting_main_create_listing', 'ynmultilisting', 'Create New Listing', 'Ynmultilisting_Plugin_Menus::canCreateListing', '{"route":"ynmultilisting_general","action":"create"}', 'ynmultilisting_main', '', 7),
('ynmultilisting_main_browse_review', 'ynmultilisting', 'Browse Review', '', '{"route":"ynmultilisting_extended","controller":"review","action":"index"}', 'ynmultilisting_main', '', 8),
('ynmultilisting_main_faqs', 'ynmultilisting', 'FAQs', '', '{"route":"ynmultilisting_faqs","module":"ynmultilisting","controller":"faqs"}', 'ynmultilisting_main', '', 9);


-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynmultilisting_listing_follow_owner', 'ynmultilisting', '{item:$subject} is following your listing.', 0, ''),
('ynmultilisting_discussion_response', 'ynmultilisting', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::listing topic} you created.', 0, ''),
('ynmultilisting_discussion_reply', 'ynmultilisting', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::listing topic} you posted on.', 0, ''),
('ynmultilisting_listing_follow', 'ynmultilisting', '{item:$subject} has create a new {item:$object:listing}.', 0, ''),
('ynmultilisting_listing_approve', 'ynmultilisting', 'Your listing {item:$subject} has been approved and published.', 0, ''),
('ynmultilisting_listing_deny', 'ynmultilisting', 'Your listing {item:$object} has been denied.', 0, ''),
('ynmultilisting_listing_add_item', 'ynmultilisting', 'A {item:$subject:$label} has just been added to listing {item:$object}.', 0, ''),
('ynmultilisting_listing_status_change', 'ynmultilisting', 'The listing {item:$subject} has just been {var:$status}.', 0, ''),
('ynmultilisting_listing_new_transaction', 'ynmultilisting', 'A new transaction has been made for the listing {item:$subject}.', 0, ''),
('ynmultilisting_listing_package_change', 'ynmultilisting', 'The current applied {item:$subject} package for the listing {item:$object} has been {var:$status}.', 0, '');

-- --------------------------------------------------------
--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('ynmultilisting_email_to_friends', 'ynmultilisting', '[host],[email],[date],[sender_title],[sender_link],[sender_photo],[object_title],[message],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_listing_follow_owner', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_discussion_response', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_discussion_reply', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_listing_follow', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_listing_approve', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_listing_deny', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_listing_add_item', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[label]'),
('notify_ynmultilisting_listing_status_change', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[status]'),
('notify_ynmultilisting_listing_new_transaction', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynmultilisting_listing_package_change', 'ynmultilisting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[status]');


-- --------------------------------------------------------
--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynmultilisting_video_create', 'ynmultilisting', '{item:$subject} posted a new video:', 1, 3, 1, 1, 1, 1),
('ynmultilisting_topic_reply', 'ynmultilisting', '{item:$subject} replied to the topic {body:$body}', 1, 3, 1, 1, 1, 1),
('ynmultilisting_topic_create', 'ynmultilisting', '{item:$subject} posted a new topic:', 1, 3, 1, 1, 1, 1),
('ynmultilisting_photo_upload', 'ynmultilisting', '{item:$subject} added {var:$count} photo(s).', 1, 3, 2, 1, 1, 1),
('ynmultilisting_topic_reply', 'ynmultilisting', '{item:$subject} replied to the topic {body:$body}', 1, 3, 1, 1, 1, 1),
('ynmultilisting_review_create', 'ynmultilisting', '{item:$subject} add a review for the listing {item:$object}', 1, 3, 1, 1, 1, 1),
('ynmultilisting_listing_transfer', 'ynmultilisting', '{item:$subject} has became the owner of the listing {item:$object}', 1, 3, 1, 1, 1, 1),
('ynmultilisting_listing_create', 'ynmultilisting', '{item:$subject} add a new listing:', 1, 5, 1, 1, 1, 1);

-- --------------------------------------------------------

-- set default permissions for level settings of listing

-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auth_share' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auth_photo' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auth_video' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auth_discussion' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN - MODERATOR

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'use_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'close' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'review' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auto_approve' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'max_listing' as `name`,
    3 as `value`,
    20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'share' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'share' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'video' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'ynultimatevideo_video' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'discussion' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'use_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'close' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'review' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'auto_approve' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'max_listing' as `name`,
    3 as `value`,
    20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'share' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'share' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'video' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'ynultimatevideo_video' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'discussion' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_listing' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');


-- auth for review

-- MODERATOR, ADMIN

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_review' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_review' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmultilisting_review' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_review' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmultilisting_review' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmultilisting_review' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- auth view for wish list
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmultilisting_wishlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user','public', 'moderator', 'admin');


INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Ynmultilisting Check Expired Listings', 'ynmultilisting', 'Ynmultilisting_Plugin_Task_CheckExpiredListings', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0),
('Ynmultilisting Check Featured Listings', 'ynmultilisting', 'Ynmultilisting_Plugin_Task_CheckFeaturedListings', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0),
('Ynmultilisting Subscribe Listings', 'ynmultilisting', 'Ynmultilisting_Plugin_Task_SubsribeListings', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);