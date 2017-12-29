<?php
/**
 *
 * @category   Application_Extensions
 * @package    Ynmember
 * @copyright  Copyright 2014 Younet
 * @license    http://socialengine.younetco.com
 * @author     LONGL
 */
return array(
	array(
	    'title' => 'Advanced Member Members Listing',
	    'description' => 'Displays members listing on member browse page.',
	    'category' => 'Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.members-listing',
	    'isPaginated' => true,
				'defaultParams' => array(
						'title' => '',
				),
				'requirements' => array(
						'no-subject',
				),
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
							'Heading',
							'mode_enabled',
							array(
								'label' => 'Which view modes are enabled?'
							)
						),
						array(
								'Radio',
								'mode_list',
								array(
									'label' => 'List view.',
									'multiOptions' => array(
										1 => 'Yes.',
										0 => 'No.',
									),
									'value' => 1,
								)
						),
						array(
								'Radio',
								'mode_grid',
								array(
									'label' => 'Grid view.',
									'multiOptions' => array(
										1 => 'Yes.',
										0 => 'No.',
									),
									'value' => 1,
								)
						),
						array(
								'Radio',
								'mode_pin',
								array(
									'label' => 'Pin view.',
									'multiOptions' => array(
										1 => 'Yes.',
										0 => 'No.',
									),
									'value' => 1,
								)
						),
						array(
								'Radio',
								'mode_map',
								array(
									'label' => 'Map view.',
									'multiOptions' => array(
										1 => 'Yes.',
										0 => 'No.',
									),
									'value' => 1,
								)
						),
						array(
								'Radio',
								'view_mode',
								array(
										'label' => 'Which view mode is default?',
										'multiOptions' => array(
											'list' => 'List view.',
											'grid' => 'Grid view.',
											'pin' => 'Pin view.',
											'map' => 'Map view.',
										),
										'value' => 'list',
								)
						),
						
					)
				),
 	),
 	array(
	    'title' => 'Advanced Member Member Birthday',
	    'description' => 'List the member birthday.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.member-birthday',
	    'requirements' => array(
	      'no-subject',
	    ),
	     'defaultParams' => array(
            'title' => 'Birthday Today',
        ),
	),
 	array(
	    'title' => 'Advanced Member Member Of Day',
	    'description' => 'List the member of day.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.member-of-day',
	    'requirements' => array(
	      'no-subject',
	    ),
	     'defaultParams' => array(
            'title' => 'Member Of Day',
        ),
	),
	array(
	    'title' => 'Advanced Member Browse Member',
	    'description' => 'List members.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.browse-members',
	    'requirements' => array(
	      'no-subject',
	    ),
	    'isPaginated' => true,
	     'defaultParams' => array(
            'title' => 'Browse Member',
        ),
	),
	array(
	    'title' => 'Advanced Member Browse Birthday Members',
	    'description' => 'List birthday members.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.browse-birthday-members',
	    'requirements' => array(
	      'no-subject',
	    ),
	    'isPaginated' => true,
	     'defaultParams' => array(
            'title' => '',
        ),
	),
	
	array(
	    'title' => 'Advanced Member Review Search',
	    'description' => 'Displays a search form in the review browse page.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.search-review',
	    'requirements' => array(
	      'no-subject',
	    ),
	),
	array(
	    'title' => 'Advanced Member Top Rated Member',
	    'description' => 'List top rated members.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.most-rating-members',
	    'requirements' => array(
	      'no-subject',
	    ),
	    'isPaginated' => true,
	     'defaultParams' => array(
            'title' => 'Top Rated Member',
        ),
	),
	array(
	    'title' => 'Advanced Member Most Reviewed Member',
	    'description' => 'List most reviewed members.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.most-reviewed-members',
	    'requirements' => array(
	      'no-subject',
	    ),
	    'isPaginated' => true,
	     'defaultParams' => array(
            'title' => 'Most Reviewed Member',
        ),
	),
	array(
	    'title' => 'Advanced Member People You May Know',
	    'description' => 'List members that user may know.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.people-may-know',
	    'requirements' => array(
	      'no-subject',
	    ),
	    'isPaginated' => true,
	     'defaultParams' => array(
            'title' => 'People You May Know',
        ),
	),
	array(
        'title' => 'Advanced Member General Reviews',
        'description' => 'Displays a member\'s reviews.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.general-review',
        'isPaginated' => true,
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'General Reviews',
        ),
    ),
    array(
        'title' => 'Advanced Member Full Reviews',
        'description' => 'Displays a member\'s full reviews.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.full-review',
        'isPaginated' => true,
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Full Reviews',
        ),
    ),
    array(
        'title' => 'Advanced Member Featured Members',
        'description' => 'Displays featured members. Default count = 7. (Count > 0)',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.featured-members',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Featured Members',
        ),
    ),
    array(
	    'title' => 'Advanced Member User Review For',
	    'description' => 'Displays users a member reviewed for.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.member-review-for',
        'isPaginated' => true,
        'requirements' => array(
            'subject' => 'ynmember_review',
        ),
        'defaultParams' => array(
            'title' => 'This Member Also Review For',
        ),
	),    
    array(
        'title' => 'Advanced Member Profile Cover',
        'description' => 'Displays a member\'s profile cover data on their profile.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.profile-cover',
        'requirements' => array(
	      'subject' => 'user',
	    ),
    ),
    array(
        'title' => 'Advanced Member Profile Statistics',
        'description' => 'Displays a member\'s profile statistics data on their profile.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.profile-statistics',
        'requirements' => array(
	      'subject' => 'user',
	    ),
    ),
    array(
	    'title' => 'Advanced Member Profile Fields',
	    'description' => 'Displays a member\'s profile field data on their profile.',
	    'category' => 'YNC - Advanced Member',
	    'type' => 'widget',
	    'name' => 'ynmember.profile-fields',
	    'defaultParams' => array(
	      'title' => 'Info',
	    ),
	    'requirements' => array(
	      'subject' => 'user',
	    ),
   ),
   array(
        'title' => 'Advanced Member Browse Menu',
        'description' => 'Displays a menu in the member browse page.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
   ),
   array(
        'title' => 'Advanced Member Browse Search',
        'description' => 'Displays a search form in the member browse page.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.browse-search',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => 'Search Members',
        ),
    ),
     array(
	    'title' => 'Advanced Member Profile General Ratings Widget',
	    'description' => 'Displays general ratings of member.',
        'category' => 'YNC - Advanced Member',
        'type' => 'widget',
        'name' => 'ynmember.general-rating',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'General Rating',
        ),
	),
);