<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynmember',
    'version' => '4.02p1',
    'path' => 'application/modules/Ynmember',
    'title' => 'YNC - Advanced Members',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Ynmember/settings/install.php',	
      'class' => 'Ynmember_Installer',
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
      0 => 'application/modules/Ynmember',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynmember.csv',
    ),
    'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'younet-core',
                'minVersion' => '4.02p13',
            ),
     ),
  ),
  'hooks' =>
	array (
			0 =>
			array (
					'event' => 'onItemCreateAfter',
					'resource' => 'Ynmember_Plugin_Core',
			),
	),		
  // Items ---------------------------------------------------------------------
  'items' => array(
    'ynmember_relationship',
    'ynmember_review',
    'ynmember_ratingtype',
    'ynmember_feature',
    'ynmember_order',
    'ynmember_transaction',
    'ynmember_rating',
    'ynmember_workplace',
    'ynmember_liveplace',
  	'ynmember_linkage',
  	'ynmember_studyplace',
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => array(
  	 
	 'ynmember_extended' => array(
		'route' => 'adv-members/:controller/:action/*',
		'defaults' => array(
			'module' => 'ynmember',
			'controller' => 'index',
			'action' => 'index',
		),
		'reqs' => array(
			'controller' => '\D+',
			'action' => '\D+',
		)
	),
	 
  	 'ynmember_general' => array(
			'route' => 'adv-members/:controller/:action/*',
			'defaults' => array(
					'module' => 'ynmember',
					'controller' => 'index',
					'action' => 'index',
			),
			'reqs' => array(
					'action' => '(index|feature-member|place-order|update-order|viewinfo|rate-member)',
			)
	),
	
	'ynmember_browse' => array(
		'route' => 'members/:action/*',
		'defaults' => array(
			'module' => 'ynmember',
			'controller' => 'index',
			'action' => 'index',
		),
		'reqs' => array(
			'action' => '(browse)',
		)
	),
	'ynmember_transaction' => array(
	      'route' => 'member/transaction/:action/*',
	      'defaults' => array(
	        'module' => 'ynmember',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
 	 ),
  )
); ?>