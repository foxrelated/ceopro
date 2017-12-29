<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynmultilisting',
    'version' => '4.01p3',
    'path' => 'application/modules/Ynmultilisting',
    'title' => 'YNC - Multiple Listings',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
    'callback' => 
    array (
        'path' => 'application/modules/Ynmultilisting/settings/install.php',    
        'class' => 'Ynmultilisting_Installer',
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
      0 => 'application/modules/Ynmultilisting',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynmultilisting.csv',
      1 => 'application/modules/Ynmultilisting/views/scripts/listing/package.tpl'
    ),
    'dependencies' => array(
        array(
            'type' => 'module',
            'name' => 'younet-core',
            'minVersion' => '4.02p10',
        ),
     ),
 ),
 // Hooks ---------------------------------------------------------------------
	'hooks' => array(
			array(
				'event' => 'onItemCreateAfter',
				'resource' => 'Ynmultilisting_Plugin_Core',
			),
			array (
			    'event' => 'onItemUpdateAfter',
			    'resource' => 'Ynmultilisting_Plugin_Core',
		    ),	
		    array (
			    'event' => 'onItemDeleteAfter',
			    'resource' => 'Ynmultilisting_Plugin_Core',
		    ),
		    array(
                'event' => 'onItemDeleteBefore',
                'resource' => 'Ynmultilisting_Plugin_Core',
            ),		
            array(
                'event' => 'onStatistics',
                'resource' => 'Ynmultilisting_Plugin_Core',
            ),
            array(
                'event' => 'onRenderLayoutDefault',
                'resource' => 'Ynmultilisting_Plugin_Core',
            ),   
	),
 // Items ---------------------------------------------------------------------
    'items' => array(
  		'ynmultilisting_category',
  		'ynmultilisting_listing',
  		'ynmultilisting_album',
  		'ynmultilisting_review',
		'ynmultilisting_package',
		'ynmultilisting_module',
		'ynmultilisting_order',
		'ynmultilisting_transaction',
		'ynmultilisting_album',
		'ynmultilisting_photo',
		'ynmultilisting_faq',
		'ynmultilisting_promotion',
		'ynmultilisting_module',
		'ynmultilisting_quicklink',
		'ynmultilisting_wishlist',
		'ynmultilisting_topic',
    	'ynmultilisting_post',
    	'ynmultilisting_editor',
    	'ynmultilisting_listingtype',
    	'ynmultilisting_mail_template',
 		'ynmultilisting_reviewtype',
 		'ynmultilisting_ratingtype',
    	'ynmultilisting_report',
    	'ynmultilisting_import',
    ),
 // Routes ---------------------------------------------------------------------
	'routes' => array(
		'ynmultilisting_home' => array(
			'route' => 'multi-listing/*',
			'defaults' => array(
				'module' => 'ynmultilisting',
				'controller' => 'index',
				'action' => 'index',
			),
		),
		
		'ynmultilisting_extended' => array(
			'route' => 'multi-listing/:controller/:action/*',
			'defaults' => array(
				'module' => 'ynmultilisting',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
				'controller' => '\D+',
				'action' => '\D+',
			)
		),
		'ynmultilisting_general' => array(
			'route' => 'multi-listing/:action/*',
			'defaults' => array(
				'module' => 'ynmultilisting',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index|browse|get-my-location|follow|manage|place-order|update-order|pay-credit|export|create|create-step-two|subscribe-listing|unsubscribe|filter-category|display-map-view)',
	        )
		),
		'ynmultilisting_specific' => array(
			'route' => 'multi-listing/:action/:listing_id/*',
	        'defaults' => array(
	            'module' => 'ynmultilisting',
	            'controller' => 'listing',
	            'action' => 'index',
	        ),
	        'reqs' => array(
	            'action' => '(package|package-change|index|edit|delete|print|feature|direction|email-to-friends|transfer-owner|select-theme|publish-close|add-to-compare|publish)',
	            'listing_id' => '\d+',
	        )
	    ),
	    
		'ynmultilisting_profile' => array(
			'route' => 'multi-listing/:id/:slug/*',
			'defaults' => array(
					'module' => 'ynmultilisting',
					'controller' => 'profile',
					'action' => 'index',
					'slug' => '',
			),
			'reqs' => array(
					'id' => '\d+',
			)
	    ),
		
		'ynmultilisting_post' => array(
			'route' => 'multi-listing/post/control/:action/*',
			'defaults' => array(
					'module' => 'ynmultilisting',
					'controller' => 'post',
					'action' => 'edit',
			),
			'reqs' => array(
					'action' => '(edit|delete|report)',
			)
		),
		
		'ynmultilisting_transaction' => array(
	      'route' => 'multi-listing/transaction/:action/*',
	      'defaults' => array(
	        'module' => 'ynmultilisting',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
 	    ),
		
		'ynmultilisting_faqs' => array(
	        'route' => 'multi-listing/faqs/:action/*',
	        'defaults' => array(
	            'module' => 'ynmultilisting',
	            'controller' => 'faqs',
	            'action' => 'index'
	        ),
	        'reqs' => array('action' => '(index)')
	    ),
	    
	    'ynmultilisting_import' => array(
            'route' => 'multi-listing/import/:action/*',
            'defaults' => array(
                'module' => 'ynmultilisting',
                'controller' => 'import',
                'action' => 'file'
            ),
        ),
        
		'ynmultilisting_review' => array(
	        'route' => 'multi-listing/review/:action/*',
	        'defaults' => array(
	                'module' => 'ynmultilisting',
	                'controller' => 'review',
	                'action' => 'index',
	        ),
	        'reqs' => array(
	                'action' => '(index|edit|delete|view|useful)',
	        )
	    ),
	    
		'ynmultilisting_compare' => array(
	        'route' => 'multi-listing/compare/:action/*',
	            'defaults' => array(
	            'module' => 'ynmultilisting',
	            'controller' => 'compare',
	            'action' => 'index'
	        ),
	    ),
	    
	    'ynmultilisting_wishlist' => array(
	        'route' => 'multi-listing/wishlist/:action/*',
	            'defaults' => array(
	            'module' => 'ynmultilisting',
	            'controller' => 'wishlist',
	            'action' => 'index'
	        ),
	    ),
	),
); ?>