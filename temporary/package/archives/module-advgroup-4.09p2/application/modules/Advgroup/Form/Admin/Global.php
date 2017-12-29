<?php
class Advgroup_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

     // Number of group per page
    $this->addElement('Text', 'advgroup_page', array(
      'label' => 'Groups Per Page',
      'description' => 'How many groups will be shown per page? (Enter a number between 1 and 999)',
      'allowEmpty' => false,
      'validators' => array(
            array('Int',true),
            array('Between',true,array(1,999)),
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.page', 10),
    ));

      $this->addElement('Text', 'pollmaxoptions', array(
        'label' => 'Maximum Poll Options',
        'description' => 'How many possible poll answers do you want to permit in a group? (Enter a number between 1 and 100)',
        'allowEmpty' => false,
        'validators' => array(
            array('Int',true),
            array('Between',true,array(1,100)),
            ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.pollmaxoptions',15),
       ));

       $this->addElement('Radio','pollcanchangevote',array(
        'label' =>'Change Poll Vote?',
        'description'=>'Do you want to permit the group members to change their poll vote in their group?.',
        'multiOptions' =>array(
            1 => 'Yes, group members can change their vote.',
            0 => 'No, group members cannot change their vote.',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.pollcanchangevote', 1),
            ));
		
		
		$this->addElement('Text', 'advgrouptime', array(
        'label' =>'Period time since new group since the created date',
        'allowEmpty' => false,
	    'validators' => array(
	            array('Int',true),
	            array('Between',true,array(1,999)),
	    ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.advgrouptime', 1),
        ));
		
		$this->addElement('select', 'advgroupunittime', array(
        'allowEmpty' => false,
	    'multiOptions' =>array(
            1 => 'Month',
            2 => 'Week',
            3 => 'Day',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.advgroupunittime', 1),
        ));
		
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

   public function saveValues()
  {
    $values = $this->getValues();
    Engine_Api::_()->getApi('settings','core')->setSetting('advgroup.pollmaxoptions',$values['pollmaxoptions']);
    Engine_Api::_()->getApi('settings','core')->setSetting('advgroup.pollcanchangevote',$values['pollcanchangevote']);
    Engine_Api::_()->getApi('settings','core')->setSetting('advgroup.page',$values['advgroup_page']);
	Engine_Api::_()->getApi('settings','core')->setSetting('advgroup.advgrouptime',$values['advgrouptime']);
	Engine_Api::_()->getApi('settings','core')->setSetting('advgroup.advgroupunittime',$values['advgroupunittime']);

    $this ->addNotice('Your changes have been saved!');
  }
}