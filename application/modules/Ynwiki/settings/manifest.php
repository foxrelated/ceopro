<?php return array (
    'package' =>
        array (
            'type' => 'module',
            'name' => 'ynwiki',
            'version' => '4.02p4',
            'path' => 'application/modules/Ynwiki',
            'title' => 'YNC - Wiki',
            'description' => 'Wiki Plugin',
            'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
            'dependencies' => array(
                array(
                    'type' => 'module',
                    'name' => 'younet-core',
                    'minVersion' => '4.02p13',
                ),
            ),
            'callback' => array(
                'path' => 'application/modules/Ynwiki/settings/install.php',
                'class' => 'Ynwiki_Installer',
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
                    0 => 'application/modules/Ynwiki',
                ),
            'files' =>
                array (
                    0 => 'application/languages/en/ynwiki.csv',
                    1 => 'application/modules/Authorization/Model/DbTable/Allow.php',
                ),
        ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Ynwiki_Plugin_Core'
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'ynwiki_page',
        'ynwiki_revision',
        'ynwiki_follow',
        'ynwiki_favourite',
        'ynwiki_view',
        'ynwiki_edit',
        'ynwiki_attachment',
        'ynwiki_list',
        'ynwiki_list_item',
        'ynwiki_report',
        'ynwiki_recycle',
        'ynwiki_list',
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'ynwiki_extended' => array(
            'route' => 'wiki/:controller/:action/*',
            'defaults' => array(
                'module' => 'ynwiki',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'controller'=>'index|help|faqs',
                'action' => '(index)',
            ),
        ),
        'ynwiki_general' => array(
            'route' => 'wiki/:action/*',
            'defaults' => array(
                'module' => 'ynwiki',
                'controller' => 'index',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(browse|create|edit|delete|view|join|leave|promote|demote|remove|listing|upload-photo|set-permission|rate|history|preview-revision|restore-revision|download|print-view|follow|un-follow|un-follow-ajax|favourite|un-favourite|un-favourite-ajax|manage-follow|manage-favourite|more-space|view-user-rate|move-location|attach|suggest|delete-attach|compare-versions|report)',
            ),
        ),

    ),
); ?>
