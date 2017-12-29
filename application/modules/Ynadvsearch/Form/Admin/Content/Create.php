<?php
class Ynadvsearch_Form_Admin_Content_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create Content Type')
      ->setAttrib('class', 'global_form_popup')
      ;
    $arr_content_types = array(
        'album' => 'Album',
        'advalbum_photo' => 'Photo',
        'group' => 'Group',
        'blog' => 'Blog',
        'event' => 'Event',
        'forum_topic' => 'Forum',
        'groupbuy_deal' => 'Group Buy',
        'mp3music_playlist' => 'Mp3 Music',
        'music_playlist' => 'Music',
        'poll' => 'Polls',
        'social_store' => 'Store Store',
        'social_product' => 'Store Product',
        'user' => 'Member',
        'video' => 'Video',
        'ynauction_product' => 'Auction',
        'yncontest_contest' => 'Contest',
        'ynfilesharing_folder' => 'File Sharing',
        'ynfundraising_campaign' => 'Fundraising',
        'ynwiki_page' => 'Wiki',
        'classified' => 'Classified',
        'ynlistings_listing' => 'Listing',
        'ynjobposting_job' => 'Job Posting - Job',
        'ynjobposting_company' => 'Job Posting - Company',
        'ynbusinesspages_business' => 'Business',
        'ynresume_resume' => 'Resume',
        'ynmultilisting_listing' => 'Multiple Listing',
        'ynmusic_song' => 'Social Music',
        'ynultimatevideo_video' => 'Ultimate Video',
        'ynvideochannel_video' => 'Video Channel',
    );
    
    asort($arr_content_types);
    $arr_checked_content_types = array();
    foreach($arr_content_types as $key => $value)
    {
        $advalbum_enable = false;
        if(Engine_Api::_()->hasItemType('advalbum_album') && $key == 'album')
        {
            $advalbum_enable = true;
        }
        $table_content_types = Engine_Api::_() -> getItemTable('ynadvsearch_contenttype');
        $row = $table_content_types->getContentType($key);
        if((Engine_Api::_()->hasItemType($key) || $advalbum_enable) && !$row)
        {
            $arr_checked_content_types[$key] = $arr_content_types[$key];
        }
    }
    if(count($arr_checked_content_types))
    {
        $this->addElement('Select', 'type', array(
          'label' => 'Content Type',
          'multiOptions' => $arr_checked_content_types,
        ));
    }

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
    
    $this -> addElement('File', 'photo', array('label' => 'Icon', ));
    $this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
    
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Apply to search bar?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));
    
     $this->addElement('Checkbox', 'show', array(
      'label' => 'Show?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create',
      'type' => 'submit',
      'onclick' => 'removeSubmit()',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}

