UPDATE `engine4_core_modules` SET `version` = '4.02p4' WHERE `name` = 'ynwiki';
UPDATE `engine4_core_menuitems` SET `label` = 'YNC - Wiki' WHERE `name` = 'core_admin_main_plugins_ynwiki';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"ynwiki_general","action":"browse","icon":"fa-file-text-o","icon_hover_action":"fa-file-text-o"}' WHERE `name` = 'core_main_ynwiki';

DELETE FROM `engine4_authorization_permissions` WHERE `type` = 'ynwiki_page' AND `name` IN ('auth_view','auth_restrict','auth_edit','auth_delete','auth_comment', 'auth_html');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_restrict' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_edit' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_delete' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynwiki_page' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","member","ynwiki_list","owner_member_member","owner_member","parent_member","owner","include","exclude"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
      level_id as `level_id`,
      'ynwiki_page' as `type`,
      'auth_html' as `name`,
      3 as `value`,
      'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, table, tbody, tr, td, caption, iframe' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_view';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_comment';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_restrict';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_edit';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","registered","member","ynwiki_list","owner_network","owner_member_member","owner_member","parent_member","owner","include","exclude"]'
WHERE `type` = 'ynwiki_page' and `name` = 'auth_delete';

UPDATE `engine4_authorization_allow` SET `role` = 'member' WHERE `resource_type` = 'ynwiki_page' AND `role` = 'ynwiki_member';