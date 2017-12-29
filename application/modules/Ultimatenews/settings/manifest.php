<?php defined("_ENGINE") or die("access denied"); return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ultimatenews',
    'title' => 'YNC - Ultimate News',
    'description' => 'Get RSS feeds from remote servers',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
    'version' => '4.06p1',
    'path' => 'application/modules/Ultimatenews',
    'repository' => 'younetco.com',
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.0',
      ),
      1 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.01',
      ),
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'callback' => 
    array (
      'path' => 'application/modules/Ultimatenews/settings/install.php',
      'class' => 'Ultimatenews_Package_Installer',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Ultimatenews',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ultimatenews.csv',
      1 => 'application/modules/Core/View/Helper/FeedDescription.php',
    ),
  ),
  'items' => 
  array (
    0 => 'ultimatenews',
    1 => 'ultimatenews_category',
    2 => 'ultimatenews_content',
    3 => 'ultimatenews_categoryparent',
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => 
  array (
  'ultimatenews_extended' =>
        array(
            'route' => 'news/:controller/:action/*',
            'defaults' =>
            array(
                'module' => 'ultimatenews',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' =>
            array(
                'controller' => '\\D+',
                'action' => '\\D+',
            ),
        ),
    'ultimatenews_feed' => 
	    array (
	      'route' => 'news/feed/:category',
	      'defaults' => 
	      array (
	        'module' => 'ultimatenews',
	        'controller' => 'index',
	        'action' => 'feed',
	      ),
      ),
      'ultimatenews_categoryparent' => 
	    array (
	      'route' => 'news/category/:categoryparent',
	      'defaults' => 
	      array (
	        'module' => 'ultimatenews',
	        'controller' => 'index',
	        'action' => 'contents',
	      ),
      ),
    'ultimatenews_specific' => 
    array (
      'route' => 'news/:id/:slug',
      'defaults' => 
      array (
        'module' => 'ultimatenews',
        'controller' => 'index',
        'action' => 'detail',
      ),
      'reqs' => 
      array (
        'action' => '(detail)',
        'id' => '\\d+',
        0 => 'title',
      ),
    ),
    'ultimatenews_category' => 
    array (
      'route' => 'news/list',
      'defaults' => 
      array (
        'module' => 'ultimatenews',
        'controller' => 'index',
        'action' => 'list',
      ),
      'reqs' => 
      array (
        'action' => '(list)',
        'id' => '\\d+',
        'title' => '\\d+',
      ),
    ),
    'ultimatenews_tag' => 
    array (
      'route' => 'news/tag/:tag_id/:tag_name',
      'defaults' => 
      array (
        'module' => 'ultimatenews',
        'controller' => 'index',
        'action' => 'tag',
      ),
      'reqs' => 
      array (
        'action' => '(tag)',
      ),
    ),
    'ultimatenews_general' => 
    array (
      'route' => 'news/:action/:page/*',
      'defaults' => 
      array (
        'module' => 'ultimatenews',
        'controller' => 'index',
        'action' => 'index',
        'page' => 1,
      ),
      'reqs' => 
      array (
        'page' => '\\d+',
        'action' => '(index|manage|featured|lists|upload-photo|create-news|your-subscribe|my-news|my-feed|create-feed|my-favorite|manage-feed|approve|favorite|favorite-ajax|un-favorite-ajax|un-favorite)',
      ),
    ),
    'ultimatenews_xml' => 
    array (
      'route' => 'news/readxml',
      'defaults' => 
      array (
        'module' => 'ultimatenews',
        'controller' => 'index',
        'action' => 'readxml',
      ),
      'reqs' => 
      array (
        'action' => '(readxml)',
      ),
    ),
    'ultimatenews_edit_ultimatenews' => 
    array (
      'route' => 'news/edit/*',
      'defaults' => 
      array (
        'module' => 'ultimatenews',
        'controller' => 'index',
        'action' => 'edit',
      ),
    ),
    'ultimatenews_loadFeed' => 
	    array (
	      'route' => 'news/loadfeed/*',
	      'defaults' => 
	      array (
	        'module' => 'ultimatenews',
	        'controller' => 'index',
	        'action' => 'loadfeed',
	      ),
	    ),
	    
	'ultimatenews_manage_action' => 
	    array (
	      'route' => 'news/manage-actions/:action/*',
	      'defaults' => 
	      array (
	        'module' => 'ultimatenews',
	        'controller' => 'manage',
	        'action' => 'index',
	      ),
	    ),
  ),
);?>