<?php 
/**
* 
*/
class DisableHelperContent{
  public function setEnabled(){
    return null;
  }
}
class Advmenusystem_SignupController extends Core_Controller_Action_Standard
{
  
  public function indexAction(){
    $this->_helper->layout->setLayout('default-simple');
    // $this->_helper->content = new DisableHelperContent();
    // If the user is logged in, they can't sign up now can they?
    if (Engine_Api::_()->user()->getViewer()->getIdentity())
    {
        return $this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
        ));
    }

    $this-> view->socialconnect_enable = Engine_Api::_()->hasModuleBootstrap("social-connect");
    $formSequenceHelper = $this->_helper->formSequence;
    foreach (Engine_Api::_()->getDbtable('signup', 'user')->fetchAll() as $row)
    {
      if ($row->enable == 1)
      {
        $class = $row->class;
        $formSequenceHelper->setPlugin(new $class, $row->order);
      }
    }

    // This will handle everything until done, where it will return true
    if (!$this->_helper->formSequence())
    {
      return;
    }
    return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 3000,
        'parentRefresh' => 10,
        'format' => 'smoothbox',
        'messages' => array('Success')
      ));
  }
}