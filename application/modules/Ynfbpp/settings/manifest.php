<?php
return array(
    'package' => array(
        'type' => 'module',
        'name' => 'ynfbpp',
        'version' => '4.02p1',
        'path' => 'application/modules/Ynfbpp',
        'title' => 'YNC - Profile Popup',
        'description' => 'Profile Popup',
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.2',
            ),
            array(
                'type' => 'module',
                'name' => 'younet-core',
                'minVersion' => '4.02p13',
            ),
        ),
        'callback' => array('class' => 'Engine_Package_Installer_Module', ),
        'actions' => array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' => array(0 => 'application/modules/Ynfbpp', ),
        'files' => array(0 => 'application/languages/en/ynfbpp.csv', ),

    ),
    'routes'=> array(
        'ynfbpp_general' =>
            array (
                'route' => 'ynfbpp/:action/*',
                'defaults' =>
                    array (
                        'module' => 'ynfbpp',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
            ),
    )
);
?>