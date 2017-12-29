UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `name` = 'advgroup';

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_advgroup_cancel_invite', 'advgroup', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]');