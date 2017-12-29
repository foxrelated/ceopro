UPDATE `engine4_core_modules` SET `version` = '4.03p2' WHERE `name` = 'ynfilesharing';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynfilesharing_folder_create', 'ynfilesharing', '{item:$subject} added a subfolder in your folder {item:$object:$label}.', 0, ''),
('ynfilesharing_file_create', 'ynfilesharing', '{item:$subject} uploaded {var:$count} file(s) in your folder {item:$object:$label}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynfilesharing_folder_create', 'ynfilesharing', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link]'),
('notify_ynfilesharing_file_create', 'ynfilesharing', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link]');