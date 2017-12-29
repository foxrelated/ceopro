<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 10163 2014-04-11 19:50:10Z andres $
 * @author     John
 */
return array(
  array(
    'title' => 'HTML Block',
    'description' => 'Inserts any HTML of your choice.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.html-block',
    'special' => 1,
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title'
          )
        ),
        array(
          'Text',
          'adminTitle',
          array(
            'label' => 'Admin Title',
            'maxlength' => 64,
          )
        ),
        array(
          'Textarea',
          'data',
          array(
            'label' => 'HTML'
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Rich Text Block',
    'description' => 'Inserts rich text using a rich text editor.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.rich-text-block',
    'special' => 1,
    'autoEdit' => true,
    'adminForm' => 'Core_Form_Admin_Widget_RichText',
  ),
  array(
    'title' => 'Ad Campaign',
    'description' => 'Shows one of your ad banners. Requires that you have at least one active ad campaign.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.ad-campaign',
   // 'special' => 1,
    'autoEdit' => true,
    'adminForm' => 'Core_Form_Admin_Widget_Ads',
  ),
  array(
    'title' => 'Banner',
    'description' => 'Shows one of your banners. Requires that you have at least one banner.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.banner',
    'autoEdit' => true,
    'adminForm' => 'Core_Form_Admin_Widget_Banner',
  ),
  array(
    'title' => 'Tab Container',
    'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.container-tabs',
    'special' => 1,
    'defaultParams' => array(
      'max' => 6
    ),
    'canHaveChildren' => true,
    'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    'adminForm' => 'Core_Form_Admin_Widget_Container',
  ),
  array(
    'title' => 'Content',
    'description' => 'Shows the page\'s primary content area. (Not all pages have primary content)',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.content',
    'requirements' => array(
      'page-content',
    ),
  ),
  array(
    'title' => 'Footer Menu',
    'description' => 'Shows the site-wide footer menu. You can edit its contents in your menu editor.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.menu-footer',
    'requirements' => array(
      'header-footer',
    ),
  ),
  array(
    'title' => 'Generic Menu',
    'description' => 'Shows a selected menu. You can edit its contents in your menu editor.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.menu-generic',
    'adminForm' => 'Core_Form_Admin_Widget_MenuGeneric',
  ),
  array(
    'title' => 'Main Menu',
    'description' => 'Shows the site-wide main menu. You can edit its contents in your menu editor.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.menu-main',
    'requirements' => array(
      'header-footer',
    ),
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'menuType',
          array(
            'label' => 'Menu Position',
            'description'=> 'Choose position of this menu below.',
            'multiOptions' => array(
                'horizontal' => 'Horizontal',
                'vertical' => 'Vertical'
            ),
            'value' => 'horizontal'
        )),
      )
    ),
  ),
  array(
    'title' => 'Mini Menu',
    'description' => 'Shows the site-wide mini menu. You can edit its contents in your menu editor.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.menu-mini',
    'requirements' => array(
      'header-footer',
    ),
    'adminForm' => 'Core_Form_Admin_Widget_MiniMenu',
  ),
  array(
    'title' => 'Mini Search',
    'description' => 'Shows the site-wide mini search.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.search-mini',
    'requirements' => array(
      'header-footer',
    ),
  ),
  array(
    'title' => 'Site Logo',
    'description' => 'Shows your site-wide main logo or title.  Images are uploaded via the <a href="admin/files" target="_parent">File Media Manager</a>.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.menu-logo',
    'adminForm' => 'Core_Form_Admin_Widget_Logo',
    'requirements' => array(
      'header-footer',
    ),
  ),
array(
    'title' => 'Social Site Links Menu',
    'description' => 'Shows the social-sites footer menu. You can edit its contents in your menu editor.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.menu-social-sites',
    'requirements' => array(
      'header-footer',
    ),
  ),
  array(
    'title' => 'Profile Links',
    'description' => 'Displays a member\'s, group\'s, or event\'s links on their profile.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.profile-links',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Links',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject',
    ),
  ),
  array(
    'title' => 'Statistics',
    'description' => 'Shows some basic usage statistics about your community.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.statistics',
    'defaultParams' => array(
      'title' => 'Statistics'
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Comments',
    'description' => 'Shows the comments about an item.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.comments',
    'defaultParams' => array(
      'title' => 'Comments'
    ),
    'requirements' => array(
      'subject',
    ),
  ),
  array(
    'title' => 'Theme Chooser',
    'description' => 'Allows a member to switch to any of the currently installed themes.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.theme-choose',
    'defaultParams' => array(
      'title' => 'Themes'
    ),
  ),
  array(
    'title' => 'Contact Form',
    'description' => 'Displays the contact form.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.contact',
    'requirements' => array(
      'no-subject',
    ),
    'defaultParams' => array(
      'title' => 'Contact',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Search Bar',
    'description' => 'Add the ability to search your site\'s content on any page.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.search',
  ),
  array(
    'title' => 'Social Share',
    'description' => 'Add the ability to share the content on the other social sites.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.social-share',
  ),
  array(
    'title' => 'Column Width Modifier',
    'description' => 'This widget can be placed in any column of a page to modify its width. New width will need to be entered in the settings of the widget.',
    'category' => 'Core',
    'type' => 'widget',
    'name' => 'core.layout-column-width',
    'type' => 'widget',
    'special' => 1,
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'hidden',
          'title',
          array(
            'label' => ''
          )
        ),
        array(
          'text',
          'columnWidth',
          array(
            'label' => 'Enter the width of this column.',
            'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(1)),
            ),
          )
        ),
        array(
          'select',
          'columnWidthType',
          array(
            'label' => 'Select the type of width',
            'multiOptions' => array(
              'px' => 'px',
              '%' => '%',
            ),
            'value' => 'px',
          )
        ),
      )
    )
  ),
) ?>
