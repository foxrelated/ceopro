<?php
class Ynadvsearch_Widget_PollSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    // Get form
    $this->view->form = $form = new Ynadvsearch_Form_PollSearch();

    // Process form
    $values = array('browse' => 1);
    if( $form->isValid($p) ) {
      $values = $form->getValues();
    }
    $this->view->formValues = array_filter($values);

    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);
  }
}
