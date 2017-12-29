<?php

return array(

  // Profile Blogs Widget
  array(
    'title' => 'YN - Advanced Blog - Profile Blogs',
    'description' => 'Displays a member\'s blog entries on their profile.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.profile-blogs',
    'defaultParams' => array(
        'title' => 'Blogs',
        'titleCount' => true,
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),

  //Blogs Menu Widget
  array(
    'title' => 'YN - Advanced Blog - Menu',
    'description' => 'Displays menu blogs on Blog Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.blogs-menu',
  ),
  
  // Top Blogs (Most Liked Blogs) Widget
  array(
    'title' => 'YN - Advanced Blog - Top Blogs',
    'description' => 'Displays most liked blogs on Blog Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.top-blogs',
    'defaultParams' => array(
      'title' => 'Top Blogs',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array( 'label' => 'Title')),
          array('Text', 'max', array('label' => 'Number of Blogs show on page.',
                                     'value' => 6)),
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),

  // New Blogs Widget
   array(
    'title' => 'YN - Advanced Blog - New Blogs',
    'description' => 'Displays new blogs on Blog Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.new-blogs',
    'defaultParams' => array(
      'title' => 'New Blogs',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array('label' => 'Title')),
          array('Text', 'max', array( 'label' => 'Number of Blogs show on page.',
                                      'value' => 6)),
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),

   //Most Viewed Blogs Widget
   array(
    'title' => 'YN- Advanced Blog - Most Viewed Blogs',
    'description' => 'Displays most viewed blogs on Blog Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.most-viewed-blogs',
    'defaultParams' => array(
      'title' => 'Most Viewed Blogs',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array( 'label' => 'Title')),
          array('Text', 'max',array( 'label' => 'Number of Blogs show on page.',
                                     'value' => 6)),
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),

   //Most Commented Blogs Widget
   array(
    'title' => 'YN - Advanced Blog - Most Commented Blogs',
    'description' => 'Displays most commented blogs on Blog Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.most-commented-blogs',
    'defaultParams' => array(
      'title' => 'Most Commented Blogs',
    ),
    'adminForm' => array(
        'elements' => array(
            array('Text', 'title', array('label' => 'Title')),
            array('Text', 'max', array('label' => 'Number of Blogs show on page.',
                                       'value' => 6)),
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
  
  // Related Blogs Widget
   array(
    'title' => 'YN - Advanced Blog - Related Blogs',
    'description' => 'Displays related blogs on Blog View Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.detail-related-blogs',
    'defaultParams' => array(
      'title' => 'Related Blogs',
    ),
    'requirements' => array(
						'subject' => 'blog',
				),
    'adminForm' => array(
        'elements' => array(
            array('Text', 'title', array('label' => 'Title')),
            array('Text', 'max', array('label' => 'Number of Blogs show on page.',
                                       'value' => 4)),
        )
     ),
  ),
  
  // Other Blogs Widget
   array(
    'title' => 'YN - Advanced Blog - Other Blogs',
    'description' => 'Displays other blogs on Blog View Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.detail-other-blogs',
    'defaultParams' => array(
      'title' => 'Other Blogs',
    ),
    'requirements' => array(
						'subject' => 'blog',
				),
    'adminForm' => array(
        'elements' => array(
            array('Text', 'title', array('label' => 'Title')),
            array('Text', 'max', array('label' => 'Number of Blogs show on page.',
                                       'value' => 4)),
        )
     ),
  ),

  //Featured Blogs Widget
  array(
    'title' => 'YN - Advanced Blog - Featured Blogs',
    'description' => 'Displays featured blogs on Blog Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.featured-blogs',
      'defaultParams' => array(
      'title' => 'Featured Blogs',
    ),
  ),

  //Blog Categories Widget
  array(
    'title' => 'YN - Advanced Blog - Blog Categories',
    'description' => 'Displays blog categories on browse blogs page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.blog-categories',
  ),

  //Blog Search Widget
  array(
    'title' => 'YN - Advanced Blog - Blogs Search',
    'description' => 'Displays blog search box on browse blogs page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.blogs-search',
  ),
  
  //Blog Listing Widget
  array(
    'title' => 'YN - Advanced Blog - Blogs Listing',
    'description' => 'Displays list of blogs on Listing Blogs Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.blogs-listing',
        'adminForm' => array(
            'elements' => array(
                array('Text', 'max', array('label' => 'Number of Blogs show on page.',
                    'value' => 6)),
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),

  //Blog Statistics
   array(
    'title' => 'YN - Advanced Blog - Blog Statistics',
    'description' => 'Displays blog statistics on Blogs Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.blogs-statistic',
  ),
  
  //Top Bloggers Widget
  array(
    'title' => 'YN - Advanced Blog - Top Bloggers',
    'description' => 'Displays top bloggers on Blogs Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.top-bloggers',
    'defaultParams' => array(
      'title' => 'Top Bloggers',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array('label' => 'Title')),
          array('Text', 'max', array( 'label' => 'Number of Bloggers show on page.',
                                      'value' => 6)),
        )
     ),
   ),

  //View By Date Blogs Widget
  array(
    'title' => 'YN - Advanced Blog - View By Date',
    'description' => 'Displays view by date on Blogs Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.view-by-date-blogs',
  ),

  //Blog Tags Widget
  array(
    'title' => 'YN - Advanced Blog - Tags',
    'description' => 'Displays tags on Blogs Browse Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.blogs-tags',
      'adminForm' => array(
          'elements' => array(
              array('Text', 'max', array('label' => 'Number of items',
                  'value' => 20)),
          )
      ),
  ),

  // Blog Owner Photo Widget
   array(
    'title' => 'YN - Advanced Blog - Owner Photo',
    'description' => 'Displays blog owner photo on User Blog List Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.owner-photo',
  ),

    // Blog Gluter Menu Widget
   array(
    'title' => 'YN - Advanced Blog - Side Menu',
    'description' => 'Displays blog side menu on User Blog List Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.blogs-side-menu',
   ),

     // Blog User Archieves Widget
   array(
    'title' => 'YN - Advanced Blog - User Archive',
    'description' => 'Displays user\'s blog archives Blog List Page.',
    'category' => 'Advanced Blogs',
    'type' => 'widget',
    'name' => 'ynblog.user-blog-archives',
   ),
)?>