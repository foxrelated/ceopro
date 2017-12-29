CREATE TABLE IF NOT EXISTS `engine4_album_ratings` (
	`rating_id` INT(11) NOT NULL AUTO_INCREMENT,
	`subject_id` INT(11) UNSIGNED NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`rating` TINYINT(1) UNSIGNED NOT NULL,
	`type` ENUM('photo','album') NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


ALTER TABLE `engine4_album_albums` ADD COLUMN `rating` FLOAT NOT NULL DEFAULT '0';

ALTER TABLE `engine4_album_albums` ADD COLUMN `featured` TINYINT(1) NULL DEFAULT '0';

ALTER TABLE `engine4_album_photos` ADD COLUMN `rating` FLOAT NOT NULL DEFAULT '0';

ALTER TABLE `engine4_album_photos` ADD COLUMN `taken_date` DATE NULL DEFAULT NULL;

ALTER TABLE `engine4_album_photos` ADD COLUMN `location` TEXT NULL COLLATE 'utf8_unicode_ci';