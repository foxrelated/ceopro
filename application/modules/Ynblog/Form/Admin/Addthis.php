<?php
class Ynblog_Form_Admin_Addthis extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('AddThis Settings')
      ->setDescription('These settings affect all members in your community.');
    $this->addElement('Text', 'ynblog_username', array(
      'label' => 'Username',
      'description' => 'Insert your Addthis account username',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.username'),
    ));
 $this->addElement('Password', 'ynblog_password', array(
      'label' => 'Password',
      'description' => 'Insert your Addthis account password',
      'value' => Engine_Api::_()->getApi('settings', 'core')
                                ->getSetting('ynblog.password'),
    ));
    $this->addElement('Text', 'ynblog_pubid', array(
      'label' => 'Profile ID',
      'description' => '',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.pubid'),
    ));
    $this->addElement('Text', 'ynblog_domain', array(
      'label' => 'Domain',
      'description' => 'Insert your domain',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.domain'),
    ));
      $this->addElement('Select', 'ynblog_period', array(
      'label' => 'Period',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.period','day'),
      'multiOptions' => array(
        'day' => 'Day',
        'week' => 'Week',
        'month' => 'Month',
      )
    ));
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}