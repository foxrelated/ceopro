<?php defined("_ENGINE") or die("access denied"); return array (
  'menus' => 
  array (
  ),
  'menuitems' => 
  array (
    0 => 
    array (
      'id' => 191,
      'name' => 'core_main_ultimatenews',
      'module' => 'ultimatenews',
      'label' => 'Ultimate News',
      'plugin' => '',
      'params' => '{"route":"ultimatenews_extended","module":"ultimatenews","icon":"fa-newspaper-o"}',
      'menu' => 'core_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 10,
    ),
    1 => 
    array (
      'id' => 192,
      'name' => 'core_admin_main_plugins_ultimatenews',
      'module' => 'ultimatenews',
      'label' => 'YNC - Ultimate News',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ultimatenews","controller":"manage"}',
      'menu' => 'core_admin_main_plugins',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    2 => 
    array (
      'id' => 193,
      'name' => 'ultimatenews_admin_main_manage',
      'module' => 'ultimatenews',
      'label' => 'News Management',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ultimatenews","controller":"manage"}',
      'menu' => 'ultimatenews_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    3 => 
    array (
      'id' => 194,
      'name' => 'ultimatenews_admin_main_category',
      'module' => 'ultimatenews',
      'label' => 'Feed Management',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ultimatenews","controller":"manage","action":"feed"}',
      'menu' => 'ultimatenews_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    4 => 
    array (
      'id' => 195,
      'name' => 'ultimatenews_admin_main_categories',
      'module' => 'ultimatenews',
      'label' => 'Category Management',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ultimatenews","controller":"manage","action":"categories"}',
      'menu' => 'ultimatenews_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 5,
    ),
    5 => 
    array (
      'id' => 196,
      'name' => 'ultimatenews_admin_main_create',
      'module' => 'ultimatenews',
      'label' => 'Add Feed',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ultimatenews","controller":"manage","action":"create"}',
      'menu' => 'ultimatenews_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
    7 => 
    array (
      'id' => 278,
      'name' => 'ultimatenews_admin_main_settings',
      'module' => 'ultimatenews',
      'label' => 'Global Settings',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ultimatenews","controller":"settings"}',
      'menu' => 'ultimatenews_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 7,
    ),
    8 => 
    array (
      'id' => 280,
      'name' => 'ultimatenews_admin_main_create_news',
      'module' => 'ultimatenews',
      'label' => 'Add News',
      'plugin' => NULL,
      'params' => '{"route":"admin_default","module":"ultimatenews","controller":"manage","action":"create-news"}',
      'menu' => 'ultimatenews_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
  ),
  'mails' => 
  array (
  ),
  'jobtypes' => 
  array (
  ),
  'notificationtypes' => 
  array (
  ),
  'actiontypes' => 
  array (
    0 => 
    array (
      'type' => 'comment_ultimatenews_content',
      'module' => 'ultimatenews',
      'body' => '{item:$subject} commented on {item:$object:news}: {body:$body}',
      'enabled' => 1,
      'displayable' => 5,
      'attachable' => 0,
      'commentable' => 1,
      'shareable' => 4,
      'is_generated' => 0,
    ),
  ),
  'permissions' => 
  array (
    0 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'auth_comment',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","owner"]',
    ),
    1 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'auth_html',
      3 => 3,
      4 => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr',
    ),
    2 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'auth_view',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","owner"]',
    ),
    3 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    4 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    5 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'css',
      3 => 1,
      4 => NULL,
    ),
    6 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'delete',
      3 => 1,
      4 => NULL,
    ),
    7 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'edit',
      3 => 1,
      4 => NULL,
    ),
    8 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'max',
      3 => 3,
      4 => '20',
    ),
    9 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'photo',
      3 => 1,
      4 => NULL,
    ),
    10 => 
    array (
      0 => 'admin',
      1 => 'ultimatenews_content',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    11 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'auth_comment',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","owner"]',
    ),
    12 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'auth_html',
      3 => 3,
      4 => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr',
    ),
    13 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'auth_view',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","owner"]',
    ),
    14 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    15 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    16 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'css',
      3 => 1,
      4 => NULL,
    ),
    17 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'delete',
      3 => 1,
      4 => NULL,
    ),
    18 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'edit',
      3 => 1,
      4 => NULL,
    ),
    19 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'max',
      3 => 3,
      4 => '20',
    ),
    20 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'photo',
      3 => 1,
      4 => NULL,
    ),
    21 => 
    array (
      0 => 'moderator',
      1 => 'ultimatenews_content',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    22 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'auth_comment',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","owner"]',
    ),
    23 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'auth_html',
      3 => 3,
      4 => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr',
    ),
    24 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'auth_view',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","owner"]',
    ),
    25 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    26 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    27 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'css',
      3 => 1,
      4 => NULL,
    ),
    28 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'delete',
      3 => 1,
      4 => NULL,
    ),
    29 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'edit',
      3 => 1,
      4 => NULL,
    ),
    30 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'max',
      3 => 3,
      4 => '20',
    ),
    31 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'photo',
      3 => 1,
      4 => NULL,
    ),
    32 => 
    array (
      0 => 'user',
      1 => 'ultimatenews_content',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    33 => 
    array (
      0 => 'public',
      1 => 'ultimatenews_content',
      2 => 'auth_comment',
      3 => 3,
      4 => '[]',
    ),
    34 => 
    array (
      0 => 'public',
      1 => 'ultimatenews_content',
      2 => 'auth_html',
      3 => 3,
      4 => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr',
    ),
    35 => 
    array (
      0 => 'public',
      1 => 'ultimatenews_content',
      2 => 'auth_view',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","owner"]',
    ),
    36 => 
    array (
      0 => 'public',
      1 => 'ultimatenews_content',
      2 => 'css',
      3 => 1,
      4 => NULL,
    ),
    37 => 
    array (
      0 => 'public',
      1 => 'ultimatenews_content',
      2 => 'max',
      3 => 3,
      4 => '20',
    ),
    38 => 
    array (
      0 => 'public',
      1 => 'ultimatenews_content',
      2 => 'photo',
      3 => 1,
      4 => NULL,
    ),
    39 => 
    array (
      0 => 'public',
      1 => 'ultimatenews_content',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
  ),
  'pages' => 
  array (
    'ultimatenews_index_detail' => 
    array (
      'page_id' => 37,
      'name' => 'ultimatenews_index_detail',
      'displayname' => 'Ultimate News Detail Page',
      'url' => NULL,
      'title' => 'Ultimate News Detail Page',
      'description' => 'This is Ultimate News Detail Page.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => NULL,
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 775,
          'page_id' => 37,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 776,
              'page_id' => 37,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 775,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 777,
                  'page_id' => 37,
                  'type' => 'widget',
                  'name' => 'ultimatenews.menu-ultimatenews',
                  'parent_content_id' => 776,
                  'order' => 3,
                  'params' => '',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 778,
          'page_id' => 37,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 780,
              'page_id' => 37,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 778,
              'order' => 4,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 782,
                  'page_id' => 37,
                  'type' => 'widget',
                  'name' => 'ultimatenews.categories-ultimatenews',
                  'parent_content_id' => 780,
                  'order' => 1,
                  'params' => '{"title":"Categories"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                 1 => 
                array (
                  'content_id' => 783,
                  'page_id' => 37,
                  'type' => 'widget',
                  'name' => 'ultimatenews.tag-news',
                  'parent_content_id' => 780,
                  'order' => 2,
                  'params' => '{"title":"Tags"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 784,
                  'page_id' => 37,
                  'type' => 'widget',
                  'name' => 'ultimatenews.lasted-ultimatenews',
                  'parent_content_id' => 780,
                  'order' => 3,
                  'params' => '{"title":"Recent News"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 779,
              'page_id' => 37,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 778,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 781,
                  'page_id' => 37,
                  'type' => 'widget',
                  'name' => 'ultimatenews.article-detail',
                  'parent_content_id' => 779,
                  'order' => 3,
                  'params' => '',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'ultimatenews_index_list' => 
    array (
      'page_id' => 36,
      'name' => 'ultimatenews_index_list',
      'displayname' => 'Ultimate News Home Page',
      'url' => NULL,
      'title' => 'Ultimate News Home Page',
      'description' => 'This is Ultimate News home page.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => NULL,
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 760,
          'page_id' => 36,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 761,
              'page_id' => 36,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 760,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 762,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.menu-ultimatenews',
                  'parent_content_id' => 761,
                  'order' => 3,
                  'params' => '',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 763,
          'page_id' => 36,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 765,
              'page_id' => 36,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 763,
              'order' => 4,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 769,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.categories-ultimatenews',
                  'parent_content_id' => 765,
                  'order' => 1,
                  'params' => '{"title":"Categories"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 770,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.lasted-ultimatenews',
                  'parent_content_id' => 765,
                  'order' => 2,
                  'params' => '{"title":"Recent News"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 771,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.most-commented-ultimatenews',
                  'parent_content_id' => 765,
                  'order' => 3,
                  'params' => '{"title":"Most Commented News"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 766,
              'page_id' => 36,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 763,
              'order' => 5,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 772,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.search-ultimatenews',
                  'parent_content_id' => 766,
                  'order' => 1,
                  'params' => '',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                 1 => 
                array (
                  'content_id' => 773,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.tag-news',
                  'parent_content_id' => 766,
                  'order' => 2,
                  'params' => '{"title":"Tags"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 774,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.top-ultimatenews',
                  'parent_content_id' => 766,
                  'order' => 3,
                  'params' => '{"title":"Top News"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                3 => 
                array (
                  'content_id' => 785,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.most-liked-ultimatenews',
                  'parent_content_id' => 766,
                  'order' => 4,
                  'params' => '{"title":"Most Liked News"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            2 => 
            array (
              'content_id' => 764,
              'page_id' => 36,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 763,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 767,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.featured-ultimatenews',
                  'parent_content_id' => 764,
                  'order' => 4,
                  'params' => '{"title":"Featured News"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 768,
                  'page_id' => 36,
                  'type' => 'widget',
                  'name' => 'ultimatenews.list-ultimatenews',
                  'parent_content_id' => 764,
                  'order' => 5,
                  'params' => '',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);?>