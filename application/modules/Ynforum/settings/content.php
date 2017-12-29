<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: content.php 8371 2011-02-01 09:49:11Z john $
 * @author     John
 */
return array(
    array(
        'title' => 'Profile Advanced Forum Topics',
        'description' => 'Displays a member\'s advanced forum topics on their profile.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.profile-ynforum-topics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Advanced Forum Topics',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Profile Advanced Forum Posts',
        'description' => 'Displays a member\'s advanced forum posts on their profile.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.profile-ynforum-posts',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Advanced Forum Posts',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Recent Advanced Forum Topics',
        'description' => 'Displays recently created advanced forum topics.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.list-recent-topics',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Advanced Forum Topics',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Recent Advanced Forum Posts',
        'description' => 'Displays recent advanced forum posts.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.list-recent-posts',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Advanced Forum Posts',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array (
        'title' => 'Hottest Topics',
        'description' => 'Display hottest advanced forum topics.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.list-hottest-topics',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => 'Hottest Topics',
        ),
        'requirements' => array(
            'no-subject',
        )
    ),
    array(
        'title' => 'Most Viewed Topics',
        'description' => 'Display the most viewed topics.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.list-most-viewed-topics',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => 'Most Viewed Topics',
        ),
        'requirements' => array(
            'no-subject',
        )
    ),
    array(
        'title' => 'Newest Topics',
        'description' => 'Display the newest topics.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.list-newest-topics',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => 'Newest Topics',
        ),
        'requirements' => array(
            'no-subject',
        )
    ),
    array(
        'title' => 'Statistic Top Users',
        'description' => 'Display the statistic of top users.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.list-statistic-top-users',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => 'Statistic Top Users',
        ),
        'requirements' => array(
            'no-subject',
        )
    ),
    array(
        'title' => 'Statistics',
        'description' => 'Display the statistic of top forum.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.statistics',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        )
    ),
    //MinhNC START//
    array(
        'title' => 'Forum Announcements',
        'description' => 'Display the announcement on forum detail.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.profile-announcements',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'subject' => 'forum',
        )
    ),
   
    array(
        'title' => 'Forum Events',
        'description' => 'Display events on forum detail.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.profile-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => "Forum's Events",
        ),
        'requirements' => array(
            'subject' => 'forum',
        )
    ),
    array(
        'title' => 'Forum Groups',
        'description' => 'Display groups on forum detail.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.profile-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => "Forum's Groups",
        ),
        'requirements' => array(
            'subject' => 'forum',
        )
    ),
    array(
        'title' => 'Forum Detail Header',
        'description' => 'Display forum header on forum detail.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.profile-header',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => "",
        ),
        'requirements' => array(
            'subject' => 'forum',
        )
    ),
    //MinhNC END //
     //LUANND START //
    array(
        'title' => 'My Watch Topics',
        'description' => 'Display the my watch topics.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.list-mywatch-topics',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => 'My Watch Topics',
        ),
        'requirements' => array(
            'no-subject',
        )
    ),
    array(
        'title' => 'Forum Polls',
        'description' => 'Display the poll on forum detail.',
        'category' => 'YNC - Advanced Forum',
        'type' => 'widget',
        'name' => 'ynforum.profile-polls',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'subject' => 'forum',
        )
    ),
    //LUANND END //
)
?>