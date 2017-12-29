UPDATE `engine4_core_modules` SET `version` = '4.02p1' WHERE `name` = 'ynwiki';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_file' as `name`,
    3 as `value`,
    'zip, rar, pdf, txt, html, php, tpl, doc, docx, xls, xlsx, pptx, exe, gif, png, jpg, jpeg, mp3, wav, xmind, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

UPDATE `engine4_core_content` SET  `params` =  '{"title":"Wiki Profile Detail"}' WHERE `engine4_core_content`.`name` = 'ynwiki.profile-detail';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('ynwiki_main', 'standard', 'Wiki Main Navigation Menu');