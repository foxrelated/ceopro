<?php

class Ultimatenews_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("ULTIMATENEWS_FORM_ADMIN_LEVEL_DESCRIPTION");
	if( !$this->isPublic() ) 
	{
		
		// Element: comment
      	$this->addElement('Radio', 'comment', array(
	        'label' => 'Allow Commenting on News Items?',
	        'description' => 'Do you want to let members of this level comment on news items?',
	        'multiOptions' => array(
	          1 => 'Yes, allow members to comment on news items.',
	          0 => 'No, do not allow members to comment on news items.',
	        ),
	        'value' => 1,
	      ));
		
		// Element: manage feed
      	$this->addElement('Radio', 'manage_feed', array(
	        'label' => 'Allow Manage RSS Feeds?',
	        'description' => 'Do you want to let members of this level manage RSS feeds?',
	        'multiOptions' => array(
	          1 => 'Yes, allow members to manage RSS feeds.',
	          0 => 'No, do not allow members to manage RSS feeds.',
	        ),
	        'value' => 0,
	      ));
		
		// Element: manage news
      	$this->addElement('Radio', 'manage_news', array(
	        'label' => 'Allow Manage News Items?',
	        'description' => 'Do you want to let members of this level manage news items?',
	        'multiOptions' => array(
	          1 => 'Yes, allow members to manage news items.',
	          0 => 'No, do not allow members to manage news items.',
	        ),
	        'value' => 0,
	      ));
		  
		// Element: create feed
      	$this->addElement('Radio', 'create_feed', array(
	        'label' => 'Allow Create RSS Feed?',
	        'description' => 'Do you want to let members of this level create RSS feed?',
	        'multiOptions' => array(
	          1 => 'Yes, allow members to create RSS feed.',
	          0 => 'No, do not allow members to create RSS feed.',
	        ),
	        'value' => 0,
	      ));
		
		// Element: create feed
      	$this->addElement('Radio', 'create_news', array(
	        'label' => 'Allow Create News?',
	        'description' => 'Do you want to let members of this level create news?',
	        'multiOptions' => array(
	          1 => 'Yes, allow members to create news.',
	          0 => 'No, do not allow members to create news.',
	        ),
	        'value' => 0,
	      ));
		
		// Element: subscribe
    	$this->addElement('Radio', 'subscribe', array(
	      'label' => 'Allow subscribe RSS Feed?',
	      'description' => 'Do you want to let members subscribe RSS feed? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(        
	        1 => 'Yes, allow subscribe RSS feed.',
	        0 => 'No, do not allow subscribe RSS feed.',
	      ),
	      'value' => 1,
	    ));
		
		// Element: approve_rss
    	$this->addElement('Radio', 'approve_rss', array(
	      'label' => 'Auto Approved RSS feed?',
	      'description' => 'Do you want to auto approved RSS feed posted by these members.',
	      'multiOptions' => array(        
	        1 => 'Yes, allow auto approved RSS feed.',
	        0 => 'No, do not allow auto approved RSS feed.',
	      ),
	      'value' => 1,
	    ));
		
		// Element: approve_news
    	$this->addElement('Radio', 'approve_news', array(
	      'label' => 'Auto Approved News?',
	      'description' => 'Do you want to auto approved news posted by these members.',
	      'multiOptions' => array(        
	        1 => 'Yes, allow auto approved news.',
	        0 => 'No, do not allow auto approved news.',
	      ),
	      'value' => 1,
	    ));
	}
  }
}