<?php
return array(
	array(
		'title' => 'Profile Albums',
		'description' => 'Displays a member\'s albums on their profile.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.profile-albums',
		'defaultParams' => array(
			'title' => 'Albums',
			'titleCount' => true,
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'list',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
			)),
	),
	array(
		'title' => 'Albums Menu',
		'description' => 'Displays albums main menu.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.albums-menu',
		'adminForm'=> array(
			'elements' => array(
				array(
					'Text',
					'title',
					array(
						'label' => 'Title'
					)
				),
			)),
	),
	array(
		'title' => 'Albums Categories',
		'description' => 'Displays albums categories.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.albums-categories',
		'defaultParams' => array(
			'title' => 'Categories',
		),
		'adminForm'=> array(
			'elements' => array(
				array(
					'Text',
					'title',
					array(
						'label' => 'Title'
					)
				),
			)),
	),
	array(
		'title' => 'Albums Statistics',
		'description' => 'Displays albums statistic.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.albums-statistics',
		'defaultParams' => array(
			'title' => 'Statistics',
		),
		'adminForm'=> array(
			'elements' => array(
				array(
					'Text',
					'title',
					array(
						'label' => 'Title'
					)
				),
			)),
	),
	array(
		'title' => 'Albums Top Members',
		'description' => 'Displays top members.',
		'category' => 'Advanced Albums',
		'name' => 'advalbum.albums-top-members',
		'type' => 'widget',
		'defaultParams' => array(
			'title' => 'Top Members',
		),
		'adminForm'=> array(
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
					'number',
					array(
						'label' =>  'Number of members to display',
						'value' => '9',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
			)),
	),
	array(
		'title' => 'Recent Photos',
		'description' => 'Displays recent added photos.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.recent-photos',
		'defaultParams' => array(
			'title' => 'Recent Photos',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),

	),
	array(
		'title' => 'Featured Albums',
		'description' => 'Displays featured album.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.featured-albums',
		'defaultParams' => array(
			'title' => 'Featured Albums',
		),
		'adminForm'=> array(
			'elements' => array(
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,36)),
						),
					),
				),

			),
		),

	),
	array(
		'title' => 'Most Viewed Photos (Popular Photos)',
		'description' => 'Displays most viewed photos.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.most-viewed-photos',
		'defaultParams' => array(
			'title' => 'Popular Photos',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),

	),
	array(
		'title' => 'Most Commented Photos (Hot Photos)',
		'description' => 'Displays most commented photos.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.most-commented-photos',
		'defaultParams' => array(
			'title' => 'Hot Photos',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),

	),
	array(
		'title' => 'Most Liked Photos (Top Photos)',
		'description' => 'Displays most liked photos.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.most-liked-photos',
		'defaultParams' => array(
			'title' => 'Top Photos',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),

				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),

	),
	array(
		'title' => "Today's Photos",
		'description' => 'Displays photos added today.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.today-photos',
		'defaultParams' => array(
			'title' => "Today's Photos",
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),
	),
	array(
		'title' => "This Week's Photos",
		'description' => 'Displays photos added this week.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.this-week-photos',
		'defaultParams' => array(
			'title' => "This Week's Photos",
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),

				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),
	),
	array(
		'title' => "This Month's Photos",
		'description' => 'Displays photos added this month .',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.this-month-photos',
		'defaultParams' => array(
			'title' => "This Month's Photos",
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),

	),
	array(
		'title' => 'Featured Photos',
		'description' => 'Displays featured photos.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.featured-photos',
		'defaultParams' => array(
			'title' => 'Featured Photos',
		),
		'adminForm'=> array(
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
					'max',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
			)),
	),
	array(
		'title' => 'Albums Search',
		'description' => 'Displays albums search panel.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.albums-search',
		'adminForm' => array(
			'elements' => array(
				array(
					'Text',
					'title',
					array(
						'label' => 'Title'
					)
				),
			)),
	),
	array(
		'title' => 'Quick Links',
		'description' => 'Displays quick links.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.albums-quick-links',
		'adminForm' => array(
			'elements' => array(
				array(
					'Text',
					'title',
					array(
						'label' => 'Title'
					)
				),
			)),
	),
	array(
		'title' => 'Photos Search',
		'description' => 'Displays photos search panel.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.photos-search',
		'adminForm' => array(
			'elements' => array(
				array(
					'Text',
					'title',
					array(
						'label' => 'Title'
					)
				),
			)),
	),
	array(
		'title' => 'Albums Listing',
		'description' => 'Displays list of albums in albums listing page.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.albums-listing',
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'list',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '10',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,20)),
						),
					),
				),
			)),
	),
	array(
		'title' => 'My Albums Listing',
		'description' => 'Displays list of albums in my albums page.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.albums-manage',
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
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '10',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,20)),
						),
					),
				),
			)),
	),
	array(
		'title' => 'Photos Listing',
		'description' => 'Displays list of photos in photos listing page.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.photos-listing',
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '10',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,20)),
						),
					),
				),
			)),
	),
	array(
		'title' => 'Album\'s Photos Listing',
		'description' => 'Displays list of photos of an album.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.photos-of-album',
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '12',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,20)),
						),
					),
				),
			)),
	),
	array(
		'title' => 'User Other Albums',
		'description' => 'Displays user other albums',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.user-other-albums',
		'defaultParams' => array(
			'title' => '',
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
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '4',
						'required' => true,
						'validators' => array(
							array('Between', true, array(1,12)),
						),
					),
				),
			)),

	),
	array(
		'title' => 'Most Liked Albums (Top Albums)',
		'description' => 'Displays top albums (most liked)',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.most-liked-albums',
		'defaultParams' => array(
			'title' => 'Top Albums',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'list',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '4',
						'required' => true,
						'validators' => array(
							array('Between', true, array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),
	),
	array(
		'title' => 'Recent Albums',
		'description' => 'Number of albums to display.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.recent-albums',
		'defaultParams' => array(
			'title' => 'Recent Albums',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'list',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '4',
						'required' => true,
						'validators' => array(
							array('Between', true, array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),
	),
	array(
		'title' => 'Most Commented Albums (Hot Albums)',
		'description' => 'Displays most commented albums.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.most-commented-albums',
		'defaultParams' => array(
			'title' => 'Hot Albums',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'list',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '4',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),

		),
	),
	array(
		'title' => 'Most Viewed Albums (Popular Albums)',
		'description' => 'Displays most viewed albums.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.most-viewed-albums',
		'defaultParams' => array(
			'title' => 'Popular Albums',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'list',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '4',
						'required' => true,
						'validators' => array(
							array('Between',true,array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),
	),
	array(
		'title' => 'Random Albums',
		'description' => 'Number of albums to display.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.random-albums',
		'defaultParams' => array(
			'title' => 'Random Albums',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'list',
					)
				),
				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of albums to display',
						'value' => '4',
						'required' => true,
						'validators' => array(
							array('Between', true, array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),
	),
	array(
		'title' => 'Random Photos',
		'description' => 'Number of photos to display.',
		'category' => 'Advanced Albums',
		'type' => 'widget',
		'name' => 'advalbum.random-photos',
		'defaultParams' => array(
			'title' => 'Random Photos',
		),
		'adminForm'=> array(
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
					'mode_pinterest',
					array(
						'label' => 'Pinterest view.',
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
							'grid' => 'Grid view.',
							'pinterest' => 'Pinterest view.',
						),
						'value' => 'grid',
					)
				),

				array(
					'Text',
					'number',
					array(
						'label' =>  'Number of photos to display',
						'value' => '8',
						'required' => true,
						'validators' => array(
							array('Between', true, array(1,12)),
						),
					),
				),
				array(
					'Radio',
					'ajax',
					array(
						'label' =>  'Load by ajax?',
						'value' => '0',
						'required' => true,
						'multiOptions' => array(
							1 => 'Yes, load this widget by ajax.',
							0 => 'No, do not load this widget by ajax.'
						),
					),
				),
			),
		),
	),
) ?>