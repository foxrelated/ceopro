<?php
return array(
    array(
    'title' => 'Menu Pages',
    'description' => 'Displays menu pages on browse wiki page.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.menu-pages',
  ),
  array(
    'title' => 'Tag Pages',
    'description' => 'Displays tag pages on browse wiki page.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.tag-pages',
  ),
  array(
    'title' => 'Search Pages',
    'description' => 'Displays search pages on browse wiki page.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.search-pages',
  ),
   array(
    'title' => 'Listing Pages',
    'description' => 'Displays lisitng pages on search wiki page.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.listing-pages',
    'defaultParams' => array(
      'title' => 'Wiki Search',
    ),
  ),
   array(
    'title' => 'Profile Edited Wiki Pages',
    'description' => 'Displays recently edited wiki page on user profile page.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.profile-edit-pages',
    'defaultParams' => array(
      'title' => 'Recently Edited Pages',
      'titleCount' => true,
    ),
  ),
    array(
    'title' => 'Most Commented WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.most-commented-pages',
    'defaultParams' => array(
      'title' => 'Most Commented',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Most Followed WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.most-followed-pages',
    'defaultParams' => array(
      'title' => 'Most Followed',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Wiki Spaces',
    'description' => 'Number of space to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.wiki-space-pages',
    'defaultParams' => array(
      'title' => 'Wiki Spaces',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '10',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,10)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Most Rated WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.most-rated-pages',
    'defaultParams' => array(
      'title' => 'Most Rated',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Most Viewed WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.most-viewed-pages',
    'defaultParams' => array(
      'title' => 'Most Viewed',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Most Favourite WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.most-favourite-pages',
    'defaultParams' => array(
      'title' => 'Most Favourite',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Recently Updated  WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.recent-updated-pages',
    'defaultParams' => array(
      'title' => 'Recently Updated',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Recently I Viewed WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.recent-viewed-pages',
    'defaultParams' => array(
      'title' => 'Recently I Viewed',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Recently I Edited WIKI pages',
    'description' => 'Number of pages to display.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.recent-edited-pages',
    'defaultParams' => array(
      'title' => 'Recently I Edited',
    ),
      'adminForm'=> array(
      'elements' => array(
          array(
              'Text',
              'number',
               array(
                'label' =>  'Number of pages to display',
                'value' => '5',
                'required' => true,
                'validators' => array(
                    array('Between', true, array(1,12)),
                ),
               ),
           ),
       ),
       ),  
  ),
  array(
    'title' => 'Display specific page',
    'description' => 'Display specific page.',
    'category' => 'Wiki Page',
    'type' => 'widget',
    'name' => 'ynwiki.group-profile-pages',
    'defaultParams' => array(
      'title' => 'Wiki page',
    ),
    'special' => 1,
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'TextYn',
          'to',
          array(
              'label' => 'Search Wiki Page'
          )
        ),
        array(
          'Text',
          'title',
          array(
            'label' => 'Tab Title'
          )
        ),
         array(
            'Text',
            'toValues',
            array(
            'label' => 'Page ID'  
            )
        ),
      ),
      'prefixPath' => array('prefix'=>'Ynwiki_Widget_','path'=>APPLICATION_PATH .'/application/modules/Ynwiki/widgets/group-profile-pages','type'=>'element')
    )
  ),
	
	array(
		'title' => 'Wiki Profile Detail',
		'description' => 'Displays wiki profile detail page.',
		'category' => 'Wiki Page',
		'type' => 'widget',
		'name' => 'ynwiki.profile-detail',
		'defaultParams' => array(
				'title' => 'Detail',
		),
	),

		
	array(
		'title' => 'Wiki Profile Options',
		'description' => 'Displays wiki profile options page.',
		'category' => 'Wiki Page',
		'type' => 'widget',
		'name' => 'ynwiki.profile-options',
		'defaultParams' => array(
				'title' => 'Wiki Profile Options',
		),
	),
	array(
		'title' => 'Wiki Profile Members',
		'description' => 'Displays a Wiki\'s members on its profile.',
		'category' => 'Wiki Page',
		'type' => 'widget',
		'name' => 'ynwiki.profile-members',
		'isPaginated' => true,
		'defaultParams' => array(
				'title' => 'Members',
				'titleCount' => true,
		),
		'requirements' => array(
				'subject' => 'ynwiki',
		),
	),
	array(
		'title' => 'Wiki Menus',
		'description' => 'Displays wiki menus.',
		'category' => 'Wiki Page',
		'type' => 'widget',
		'name' => 'ynwiki.profile-menus',
	),
		
 
) ?>
