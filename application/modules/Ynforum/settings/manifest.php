<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'ynforum',
        'version' => '4.05p1',
        'path' => 'application/modules/Ynforum',
        'title' => 'YNC - Advanced Forum',
        'description' => 'Advanced Forum',
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
        'changeLog' => 'settings/changelog.php',
        'callback' => array(
            'class' => 'Engine_Package_Installer_Module',
        ),
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'forum',
                'minVersion' => '4.1.1',
            ),
            array(
                'type' => 'module',
                'name' => 'younet-core',
                'minVersion' => '4.02p13',
            ),
        ),
        'callback' => array(
            'path' => 'application/modules/Ynforum/settings/install.php',
            'class' => 'Ynforum_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Ynforum',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/ynforum.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Ynforum_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteAfter',
            'resource' => 'Ynforum_Plugin_Core'
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Ynforum_Plugin_Core'
        ),
        array(
            'event' => 'onItemCreateAfter',
            'resource' => 'Ynforum_Plugin_Core'
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'forum', // Hack, forum_forum should be removed    
        'ynforum_forum',
        'forum_forum',
        'ynforum_category',
        'ynforum_container',
        'ynforum_post',
        'forum_post',
        'ynforum_signature',
        'ynforum_topic',
        'forum_topic',
        'ynforum_list',
        'ynforum_list_item',
        'ynforum_category_list',
        'ynforum_category_list_item',
        'ynforum_photo',
        'ynforum_thank',
        'ynforum_reputation',
        'ynforum_userview',
        'ynforum_icon',
        'ynforum_announcement',
        'ynforum_postalbum',
        'ynforum_postphoto',
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(    	
        'ynforum_general' => array(
            'route' => 'forums/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'index',
                'action' => 'index'
            )
        ),
        'ynforum_forum' => array(
            'route' => 'forums/:forum_id/:slug/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'forum',
                'action' => 'view',
                'slug' => '-',
            ),
            'reqs' => array(
                'action' => '(create|edit|delete|view|topic-create|view-not-approved-posts|search|watch|newest-posts)',
                'slug' => '[\w-]+',
            ),
        ),
        'ynforum_category' => array(
            'route' => 'forums/category/:category_id/:slug/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'category',
                'slug' => '-',
            ),
        ),
        'ynforum_topic' => array(
            'route' => 'forums/topic/:topic_id/:slug/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'topic',
                'action' => 'view',
                'slug' => '-',
            ),
            'reqs' => array(
                'action' => '(edit|delete|close|rename|move|sticky|view|watch|post-create|topic-rate)',
                'slug' => '[\w-]+',
            ),
        ),
        'ynforum_post' => array(
            'route' => 'forums/post/:action/:post_id/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'post',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(edit|delete|approve|thank|add-reputation|deny|manage-photos|render-album-photos|upload-photo|delete-photo)',
            ),
        ),
        'ynforum_postphoto' => array(
            'route' => 'forums/post/:post_id/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'post',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(edit|delete|approve|thank|add-reputation|deny|manage-photos|render-album-photos)',
            ),
        ),
        // announcements
         'ynforum_announcement' => array(
            'route' => 'forums/:forum_id/announcement/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'announcement',
                'action' => 'manage',
            ),
            'reqs' => array(
                'action' => '(create|edit|delete|manage|highlight)',
            ),
        ),
        // events
         'ynforum_event' => array(
            'route' => 'forums/:forum_id/event/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'event',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(create|manage|index|invite|highlight)',
            ),
        ),
        // groups
         'ynforum_group' => array(
            'route' => 'forums/:forum_id/group/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'group',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(create|manage|index|invite|highlight)',
            ),
        ),
        'ynforum_admin_default' => array(
            'route' => 'admin/forum/manage/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'admin-manage',
            )
        ),
        'ynforum_upload_photo' => array(
            'route' => 'forums/upload/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'index',
                'action' => 'upload'
            )
        ),
		// polls
        'ynforum_poll' => array(
            'route' => 'forums/:forum_id/poll/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'poll',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(create|manage|index|invite|highlight|edit|close|delete)',
            ),
        ),
    	// user dashboard
    	'ynforum_dashboard' => array(
    			'route' => 'forums/dashboard/:action/*',
    			'defaults' => array(
    					'module' => 'ynforum',
    					'controller' => 'dashboard',
    					'action' => 'signature',
    			),
    			'reqs' => array(
    					'action' => '(signature|manage-attachments|my-watch-topic|deleteattachment)',
    			),
    	),
        // blog
        'ynforum_blog' => array(
            'route' => 'forums/:forum_id/blog/:action/*',
            'defaults' => array(
                'module' => 'ynforum',
                'controller' => 'blog',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(create|index)',
            ),
        ),
    )
);
?>