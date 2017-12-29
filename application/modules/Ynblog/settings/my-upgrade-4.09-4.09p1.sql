UPDATE `engine4_core_modules` SET `version` = '4.09p1' where 'name' = 'ynblog';

UPDATE `engine4_core_content`
SET `params` = '{"mode_grid":1,"mode_list":1,"view_mode":"list"}'
WHERE `name` = 'ynblog.blogs-listing' ;

UPDATE `engine4_core_content`
SET `params` = '{"title":"Blogs","titleCount":true,"mode_grid":1,"mode_list":1,"view_mode":"list"}'
WHERE `name` = 'ynblog.profile-blogs' ;

UPDATE `engine4_core_content`
SET `params` = '{"title":"Top Blogs","titleCount":true,"mode_grid":1,"mode_list":1,"view_mode":"list"}'
WHERE `name` = 'ynblog.top-blogs' ;

UPDATE `engine4_core_content`
SET `params` = '{"title":"New Blogs","titleCount":true,"mode_grid":1,"mode_list":1,"view_mode":"list"}'
WHERE `name` = 'ynblog.new-blogs' ;

UPDATE `engine4_core_content`
SET `params` = '{"title":"Most Viewed Blogs","titleCount":true,"mode_grid":1,"mode_list":1,"view_mode":"list"}'
WHERE `name` = 'ynblog.most-viewed-blogs' ;

UPDATE `engine4_core_content`
SET `params` = '{"title":"Most Commented Blogs","titleCount":true,"mode_grid":1,"mode_list":1,"view_mode":"list"}'
WHERE `name` = 'ynblog.most-commented-blogs' ;
