<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'ynmobileview',
    'version' => '4.04p3',
    'path' => 'application/modules/Ynmobileview',
    'repository' => 'socialengine.younetco.com',
    'title' => 'YN - Mobile View',
    'description' => 'YouNet Mobile View',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.5.0',
      ),
       array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Ynmobileview/settings/install.php',
      'class' => 'Ynmobileview_Installer',
    ),
    'directories' => array(
      0 => 'application/modules/Ynmobileview'
    ),
    'files' => array(
      'application/languages/en/ynmobileview.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
   'hooks' => 
  array (
    0 => 
    array (
      'event' => 'onItemCreateAfter',
      'resource' => 'Ynmobileview_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'mobi_feed' => array(
      'route' => 'mobi/status/:action_id/*',
      'defaults' => array(
        'module' => 'ynmobileview',
        'controller' => 'feed',
        'action' => 'status',
      ),
      'reqs' => array(
        'id' => '\d+',
      )
    ),
    'ynmobi_login' => array(
      'route' => 'ynmobile/login/*',
      'defaults' => array(
        'module' => 'ynmobileview',
        'controller' => 'index',
        'action' => 'login',
      ),
    ),
    'ynmobi_cover' => array(
      'route' => 'ynmobile/edit-cover/*',
      'defaults' => array(
        'module' => 'ynmobileview',
        'controller' => 'index',
        'action' => 'edit-cover',
      ),
    ),
    'ynmobi_remove_cover' => array(
      'route' => 'ynmobile/remove-cover/*',
      'defaults' => array(
        'module' => 'ynmobileview',
        'controller' => 'index',
        'action' => 'remove-cover',
      ),
    ),
  )
) ?>