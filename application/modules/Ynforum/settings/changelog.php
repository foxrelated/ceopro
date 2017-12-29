<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
return array(    
    '4.01p1' => array(
        '/application/languages/en/ynforum.csv' => 'Fixed translation missing',
        'controllers/PostController.php' => 'Added the editing/deleting post permission for member levels',
        'controllers/TopicController.php' => 'Added the editing/deleting post permission for member levels',
        'externals/style/main.css' => 'Define h3 style for the forum',
        'Form/Admin/Settings/Level.php' => 'Added the editing/deleting post permission for member levels',
        'Model/Topic.php' => 'Stripping BBCode from description, and fix the warning when send activity relating to the topic',
        'Model/Post.php' => 'Stripping BBCode from description',
        'settings/changelog.php' => 'Incremented version',
        'settings/manifest.php' => 'Incremented version',
        'settings/my-upgrade-4.01-4.01p1.sql' => 'Added',
        'settings/my.sql' => 'Add the notification type ynforum_topic_reputation, incremented version',
        'views/scripts/_post.tpl' => 'Added the editing/deleting post permission for member levels',
        'views/scripts/topic/view.tpl' => 'Added the editing/deleting post permission for member levels',
        'widgets/profile-forum-posts/index.tpl' => 'Stripping BBCode from description'
    ),
    
    '4.01p2' => array(
        '/application/languages/en/ynforum.csv' => 'Add the mail templates for notification types',
        'settings/manifest.php' => 'Add the two actions newest-post and  watch',
        'controllers/TopicController.php' => 'Fix the quoting function and notify users watching the forum when a post is created or reply or a new topic is created',
        'controllers/ForumController.php' => 'Add the feature to allow users watching a forum, and see newest posts in the forum',
        'controllers/PostController.php' => 'Notify users watching the forum when its new topic is approved',
        'views/scripts/_post.tpl' => 'Make sure the existing photos on the SE forum module are still showed in this module',
        'views/scripts/_forum.tpl' => 'Add a link for a user to watch the newest posts in a forum',
        'views/scripts/forum/view.tpl' => 'Add a link for a user to watch a forum',
        'views/scripts/forum/newest-posts.tpl' => 'Added',
        'views/scripts/index/upload.tpl' => 'Added',
        'Form/Forum/Watch.php' => 'Add the the watch forum form',
        'externals/images/newest-posts.png' => 'Added',
        'settings/changelog.php' => 'Incremented version',
        'settings/manifest.php' => 'Incremented version',
        'settings/my-upgrade-4.01p1-4.01p2.sql' => 'Added',
        'settings/my.sql' => 'Add the mail templates for all the module\'s notifications, and creating the new table engine4_forum_forumwatches',
    ),
    
    '4.01p3' => array(
        'views/scripts/_FancyUpload.tpl' => 'Escape Javascript string to prevent the error when the translated message have special characters',
        'views/index/signature.tpl' => 'Add the signature feature on a post for a user',
        'controllers/IndexController.php' => 'Add action signature for the settings user\'s signature',
        'Forum/User/Signature.php' => 'The form for user to add the signature',
        'views/_post.tpl' => 'Add showing the user\'s signature',
      	'views/scripts/_post.tpl' => 'Fix the bug about layouts when showing a post of a deleted member',
      	'settings/changelog.php' => 'Incremented version',
        'settings/manifest.php' => 'Incremented version',
        'settings/my-upgrade-4.01p2-4.01p3.sql' => 'Added',
        'settings/my.sql' => 'Alter table for the feature signature',
    ),

    '4.01p4' => array(
        'Model/Cateogry.php' => 'Fix the bug when a category is deleted, all its sub categories need to be deleted',
		'controllers/AdminManageController.php' => 'Fix the bug when creating a new category and move an existing forum to this existed category'        
    )    
)
?>