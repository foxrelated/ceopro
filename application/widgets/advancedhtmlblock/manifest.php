<?php
return array(
    'package' => array(
        'type' => 'widget',
        'name' => 'advancedhtmlblock',
        'version' => '4.02p5',
        'path' => 'application/widgets/advancedhtmlblock',
        'title' => 'YNC - Advanced Html Block',
        'description' => 'Advanced Html Block',
        'category' => 'Core',
        'special' => 1,
        'autoEdit' => true,
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
        'actions' => array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'remove',
        ),
        'directories' => array(0 => 'application/widgets/advancedhtmlblock',),
        'files' => array(0 => 'application/modules/Core/Form/Admin/Younetadvancedhtmlblock.php'),
    ),
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'advancedhtmlblock',
    'version' => '4.02p4',
    'title' => 'YNC - Advanced Html Block',
    'description' => 'YouNet Advanced Html Block',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
    'special' => 1,
    'autoEdit' => true,
    'adminForm' => 'Core_Form_Admin_Younetadvancedhtmlblock',
);
?>