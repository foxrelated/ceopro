<?php
return array(
	array(
	    'title' => 'YNC - Business Profile Members',
	    'description' => 'Displays a business\'s members on it\'s profile.',
	    'category' => 'YNC - Business',
	    'type' => 'widget',
	    'name' => 'ynbusinesspages.business-profile-members',
	    'isPaginated' => true,
	    'defaultParams' => array(
	      'title' => 'Members',
	      'titleCount' => true,
	      'itemCountPerPage' => 10
	    ),
	    'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
	),
	
	array(
        'title' => 'YNC - Business Main Menu',
        'description' => 'Displays main menu.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.main-menu'
	),
	
	array(
        'title' => 'YNC - Business Profile Reviews',
        'description' => 'Displays a list of reviews on the business.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-reviews',
        'isPaginated' => true,
        'defaultParams' => array(
          'title' => 'Reviews',
          'titleCount' => true,
          'itemCountPerPage' => 4
        ),
        'requirements' => array(
          'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Check-in',
        'description' => 'Displays a list of user has checked-in on the business.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-checkins',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'People check-in here',
            'itemCountPerPage' => 20,
        ),
        'requirements' => array(
          'subject' => 'ynbusinesspages_business',
        ),
    ),
	
	array(
	    'title' => 'YNC - Business Create Link',
	    'description' => 'Displays business create link.',
	    'category' => 'YNC - Business',
	    'type' => 'widget',
	    'name' => 'ynbusinesspages.business-create-link',
    ),
	
	array(
	    'title' => 'YNC - Business Profile Announcements',
	    'description' => 'Displays recent announcements.',
	    'category' => 'YNC - Business',
	    'type' => 'widget',
	    'name' => 'ynbusinesspages.business-profile-announcements',
	    'defaultParams' => array(
            'title' => 'Announcements',
        ),
	    'requirements' => array(
	      'subject'=>'ynbusinesspages_business',
	    ),
    ),
	
	array(
	    'title' => 'YNC - Business Profile Operating Hours',
	    'description' => 'Displays business operating hours.',
	    'category' => 'YNC - Business',
	    'type' => 'widget',
	    'name' => 'ynbusinesspages.business-profile-operating-hours',
	    'defaultParams' => array(
            'title' => 'Operating Hours',
        ),
	    'requirements' => array(
	      'subject'=>'ynbusinesspages_business',
	    ),
    ),
	
	array(
	    'title' => 'YNC - Business Profile Contact Information',
	    'description' => 'Displays business contact information.',
	    'category' => 'YNC - Business',
	    'type' => 'widget',
	    'name' => 'ynbusinesspages.business-profile-contact-info',
	    'defaultParams' => array(
            'title' => 'Contact Information',
        ),
	    'requirements' => array(
	      'subject'=>'ynbusinesspages_business',
	    ),
	    'adminForm' => array(
            'elements' => array(
				array(
					'Heading',
					'heading',
					array(
						'label' => 'Show/hide all information?'
					)
				),
				array(
					'Radio',
					'phone',
					array(
						'label' => 'Phone',
						'multiOptions' => array(
							1 => 'Yes.',
							0 => 'No.',
						),
						'value' => 1,
					)
				),
				array(
					'Radio',
					'fax',
					array(
						'label' => 'Fax',
						'multiOptions' => array(
							1 => 'Yes.',
							0 => 'No.',
						),
						'value' => 1,
					)
				),
				array(
                    'Radio',
                    'email',
                    array(
                        'label' => 'Email',
                        'multiOptions' => array(
                            1 => 'Yes.',
                            0 => 'No.',
                        ),
                        'value' => 1,
                    )
                ),
				array(
					'Radio',
					'website',
					array(
						'label' => 'Website',
						'multiOptions' => array(
							1 => 'Yes.',
							0 => 'No.',
						),
						'value' => 1,
					)
				),
				array(
					'Radio',
					'facebook',
					array(
						'label' => 'Facebook',
						'multiOptions' => array(
							1 => 'Yes.',
							0 => 'No.',
						),
						'value' => 1,
					)
				),
				array(
					'Radio',
					'twitter',
					array(
						'label' => 'Twitter',
						'multiOptions' => array(
							1 => 'Yes.',
							0 => 'No.',
						),
						'value' => 1,
					)
				),
			)
		)
    ),
	
	array(
        'title' => 'YNC - Business Profile Cover Style 1',
        'description' => 'Displays Business Cover on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-cover-style1',
        'defaultParams' => array(
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
     array(
        'title' => 'YNC - Business Profile Cover Style 2',
        'description' => 'Displays Business Cover on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-cover-style2',
        'defaultParams' => array(
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Cover Style 3',
        'description' => 'Displays Business Cover on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-cover-style3',
        'defaultParams' => array(
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
	
	 array(
        'title' => 'YNC - Business Profile Overview',
        'description' => 'Displays Business Overview on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-overview',
        'defaultParams' => array(
            'title' => 'Overview',
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Contact',
        'description' => 'Displays Business Contact Form on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-contact',
        'defaultParams' => array(
            'title' => 'Contact Us',
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
	
	array(
        'title' => 'YNC - Business Profile Photos',
        'description' => 'Displays Business Photos on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
            'itemCountPerPage' => 8
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Discussions',
        'description' => 'Displays Business Discussions on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-discussions',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
            'itemCountPerPage' => 5
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Videos',
        'description' => 'Displays Business Videos on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
            'itemCountPerPage' => 5
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Events',
        'description' => 'Displays Business Events on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
            'itemCountPerPage' => 5
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Wikis',
        'description' => 'Displays Business Wikis on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-wikis',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Wikis',
            'titleCount' => true,
            'itemCountPerPage' => 5
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Files',
        'description' => 'Displays Business Files on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-files',
        'defaultParams' => array(
            'title' => 'Files',
            'titleCount' => true,
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
	
    array(
        'title' => 'YNC - Business Profile Followers',
        'description' => 'Displays Business Followers on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-followers',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Followers',
            'titleCount' => true,
            'itemCountPerPage' => 15
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
	   
    array(
        'title' => 'YNC - Business Profile Musics',
        'description' => 'Displays Business Musics on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-musics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Musics',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
	      'subject' => 'ynbusinesspages_business',
	    ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Mp3Musics',
        'description' => 'Displays Business Mp3Musics on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-mp3musics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Mp3Musics',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Blogs',
        'description' => 'Displays Business Blogs on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-blogs',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Blogs',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Classifieds',
        'description' => 'Displays Business Classifieds on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-classifieds',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Classifieds',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Groupbuys',
        'description' => 'Displays Business Groupbuys on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-groupbuys',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Groupbuys',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Contests',
        'description' => 'Displays Business Contests on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-contests',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Contests',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Listings',
        'description' => 'Displays Business Listings on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Listings',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Polls',
        'description' => 'Displays Business Polls on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-polls',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Polls',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Jobs',
        'description' => 'Displays Business Jobs on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-jobs',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Jobs',
            'titleCount' => true,
            'itemCountPerPage' => 10
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Social Music Albums',
        'description' => 'Displays Business Social Music Albums on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-ynmusic-album',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Social Music Albums',
            'titleCount' => true,
            'itemCountPerPage' => 8
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
	array(
        'title' => 'YNC - Business Profile Social Music Songs',
        'description' => 'Displays Business Social Music Songs on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-ynmusic-song',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Social Music Songs',
            'titleCount' => true,
            'itemCountPerPage' => 8
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),

	array(
		'title' => 'YNC - Business Profile Ultimate Video',
		'description' => 'Displays Business Ultimate Videos on Business Detail page',
		'category' => 'YNC - Business',
		'type' => 'widget',
		'name' => 'ynbusinesspages.business-profile-ynultimatevideo-videos',
		'isPaginated' => true,
		'defaultParams' => array(
			'title' => 'Ultimate Video',
			'titleCount' => true,
			'itemCountPerPage' => 8
		),
		'requirements' => array(
			'subject' => 'ynbusinesspages_business',
		),
	),

    array(
        'title' => 'YNC - Business Profile Employees',
        'description' => 'Displays Business Employees on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-employees',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Employees',
            'titleCount' => true,
            'itemCountPerPage' => 8
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),

    array(
        'title' => 'YNC - Business Profile Students and Alumni',
        'description' => 'Displays Business Students and Alumni on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-students-and-alumni',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Students and Alumni',
            'titleCount' => true,
            'itemCountPerPage' => 8
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),

	array(
		'title' => 'YNC - Business Profile VideoChannel Video',
		'description' => 'Displays Business VideoChannel Videos on Business Detail page',
		'category' => 'YNC - Business',
		'type' => 'widget',
		'name' => 'ynbusinesspages.business-profile-ynvideochannel-videos',
		'isPaginated' => true,
		'defaultParams' => array(
			'title' => 'VideoChannel Video',
			'titleCount' => true,
			'itemCountPerPage' => 8
		),
		'requirements' => array(
			'subject' => 'ynbusinesspages_business',
		),
	),

	array(
		'title' => 'YNC - Business Newest Ultimate Video',
		'description' => 'Displays Newest Ultimate Videos on Business Detail page',
		'category' => 'YNC - Business',
		'type' => 'widget',
		'name' => 'ynbusinesspages.business-newest-ynultimatevideo-video',
		'isPaginated' => true,
		'defaultParams' => array(
			'title' => 'Newest Ultimate Videos',
			'itemCountPerPage' => 1
		),
		'requirements' => array(
			'subject' => 'ynbusinesspages_business',
		),
	),

    array(
        'title' => 'YNC - Business Newest Blogs',
        'description' => 'Displays Newest Blogs of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-blogs',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Blogs',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Photos',
        'description' => 'Displays Newest Photos of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Photos',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Videos',
        'description' => 'Displays Newest Videos of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Videos',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Discussions',
        'description' => 'Displays Newest Discussions of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-discussions',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Discussions',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Files',
        'description' => 'Displays Newest Files of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-files',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Files',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Events',
        'description' => 'Displays Newest Events of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Events',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Wikis',
        'description' => 'Displays Newest Wikis of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-wikis',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Wikis',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Classifieds',
        'description' => 'Displays Newest Classifieds of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-classifieds',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Classifieds',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Contests',
        'description' => 'Displays Newest Contests of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-contests',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Contests',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Polls',
        'description' => 'Displays Newest Polls of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-polls',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Polls',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Listings',
        'description' => 'Displays Newest Listings of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Listings',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Jobs',
        'description' => 'Displays Newest Jobs of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-jobs',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Jobs',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Deals',
        'description' => 'Displays Newest Deals of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-groupbuys',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Deals',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Mp3Music Albums',
        'description' => 'Displays Newest Mp3Music Albums of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-mp3musics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Albums',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Musics Playlists',
        'description' => 'Displays Newest Musics Playlists of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-musics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Playlists',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
	array(
        'title' => 'YNC - Business Newest Social Music Albums',
        'description' => 'Displays Newest Social Music Albums of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-ynmusic-album',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Music Album',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
	array(
        'title' => 'YNC - Business Newest Social Music Songs',
        'description' => 'Displays Newest Social Music Songs of Business on Business Detail page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-newest-ynmusic-song',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Newest Music Song',
            'itemCountPerPage' => 1,
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Featured Businesses',
        'description' => 'Displays featured Businesses',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.featured-businesses',
        'defaultParams' => array(
            'title' => 'Featured Businesses',
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
					'Select',
					'max_business',
					array(
						'label' => 'Count(number of items to show)?',
						'multiOptions' => array(
							'4' => '4',
							'8' => '8',
							'12' => '12',
							'16' => '16',
							'20' => '20',
						),
						'value' => 'list',
					)
				),
			)
		)
    ),
    
    array(
        'title' => 'YNC - Business Browse Categories',
        'description' => 'Displays categories level 1 and child.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.browse-categories',
        'defaultParams' => array(
          'title' => 'Browse Categories',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Newest Businesses',
        'description' => 'Displays newest businesses.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.newest-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
          'title' => 'Newest Businesses',
           'itemCountPerPage' => 12
        ),
		'adminForm' => array(
			'elements' => array(
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
						'label' => 'Grid view',
						'multiOptions' => array(
							1 => 'Yes',
							0 => 'No',
						),
						'value' => 1,
					)
				),
				array(
					'Radio',
					'mode_list',
					array(
						'label' => 'List view',
						'multiOptions' => array(
							1 => 'Yes',
							0 => 'No',
						),
						'value' => 1,
					)
				),
				array(
					'Radio',
					'mode_pin',
					array(
						'label' => 'Pin view',
						'multiOptions' => array(
							1 => 'Yes',
							0 => 'No',
						),
						'value' => 1,
					)
				),
				array(
					'Radio',
					'mode_map',
					array(
						'label' => 'Map view',
						'multiOptions' => array(
							1 => 'Yes',
							0 => 'No',
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
							'grid' => 'Grid view',
							'list' => 'List view',
							'pin' => 'Pin view',
							'map' => 'Map view',
						),
						'value' => 'simple',
					)
				),
			)
		),
    ),
    
    array(
        'title' => 'YNC - Business Search',
        'description' => 'Displays form for searching businesses',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-search',
        'defaultParams' => array(
            'title' => 'Business Search',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Manage Menu',
        'description' => 'Displays manage menu in my business page',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-manage-menu',
        'defaultParams' => array(
        ),
    ),
    
    array(
        'title' => 'YNC - Business List Categories',
        'description' => 'Displays a list of categories.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.list-categories',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Most Liked Businesses',
        'description' => 'Displays a list of most liked businesses.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.most-liked-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Liked Businesses',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Most Viewed Businesses',
        'description' => 'Displays a list of most viewed businesses.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.most-viewed-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Viewed Businesses',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Most Rated Businesses',
        'description' => 'Displays a list of most rated businesses.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.most-rated-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Rated Businesses',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Recent Reviews',
        'description' => 'Displays a list of recent reviews',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.recent-reviews',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Reviews',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Businesses You May Like',
        'description' => 'Displays a list of busineses you may like.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.businesses-you-may-like',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Businesses You May Like',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Profile Related Businesses',
        'description' => 'Displays a list related Businesses on Business Detail Page.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.business-profile-related-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Related Businesses',
        ),
        'requirements' => array(
            'subject' => 'ynbusinesspages_business',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Most Discussed Businesses',
        'description' => 'Displays a list of most discussed businesses.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.most-discussed-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Discussed Businesses',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Most Checked-in Businesses',
        'description' => 'Displays a list of most checked-in businesses.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.most-checkedin-businesses',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Checked-in Businesses',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Businesses Tags',
        'description' => 'Displays businesses tags.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.businesses-tags',
          'defaultParams' => array(
            'title' => 'Tags',
        ),
    ),
    
	array(
        'title' => 'YNC - Business Businesses Listing',
        'description' => 'Displays Businesses Listing',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.businesses-listing',
        'isPaginated' => true,
		'defaultParams' => array(
      		'title' => '',
      		'itemCountPerPage' => 12
    	),
        'requirements' => array(
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
							'map' => 'Map view.',
							'pin' => 'Pin view.',
						),
						'value' => 'list',
					)
				),
			)
		)
	),
    
    array(
        'title' => 'YNC - Business Compare Businesses Bar',
        'description' => 'Displays Compare Businesses Bar.',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.compare-bar',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'YNC - Business Dashboard Menu',
        'description' => 'YNC - Business Dashboard Menu',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.dashboard-menu',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    
    array(
        'title' => 'YNC - Business Item Business Info',
        'description' => 'YNC - Business Item Business Info',
        'category' => 'YNC - Business',
        'type' => 'widget',
        'name' => 'ynbusinesspages.item-business',
        'defaultParams' => array(
            'title' => 'Business Info',
        ),
    ),
);