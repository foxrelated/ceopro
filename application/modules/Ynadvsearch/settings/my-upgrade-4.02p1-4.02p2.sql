ALTER TABLE `engine4_ynadvsearch_modules` ADD UNIQUE INDEX `name` (`name`);

INSERT IGNORE INTO `engine4_ynadvsearch_modules` (`name`, `title`, `enabled`, `available`) VALUES ('user', 'Members', 1, 1);
