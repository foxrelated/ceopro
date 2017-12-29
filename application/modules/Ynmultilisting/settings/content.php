<?php
return array(
	array(
	    'title' => 'Multiple Listings - Listing Type Menu',
	    'description' => 'Displays a listing type menu',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.listing-type-menu',
	),
	
	array(
        'title' => 'Multiple Listings - Main Menu',
        'description' => 'Displays main menu.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.main-menu'
	),
	
	array(
        'title' => 'Multiple Listings - Profile Cover Style 1',
        'description' => 'Displays Listing Cover on Listing Detail page',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.listing-profile-cover-style1',
        'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),
    
	array(
        'title' => 'Multiple Listings - Profile Cover Other Styles',
        'description' => 'Displays Listing Cover on Listing Detail page',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.listing-profile-cover-styles',
        'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing Info',
	    'description' => 'Displays a listing\'s info on its profile.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.listing-profile-info',
	    'defaultParams' => array(
	      'title' => 'Info',
	    ),
	    'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing About Us',
	    'description' => 'Displays a listing\'s about us on its profile.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.listing-profile-about',
	    'defaultParams' => array(
	      'title' => 'About Us',
	    ),
	    'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing Location',
	    'description' => 'Displays a listing\'s location on its profile.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.listing-profile-location',
	    'defaultParams' => array(
	      'title' => 'Location',
	    ),
	    'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),
	
	 array(
	    'title' => 'Profile Listing Albums',
	    'description' => 'Displays a listing\'s albums on its profile.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.listing-profile-albums',
	    'isPaginated' => true,
	    'defaultParams' => array(
	      'title' => 'Albums',
	      'titleCount' => true,
	    ),
	    'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),
	
	array(
        'title' => 'Profile Related Listings',
        'description' => 'Displays a list of other listings that has the same category to the current listing.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.listing-profile-related-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Related Listings'
        ),
        'requirements' => array(
            'subject' => 'ynmultilisting_listing',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 6,
                    ),
                ),
            ),
        ),
    ),
	
	array(
	    'title' => 'Profile Listing Discussions',
	    'description' => 'Displays a listing\'s discussions on its profile.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.listing-profile-discussions',
	    'isPaginated' => true,
	    'defaultParams' => array(
	      'title' => 'Discussions',
	      'titleCount'=>true,
	    ),
	    'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing Videos',
	    'description' => 'Displays a list of videos on the listing.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.listing-profile-videos',
	    'isPaginated' => true,
	    'defaultParams' => array(
	      'title' => 'Videos',
	      'titleCount' => true,
	    ),
	    'requirements' => array(
	      'subject' => 'ynmultilisting_listing',
	    ),
    ),

	array(
		'title' => 'Profile Listing Ultimate Videos',
		'description' => 'Displays a list of ultimate videos on the listing.',
		'category' => 'Multiple Listings',
		'type' => 'widget',
		'name' => 'ynmultilisting.listing-profile-ultimate-videos',
		'isPaginated' => true,
		'defaultParams' => array(
			'title' => 'Ultimate Videos',
			'titleCount' => true,
		),
		'requirements' => array(
			'subject' => 'ynmultilisting_listing',
		),
	),
	
	array(
        'title' => 'Profile Listing Reviews',
        'description' => 'Displays a list of reviews on the listing.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.listing-profile-reviews',
        'isPaginated' => true,
        'defaultParams' => array(
          'title' => 'Reviews',
        ),
        'requirements' => array(
          'subject' => 'ynmultilisting_listing',
        ),
    ),
	
	array(
        'title' => 'Featured Listings',
        'description' => 'Displays featured listings on listings home page.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.featured-listing',
        'defaultParams' => array(
          'title' => 'Featured Listings',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 6,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Quick Links Slideshow',
        'description' => 'Displays quick links as slideshow.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.quick-link-slide',
        'defaultParams' => array(
          'title' => 'Quick Links Slideshow',
          'listingtype' => '0',
          'show_name' => 1,
          'show_onwer' => 1,
          'show_category' => 1,
          'show_price' => 1,
          'change_speed' => 5,
          'quicklink_ids' => '',
          'quicklinks' => array()
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => 'Ynmultilisting_Form_Admin_Widget_QuickLinkSlide'
    ),
    array(
        'title' => 'Quick Links Link Only',
        'description' => 'Displays quick links as link.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.quick-link-link',
        'defaultParams' => array(
          'title' => 'Quick Links Link Only',
          'quicklink_ids' => '',
          'quicklinks' => array()
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => 'Ynmultilisting_Form_Admin_Widget_QuickLinkLink'
    ),
    array(
	    'title' => 'Middle Categories Widget',
	    'description' => 'Displays Listing Categories in Middle Column.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.middle-categories',
	    'defaultParams' => array(
			'title' => 'Shop by Category',
			'itemCountPerPage' => 10
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
                    'Integer',
                    'itemCountPerPage',
                    array(
                        'label' => 'Number of categories will show?',
                        'value' => 10,
                    ),
                ),
            ),
        ),
	    
	),
	array(
        'title' => 'List Categories',
        'description' => 'Displays a list of categories.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.list-categories',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),
	array(
        'title' => 'Browse Listings',
        'description' => 'Displays listings in browse page.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.browse-listing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Browse Listings',
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
        'title' => 'Highlight Listing',
        'description' => 'Displays highlight listing.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.highlight-listing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Highlight Listings'
        ),
    ),
    array(
        'title' => 'Most Liked Listings',
        'description' => 'Displays a list of most liked listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.most-liked-listing',
        'defaultParams' => array(
            'title' => 'Most Liked Listings',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Commented Listings',
        'description' => 'Displays a list of most commented listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.most-commented-listing',
        'defaultParams' => array(
            'title' => 'Most Commented Listings',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Discussed Listings',
        'description' => 'Displays a list of most discussed listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.most-discussed-listing',
        'defaultParams' => array(
            'title' => 'Most Discussed Listings',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Rated Listings',
        'description' => 'Displays a list of most rated listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'isPaginated' => true,
        'name' => 'ynmultilisting.most-rated-listing',
        'defaultParams' => array(
            'title' => 'Most Rated Listings',
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
        'title' => 'Most Viewed Listings',
        'description' => 'Displays a list of most viewed listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.most-viewed-listing',
        'defaultParams' => array(
            'title' => 'Most Viewed Listings',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Recent Listings',
        'description' => 'Displays a list of recent listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.recent-listing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Listings',
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
        'title' => 'Most Reviewed Listings',
        'description' => 'Displays a list of most reviewed listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'isPaginated' => true,
        'name' => 'ynmultilisting.most-reviewed-listing',
        'defaultParams' => array(
            'title' => 'Most Reviewed Listings',
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
        'title' => 'Recent Reviews',
        'description' => 'Displays a list of recent reviews.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.recent-review',
        'defaultParams' => array(
            'title' => 'Recent Reviews',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Top Listings',
        'description' => 'Displays a list of top listings.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.top-listing',
        'defaultParams' => array(
            'title' => 'Top Listings',
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
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Search Listing',
        'description' => 'Displays search listing form.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.search-listing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Search Listings'
        ),
    ),
    array(
        'title' => 'Search Review',
        'description' => 'Displays search review form.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.search-review',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Search Review'
        ),
    ),
    array(
        'title' => 'Subscribe Listing',
        'description' => 'Displays subscribe listing form.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.subscribe-listing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Subscribe Listings'
        ),
    ),
    
    array(
        'title' => 'Tags',
        'description' => 'Displays listings tags on listing home page.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.tags',
          'defaultParams' => array(
            'title' => 'Tags',
        ),
    ),
    
	array(
        'title' => 'Compare Listings Bar',
        'description' => 'Displays Compare Listings Bar.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.compare-bar',
        'defaultParams' => array(
            'title' => 'Compare Listings Bar',
        ),
    ),
    
	array(
        'title' => 'Wish List Search',
        'description' => 'Displays a search form for search wish list.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.wishlist-search',
        'defaultParams' => array(
            'title' => 'Search Wish List',
        ),
    ),
    
	array(
        'title' => 'Wish List Listings',
        'description' => 'Displays listings of all Wish Lists.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.wishlist-listing',
        'defaultParams' => array(
            'title' => 'Wish List Listings',
            'itemCountPerpage' => 10
        ),
    ),
    
	array(
	    'title' => 'Wish List Create Link',
	    'description' => 'Displays Wish List create link.',
	    'category' => 'Multiple Listings',
	    'type' => 'widget',
	    'name' => 'ynmultilisting.wishlist-create-link',
        'defaultParams' => array(
            'title' => 'Wish List Create Link',
        ),
    ),
    
	array(
        'title' => 'User Profile Listings',
        'description' => 'Displays a member\'s listings on their profile.',
        'category' => 'Multiple Listings',
        'type' => 'widget',
        'name' => 'ynmultilisting.profile-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Multiple Listings',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
  	),
);