UPDATE `engine4_core_modules` SET `version` = '4.04p2' where 'name' = 'ynforum';
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynforum_category' as `type`,
    'forumcat.view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
  
INSERT IGNORE INTO `engine4_authorization_allow`
  SELECT
    'ynforum_category' as `resource_type`,
    `category_id` as `resource_id`,
    'forumcat.view' as `action`,
    'everyone' as `role`,
    0 as `role_id`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_forum_categories`;