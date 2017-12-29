<?php
class Ynblog_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    // Form information
    $this->setTitle('Global Settings')
         ->setDescription('These settings affect all members in your community.');

    // Blog moderation setting
     $this->addElement('Radio', 'ynblog_moderation', array(
      'label' => 'Blog Moderation',
      'description' => "If set up \"Yes\" admin must approve blogs before user can view it. Otherwise, the blogs are automatically approved.",
      'multiOptions' => array(
        1 => 'Yes, allow blog moderation mode.',
        0 => 'No, not allow blog moderation mode.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.moderation', 0),
    ));

     // Blog moderation setting
     $this->addElement('Radio', 'ynblog_captcha', array(
      'label' => 'Blog Captcha',
      'description' => "If set up \"Yes\" captcha will be added to create form to prevent spamming.",
      'multiOptions' => array(
        1 => 'Yes, add Captcha when creating a blog.',
        0 => 'No, do not add Captcha when creating a blog.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.captcha', 0),
    ));
    
    
    $this->addElement('Radio', 'ynblog_cron', array(
      'label' => 'Import Blog',
      'multiOptions' => array(
        1 => 'Yes, allow automatically import blog.',
        0 => 'No, do not allow automatically import blog.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.cron', 1),
    ));
    
    // Submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}