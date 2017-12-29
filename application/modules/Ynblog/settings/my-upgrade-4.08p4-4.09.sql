UPDATE `engine4_core_modules` SET `version` = '4.09' where 'name' = 'ynblog';

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_admin_blog_moderation', 'ynblog', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');
