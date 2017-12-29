<?php
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'advalbum',
    'version' => '4.13p2',
	'title'=>'YNC - Advanced Album',
  	'author' => '<a href="http://socialengine.younetco.com/" title="YouNetCo" target="_blank">YouNetCo</a>',
	'description' => 'Advanced Album Module.',
    'path' => 'application/modules/Advalbum',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.2',
      ),
      array(
         'type' => 'module',
         'name' => 'album',
         'minVersion' => '4.1.2',
      ),
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02p12',
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
      'path' => 'application/modules/Advalbum/settings/install.php',
      'class' => 'Advalbum_Installer',
    ),
    'directories' => array(
      'application/modules/Advalbum',
    ),
    'files' => array(
      'application/languages/en/advalbum.csv',
    ),
  ),
  // Compose -------------------------------------------------------------------
  'compose' => array(
    array('_composePhoto.tpl', 'advalbum'),
  ),
  'composer' => array(
    'photo' => array(
      'script' => array('_composePhoto.tpl', 'advalbum'),
      'plugin' => 'Advalbum_Plugin_Composer',
    ),
  ),
  // Content -------------------------------------------------------------------
  'content'=> array(
    'album_profile_albums' => array(
      'type' => 'action',
      'title' => 'Album Profile Tab',
      'route' => array(
        'module' => 'advalbum',
        'controller' => 'widget',
        'action' => 'profile-albums',
      ),
    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'advalbum_album',
    'advalbum_photo',
    'advalbum_param' ,
    'advalbum_feature',
  	'album_rating',
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Advalbum_Plugin_Core'
    ),
    array(
      'event' => 'onUserProfilePhotoUpload',
      'resource' => 'Advalbum_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteAfter',
      'resource' => 'Advalbum_Plugin_Core'
    ),
    array(
      'event' => 'onActivityActionUpdateAfter',
      'resource' => 'Advalbum_Plugin_Core'
    )
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
     'album_extended' => array(
      'route' => 'albums/:controller/:action/*',
      'defaults' => array(
        'module' => 'advalbum',
        'controller' => 'index',
        'action' => 'index'
      ),
    ),
    'album_specific' => array(
      'route' => 'albums/:action/:album_id/*',
      'defaults' => array(
        'module' => 'advalbum',
        'controller' => 'album',
        'action' => 'view'
      ),
      'reqs' => array(
        'action' => '(compose-upload|delete|edit|editphotos|upload|view|order|download|delete-photos|move-photos|load-more-photos|share-slideshow)',
      ),
    ),
    'album_general' => array(
      'route' => 'albums/:action/*',
      'defaults' => array(
        'module' => 'advalbum',
        'controller' => 'index',
        'action' => 'browse'
      ),
      'reqs' => array(
        'action' => '(listing|browse|create|list|listing-photo|manage|upload|upload-photo|browsebyuser|tagphotouser|featuredphotos|m-featuredphotos|featuredphotos-middle|create-virtual-album|suggest-tag)',
      ),
    ),
    'album_photo_specific' => array(
      'route' => 'albums/photos/:action/:album_id/:photo_id/*',
      'defaults' => array(
        'module' => 'advalbum',
        'controller' => 'photo',
        'action' => 'view'
      ),
      'reqs' => array(
        'action' => '(view|change-location|rate|edit-title|download-photo|set-album-cover|delete-photo|add-to-virtual|delete-virtual-photo)',
      ),
    ),
    'album_photo_delete' => array(
      'route' => 'albums/photos/:photo_id/*',
      'defaults' => array(
        'module' => 'advalbum',
        'controller' => 'photo',
        'action' => 'delete'
      ),
      'reqs' => array(
    	'photo_id' => '\d+',
    	'action' => '(delete)',
      ),
    ),
    'album_admin_manage_level' => array(
      'route' => 'admin/advalbum/level/:level_id',
      'defaults' => array(
        'module' => 'advalbum',
        'controller' => 'admin-level',
        'action' => 'index',
        'level_id' => 1
      )
    ),
    'album_admin_manage_album' => array(
      'route' => 'admin/manage',
      'defaults' => array(
        'module' => 'advalbum',
        'controller' => 'admin-manage',
        'action' => 'index',
      )
    ),
  ),
);