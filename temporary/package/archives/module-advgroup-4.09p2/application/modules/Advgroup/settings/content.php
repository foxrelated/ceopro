<?php
return array(
    array(
        'title' => 'Most Active Groups',
        'description' => 'Displays a list groups that have the most number of topics.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.list-most-active-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Active Groups',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'time',
                    array(
                        'label' => 'Posted date',
                        'multiOptions' => array(
                            '1' => '30 days ago',
                            '2' => '60 days ago',
                            '3' => '90 days ago',
                        ),
                        'value' => '1',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Group Directory',
        'description' => 'Displays all existing groups',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-directory',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Group Directory',
        ),
    ),
    array(
        'title' => 'Group Quick Navigation',
        'description' => 'Display quick navagation',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-quick-navigation',
        'isPaginated' => true,
        'defaultParams' => array(//'title' => 'Group Directory',
        ),
    ),

    array(
        'title' => 'Group Profile Cover',
        'description' => 'Displays a advanced group\'s cover and information on it\'s profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-cover',
        'requirements' => array(
            'subject' => 'advgroup',
        ),
    ),

    array(
        'title' => 'Advanced Group Profile Sponsors',
        'description' => 'Displays a advanced group\'s sponsors on it\'s profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-sponsors',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => 'Sponsors',
        ),
        'requirements' => array(
            'subject' => 'advgroup',
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
                        'label' => 'Number of sponsors to display',
                        'value' => '8',
                        'required' => true,
                        'validators' => array(
                            array('Between', true, array(1, 100)),
                        ),
                    ),
                ),
            )),
    ),

    array(
        'title' => 'Advanced Group Hightlight Video',
        'description' => 'Displays highlight video on group profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-video',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => 'Highlight video',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'advgroup',
        ),
    ),

    array(
        'title' => 'Advanced Group May-like Group',
        'description' => 'Displays related groups on group profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.list-maylike-groups',
        'defaultParams' => array(
            'title' => 'Group you may like',
        ),
        'requirements' => array(
            'subject' => 'advgroup',
        ),
    ),

    array(
        'title' => 'Groups Menu',
        'description' => 'Displays groups menu on the group Homepage.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-menu',
    ),
    array(
        'title' => 'Groups Search',
        'description' => 'Displays group search form on selected page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-search',
    ),
    array(
        'title' => 'Groups Listing',
        'description' => 'Displays groups listing on groups listing page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-listing',
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
                        ),
                        'value' => 'list',
                    )
                ),

            )
        ),
    ),
    array(
        'title' => 'Featured Groups',
        'description' => 'Displays featured groups on groups home page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.featured-groups',
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Activity Feed',
        'description' => 'Displays activity feed on groups profile page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.feed',
        'defaultParams' => array(
            'title' => "What's New",
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Top Posters',
        'description' => 'Displays top posters of a group on groups profile page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-top-posters',
        'defaultParams' => array(
            'title' => 'Top Posters',
        ),
    ),
    array(
        'title' => 'Popular Groups',
        'description' => 'Displays a list of groups that have the most number of members or number of views( According to the option set).',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.list-popular-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Groups',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'popularType',
                    array(
                        'label' => 'Popular Type',
                        'multiOptions' => array(
                            'member' => 'Number of Members',
                            'view' => 'Number of Views',
                        ),
                        'value' => 'member',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'New Groups',
        'description' => 'Displays a list of recently created groups.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.list-recent-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'New Groups',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'recentType',
                    array(
                        'label' => 'Recent Type',
                        'multiOptions' => array(
                            'creation' => 'Creation Date',
                            'modified' => 'Modified Date',
                        ),
                        'value' => 'creation',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Active Groups',
        'description' => 'Displays a list groups that have the most number of topics.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.list-active-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Active Groups',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(),
    ),
    array(
        'title' => 'Profile Groups',
        'description' => 'Displays a member\'s groups on their profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Groups',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Group Profile Discussions',
        'description' => 'Displays a group\'s discussions on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-discussions',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Info',
        'description' => 'Displays a group\'s info (creation date, member count, leader, officers, etc) on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-info',
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Members',
        'description' => 'Displays a group\'s members on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Members',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Options',
        'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a group on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-options',
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Photo',
        'description' => 'Displays a group\'s photo on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-photo',
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Photos',
        'description' => 'Displays a group\'s photos on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Status',
        'description' => 'Displays a group\'s title on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-status',
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Events',
        'description' => 'Displays a group\'s events on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Musics',
        'description' => 'Displays a group\'s musics on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-musics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Musics',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Mp3Musics',
        'description' => 'Displays a group\'s mp3musics on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-mp3musics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Mp3Musics',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),

    array(
        'title' => 'Group Profile Listings',
        'description' => 'Displays a group\'s listings on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Listings',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),

    array(
        'title' => 'Group Profile Social Music Albums',
        'description' => 'Displays a group\'s social music albums on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-social-music-albums',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Social Music Albums',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),

    array(
        'title' => 'Group Profile Social Music Songs',
        'description' => 'Displays a group\'s social music songs on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-social-music-songs',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Social Music Songs',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),

    array(
        'title' => 'Group Profile Listings',
        'description' => 'Displays a group\'s listings on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Listings',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),

    array(
        'title' => 'Group Profile FileSharing',
        'description' => 'Displays a group\'s filesharing on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-filesharing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'FileSharing',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Profile Useful Links',
        'description' => 'Display useful links for group member on group profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-useful-links',
        'defaultParams' => array(
            'title' => 'Useful Links',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Albums',
        'description' => 'Displays a group\'s albums on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-albums',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Albums',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Announcements',
        'description' => 'Displays recent announcements.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-group-announcements',
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Overall Statistic',
        'description' => 'Displays overall statistic of groups on group home page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.overall-statistic',
        'defaultParams' => array(
            'title' => 'Overall Statistic',
        ),
    ),
    array(
        'title' => 'Group Profile Polls',
        'description' => 'Displays a group\'s polls on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-polls',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Polls',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Statistic',
        'description' => 'Displays statistic of a group on that group profile page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-statistic',
        'defaultParams' => array(
            'title' => 'Statistic',
        ),
    ),
    array(
        'title' => 'Group Suggested Poll',
        'description' => 'Displays a random group\'s poll on its profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.suggested-poll',
        'requirements' => array(
            'title' => 'Suggested Poll',
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Categories',
        'description' => 'Displays group categories on group browse page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-category-search',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),
    array(
        'title' => 'Sub Groups',
        'description' => 'Displays sub groups list on group profile page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.sub-groups',
        'isPaginated' => true,
    ),
    array(
        'title' => 'Group Profile Videos',
        'description' => 'Displays a list of videos that are recently posted on the group.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.recent-group-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Profile Videos',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'marginLeft',
                    array(
                        'label' => 'Margin left of each video',
                        'value' => '26',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            ),
        ),
    ),

    array(
        'title' => 'Group Profile Ultimate Videos',
        'description' => 'Displays a list of ultimate videos of the group.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-ynultimatevideo-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Ultimate Videos',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Group Profile VideoChannel Videos',
        'description' => 'Displays a list of videochannel videos of the group.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-ynvideochannel-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'VideoChannel Videos',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),
    array(
        'title' => 'Advanced Group Profile Slideshow Photos',
        'description' => 'Displays a advanced group\'s photos on it\'s profile.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-slideshow-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Slideshow Photos'
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'allowLoop',
                    array(
                        'label' => 'Allow to loop',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 'no',
                    )
                ),
                array(
                    'Radio',
                    'effect',
                    array(
                        'label' => 'Effects (animation)',
                        'multiOptions' => array(
                            'fade' => 'Fade',
                            'slide' => 'Slide',
                        ),
                        'value' => 'fade',
                    )
                ),
            ),

        ),
    ),

    array(
        'title' => 'Group Profile Wikis',
        'description' => 'Displays a group\'s wiki pages on its profile',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.profile-wikis',
        'isPaginated' => true,
        'defaultParams' => array(
            'titleCount' => true,
            'title' => 'Wiki Pages'
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Tags',
        'description' => 'Displays groups tags on group home page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.groups-tags',
        'defaultParams' => array(
            'title' => 'Tags',
        ),
    ),
    array(
        'title' => 'Groups Activities',
        'description' => 'Listing the public groups activities.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.group-activity',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Groups Activities',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'widget_height',
                    array(
                        'label' => 'Maximum height of the widget block(px)',
                        'value' => '450',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(-1)),
                        )
                    )
                ),
            ),
        )
    ),
    array(
        'title' => 'Most Active Members',
        'description' => 'Listing the most active group members',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.group-top-members',
        'defaultParams' => array(
            'title' => 'Most Active Members',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'number',
                    array(
                        'label' => 'How many members will be shown',
                        'value' => '5',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            ),
        )
    ),

    array(
        'title' => 'Most Groups',
        'description' => 'Displays a most groups in group landing page.',
        'category' => 'YNC - Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.list-most-items',
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
                    'tab_enabled',
                    array(
                        'label' => 'Which tabs are enabled?'
                    )
                ),
                array(
                    'Radio',
                    'tab_recent',
                    array(
                        'label' => 'Newest Groups tab.',
                        'multiOptions' => array(
                            1 => 'Yes.',
                            0 => 'No.',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'tab_popular',
                    array(
                        'label' => 'Popular Groups tab.',
                        'multiOptions' => array(
                            1 => 'Yes.',
                            0 => 'No.',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'tab_active',
                    array(
                        'label' => 'Active Groups tab.',
                        'multiOptions' => array(
                            1 => 'Yes.',
                            0 => 'No.',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'tab_directory',
                    array(
                        'label' => 'Directories Groups tab.',
                        'multiOptions' => array(
                            1 => 'Yes.',
                            0 => 'No.',
                        ),
                        'value' => 1,
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
                        ),
                        'value' => 'list',
                    )
                ),

            )
        ),
    ),
)
?>
