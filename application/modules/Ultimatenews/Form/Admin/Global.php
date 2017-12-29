<?php

class Ultimatenews_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

	$this->addElement('Text', 'ultimate_days', array(
      'label' => 'Number of days for removing the old news by cron job',
      'description' => 'How many days that you would like to mark the news are so old and remove them? You should config this setting to make your server is not crash',
      'validators' => array(
        array('NotEmpty', true),
        array('Int', true),
        array('GreaterThan',true,array(0))
      ),
      'filters' => array(
      		new Engine_Filter_Censor(),
       ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.days', 7),
    ));
	
	$this->addElement('Text', 'ultimate_feeds', array(
      'label' => 'Number of feeds will be get by cron job',
      'description' => 'This option is for admin to divide the list of feeds when the system has a lot of feeds. '
      		. 'Leave 0 to get all feed content every cron excutes',
      'validators' => array(
        array('NotEmpty', true),
        array('Int', true),
      ),
      'filters' => array(
      		new Engine_Filter_Censor(),
       ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.feeds', 7),
    ));
	
	$this->addElement('Radio', 'ultimate_parser', array(
      'label' => 'Using Younet RSS service',
      'description' => 'Enabling this option will use our centralized server to '
          . 'parse news content on this site .',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.parser', 1),
      'multiOptions' => array(
        '1' => 'Yes, allow using Younet RSS parser',
        '0' => 'No, only use my own RSS parser ',
      ),
    ));
	
	$this->addElement('Text', 'ultimate_newsPerPage', array(
      'label' => 'Number of News per Page',
      'description' => 'How many news will be shown per page?',
      'validators' => array(
        array('NotEmpty', true),
        array('Int', true),
        array('GreaterThan',true,array(0))
      ),
      'filters' => array(
      		new Engine_Filter_Censor(),
       ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ultimate.newsPerPage', 10),
    ));
	
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}