<?php return array (
  'package' =>
  array (
    'type' => 'theme',
    'name' => 'insignia',
    'version' => '4.9.4p4',
    'revision' => '$Revision: 10113 $',
    'path' => 'application/themes/insignia',
    'repository' => 'socialengine.com',
    'title' => 'Insignia',
    'thumb' => 'theme.jpg',
    'author' => 'Webligo Developments',
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' =>
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' =>
    array (
      0 => 'application/themes/insignia',
    ),
    'description' => 'Insignia',
  ),
  'files' =>
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
  'colorVariants' => array(
    'insignia-black' => array(
      'title' => 'Insignia Black',
    ),
    'insignia-green' => array(
      'title' => 'Insignia Green',
    ),
    'insignia-orange' => array(
      'title' => 'Insignia Orange',
    ),
    'insignia-pink' => array(
      'title' => 'Insignia Pink',
    ),
    'insignia-purple' => array(
      'title' => 'Insignia Purple',
    ),
  ),
); ?>