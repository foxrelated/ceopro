<?php return array (
  /*----- Package -----*/
  'package' =>
  array (
    'type' => 'module',
    'name' => 'ynblog',
    'version' => '4.10p2',
    'path' => 'application/modules/Ynblog',
    'title' => 'YNC - Advanced Blog',
    'description' => 'The blog plugin helps users share their feeling, emotion and their knowledge to each other.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
      array(
        'type' => 'module',
        'name' => 'blog',
        'minVersion' => '4.1.5',
      ),
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'callback' =>
    array (
      'path' => 'application/modules/Ynblog/settings/install.php',
      'class' => 'Ynblog_Installer',
    ),
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' =>
    array (
      0 => 'application/modules/Ynblog',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/ynblog.csv',
    ),
  ),
  /*----- Hook -----*/
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Ynblog_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Ynblog_Plugin_Core',
    ),
  ),
  /*----- Item -----*/
  'items' => array(
    'blog',
    'blog_category',
    'blog_feature',
  	'blog_link'
  ),
  /*----- Routes -----*/
  'routes' => array(

    'blog_specific' => array(
      'route' => 'blogs/:action/:blog_id/*',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'blog_id' => '\d+',
        'action' => '(delete|edit)',
      ),
    ),

    'blog_general' => array(
      'route' => 'blogs/:action/*',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|create|manage|listing|style|tag|upload-photo|rss|become|share|show-message)',
      ),
    ),

    'blog_view' => array(
      'route' => 'blogs/:user_id/*',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'list',
      ),
      'reqs' => array(
        'user_id' => '\d+',
      ),
    ),

    'blog_entry_view' => array(
      'route' => 'blogs/:user_id/:blog_id/:slug',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'blog_id' => '\d+'
      ),
    ),
    // Public
    'blog_import' => array(
      'route' => 'blogs/import',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'import',
        'action' => 'import',
      ),
      'reqs' => array(
        'action' => '(import)',
      ),
    ),
      'blog_export' => array(
          'route' => 'blogs/export/:action/*',
          'defaults' => array(
              'module' => 'ynblog',
              'controller' => 'export',
              'action' => 'index',
          ),
          'reqs' => array(
              'action' => '(index|export)',
          ),
      ),

  ),
); ?>