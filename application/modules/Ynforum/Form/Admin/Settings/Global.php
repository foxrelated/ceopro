<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 8150 2011-01-06 01:26:03Z char $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this->setTitle('Global Settings')->setDescription('These settings affect all members in your community.');

        // Create Topic Length Text element
        $topic_length = new Engine_Form_Element_Integer('topic_length');
        $topic_length->setDescription('How many posts will be showed per topic page? (Enter a number between 1 and 999)');
        $topic_length->setLabel('Posts per topic page');
        $topic_length->setValue(25);

        // Create Forum Length Text element
        $forum_length = new Engine_Form_Element_Integer('forum_length');
        $forum_length->setDescription('How many topics will be showed per forum page? (Enter a number between 1 and 999)');
        $forum_length->setLabel('Topics per forum page');
        $forum_length->setValue(25);

        $hot_topic_posts = new Engine_Form_Element_Integer('hot_topic_posts');
        $hot_topic_posts->setDescription('How many posts does a topic have to get to be considered as the hot one? (Enter a number between 1 and 999)');
        $hot_topic_posts->setLabel('Minimum number of posts of a hot topic');
        $hot_topic_posts->setValue(25);
        
        // Create BBCode Radio element
        $bbcode = new Engine_Form_Element_Radio('bbcode');
        $bbcode->addMultiOptions(array(
                    1 => 'Yes, members can use BBCode tags.',
                    0 => 'No, do not let members use BBCode.'
                ));
        $bbcode->setValue(1);
        $bbcode->setLabel("Enable BBCode");

        // Create HTML Radio element
        $html = new Engine_Form_Element_Radio('html');
        $html->addMultiOptions(array(
                    1 => 'Yes, members can use HTML in their posts.',
                    0 => 'No, strip HTML from posts.'
                ));
        $html->setValue(0);
        $html->setLabel("Enable HTML");
        
        // Create Approve Topics Radio element
        $approveTopics = new Engine_Form_Element_Radio('approve_topics');
        $approveTopics->addMultiOptions(array(
            1 => 'Yes, topics are approved automatically',
            0 => 'No, topics have to be approved before displaying to the community'
        ));
        $approveTopics->setValue(1);
        $approveTopics->setLabel('Approve topics');
		
		// Create the viewing permission can see forums in listing page
        $permissionForums = new Engine_Form_Element_Radio('permission_see_forum');
        $permissionForums->addMultiOptions(array(
            1 => 'Yes, the site will only show the forums in the listing page if the viewer has the viewing permission',
            0 => 'No, the site will show all forums in the listing page when the viewer does not has the viewing permission'
        ));
        $permissionForums->setValue(0);
        $permissionForums->setLabel('Permission show the forums');
        
         // Create detect Link Radio element
        $detectLink = new Engine_Form_Element_Radio('detect_link');
        $detectLink->addMultiOptions(array(
            1 => "Yes, detect the links automatically and replace by tag a in a user's post or a user's topic",
            0 => 'No, do not detect the links automatically'
        ));
        $detectLink->setValue(1);
        $detectLink->setLabel('Detect the links automatically');
        
		/*
        $maxNumberOfPhotos = new Engine_Form_Element_Integer('max_photo_per_post');
        $maxNumberOfPhotos->setDescription('How many photos will be showed per post? (Enter a number between 1 and 100)');
        $maxNumberOfPhotos->setLabel('Photos per topic');
		 */
        
		$maxFileSizeAttach = new Engine_Form_Element_Integer('maxFileSizeAttach');
        $maxFileSizeAttach->setDescription('Enter the maximum filesize for uploaded files in KB. This must be a number between 1 and 204800.');
        $maxFileSizeAttach->setLabel('Maximum file size attachment');
		$maxFileSizeAttach->setValidators(array(new Zend_Validate_Between(1,204800)));
		
		$fileTypeAttach = new Engine_Form_Element_Text('fileType');
        $fileTypeAttach->setDescription('If you want to allow file type, you can enter them below (separated by commas). Example: gif,png,jpg,rar,doc');
        $fileTypeAttach->setLabel('Allow file type attachment');
        
		
        $hottestTopicLength = new Engine_Form_Element_Integer('hottest_topic_length');
        $hottestTopicLength->setDescription('How many topics will be showed in the Hottest topics widget?');
        $hottestTopicLength->setLabel('Number of topics in the hottest topics widget');
        
        $mostViewedTopicLength = new Engine_Form_Element_Integer('most_viewed_topic_length');
        $mostViewedTopicLength->setDescription('How many topics will be showed in the Most Viewed Topics widget?');
        $mostViewedTopicLength->setLabel('Number of topics in the most viewed topics widget');
        
        $newestTopicLength = new Engine_Form_Element_Integer('newest_topic_length');
        $newestTopicLength->setDescription('How many topics will be showed in the Newest Topics widget?');
        $newestTopicLength->setLabel('Number of topics in the newest topics widget');
        
        $topThankedUserLength = new Engine_Form_Element_Integer('top_thanked_user_length');
        $topThankedUserLength->setDescription('How many users will be showed in the Top Thanked Users widget?');
        $topThankedUserLength->setLabel('Number of users in the top thanked users widget');
        
        // Add elements
        $this->addElements(array(
            $topic_length,
            $forum_length,
            $hot_topic_posts,
            $bbcode,
            $html,
            $approveTopics,
            $permissionForums,
            $detectLink,
            //$maxNumberOfPhotos,
            $maxFileSizeAttach,
            $fileTypeAttach,
            $hottestTopicLength,
            $mostViewedTopicLength,
            $newestTopicLength,
            $topThankedUserLength,
        ));

        // Add submit button
        $submit = new Engine_Form_Element_Button('submit');
        $submit->setAttrib('type', 'submit')
                ->setLabel('Save Changes');
        $this->addElement($submit);
    }
}