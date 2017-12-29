UPDATE `engine4_core_modules` SET `version` = '4.12' WHERE `name` = 'advalbum';

ALTER TABLE `engine4_album_photos` ADD `longitude` VARCHAR(64) NULL AFTER `location`;
ALTER TABLE `engine4_album_photos` ADD `latitude` VARCHAR(64) NULL AFTER `location`;

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
  ('send_image', 'advalbum', '[host],[email],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo]');

UPDATE `engine4_core_mailtemplates`
  SET `vars` = '[host],[email],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo]'
  WHERE `type` = 'send_image' AND `module` = 'advalbum';