<?php
class Advgroup_Form_Poll_Create extends Engine_Form
{
  
public function init()
{
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Create Poll')
      ->setDescription('Create your poll below, then click "Create Poll" to start your poll.');

    $this->addElement('text', 'title', array(
      'label' => 'Poll Title',
      'required' => true,
      'maxlength' => 63,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
    ));

    $this->addElement('textarea', 'description', array(
      'label' => 'Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '400'))
      ),
    ));

    $this->addElement('textarea', 'options', array(
      'label' => 'Possible Answers',
      'style' => 'display:none;',
    ));

    // Privacy
    $availableLabels = array(
        'everyone'      => 'Everyone',
        'registered'    => 'All Registered Members',
        'parent_member' => 'Group Members',
        'owner'         => 'Just Me',
    );
  
    // Search
    $this->addElement('Checkbox', 'search', array(
      'label' => "Show this poll in search results",
      'value' => 1,
    ));

    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Poll',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      //'href' => 'javascr',
      //'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
?>
