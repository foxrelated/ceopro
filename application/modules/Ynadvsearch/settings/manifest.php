<?php return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'ynadvsearch',
    'version' => '4.04p4',
    'path' => 'application/modules/Ynadvsearch',
    'title' => 'YNC - Advanced Search',
    'description' => 'YNC Advanced Search',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
    'callback' =>
    array (
      'path' => 'application/modules/Ynadvsearch/settings/install.php',
      'class' => 'Ynadvsearch_Installer',
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
      0 => 'application/modules/Ynadvsearch',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/ynadvsearch.csv',
    ),
    'dependencies' =>
    array (
      0 =>
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02p13',
      ),
    ),
  ),
      // Routes --------------------------------------------------------------------
  'routes' => array(
        'ynadvsearch_extended' => array(
        'route' => 'search/:controller/:action/*',
        'defaults' => array(
            'module' => 'ynadvsearch',
            'controller' => 'index',
            'action' => 'index',
        ),
        'reqs' => array(
            'controller' => '\D+',
            'action' => '\D+',
        )
    ),
    
    'ynadvsearch_search' => array(
        'route' => 'search/:action/*',
        'defaults' => array(
            'module' => 'ynadvsearch',
            'controller' => 'search',
            'action' => 'index',
			'is_search' => 1
        ),
        'reqs' => array(
            'controller' => '\D+',
            'action' => '\D+',
        )
    ),

	'ynadvsearch_faqs' => array(
            'route' => 'search/faqs/:action/*',
            'defaults' => array(
                'module' => 'ynadvsearch',
                'controller' => 'faqs',
                'action' => 'index'
            ),
            'reqs' => array('action' => '(index)')
        ),
   ),
   // Items ---------------------------------------------------------------------
  'items' => array(
    'ynadvsearch_faq',
    'ynadvsearch_contenttype',
    'ynadvsearch_keyword',
  ),
) ; ?>