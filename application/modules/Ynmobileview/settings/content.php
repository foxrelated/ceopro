<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

return array(
  array(
    'title' => 'YouNet Mobile Main Menu',
    'description' => 'Shows the mobile main menu. You can edit its contents in your menu editor.',
    'category' => 'YouNet Mobile View',
    'type' => 'widget',
    'name' => 'ynmobileview.mobi-menu-main',
  ),

  array(
    'title' => 'YouNet Mobile Logo',
    'category' => 'YouNet Mobile View',
    'type' => 'widget',
    'name' => 'ynmobileview.mobi-menu-logo',
    'adminForm' => 'Core_Form_Admin_Widget_Logo',
    'requirements' => array(
      'header-footer',
    ),
  ),    
  array(
	'title' => 'YouNet Mobile Login or SignUp',
	'description' => 'Displays Login or SignUp form in the homepage.',
	'category' => 'YouNet Mobile View',
	'type' => 'widget',
	'name' => 'ynmobileview.login-or-signup',
  ), 
  array(
	'title' => 'YouNet Mobile Feed',
	'description' => 'Displays Feed in the members/profile homepage.',
	'category' => 'YouNet Mobile View',
	'type' => 'widget',
	'name' => 'ynmobileview.mobi-feed',
	 'defaultParams' => array(
      'title' => 'What\'s New',
    ),
  ), 
   array(
    'title' => 'YouNet Mobile Profile Options',
    'description' => 'Displays a list of actions that can be performed on a member on their mobile profile (add as friend, etc).',
    'category' => 'YouNet Mobile View',
    'type' => 'widget',
    'name' => 'ynmobileview.mobi-profile-options',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'YouNet Mobile Group Options',
    'description' => 'Displays a list of actions that can be performed on a group.',
    'category' => 'YouNet Mobile View',
    'type' => 'widget',
    'name' => 'ynmobileview.mobi-group-options',
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title' => 'YouNet Mobile Event Options',
    'description' => 'Displays a list of actions that can be performed on a event.',
    'category' => 'YouNet Mobile View',
    'type' => 'widget',
    'name' => 'ynmobileview.mobi-event-options',
    'requirements' => array(
      'subject' => 'event',
    ),
  ),
  array(
		'title' => 'YouNet Mobile Profile Photos',
		'description' => 'Displays a user\'s photos on it\'s profile.',
		'category' => 'YouNet Mobile View',
		'type' => 'widget',
	    'isPaginated' => true,
		'name' => 'ynmobileview.mobi-profile-photos',
		'defaultParams' => array(
				'title' => 'Photos',
				'titleCount' => true,
		),
		'requirements' => array(
				'subject' => 'user',
		),
),
)
?>