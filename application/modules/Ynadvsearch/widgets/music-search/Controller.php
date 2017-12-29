<?php
class Ynadvsearch_Widget_MusicSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    // Get browse params
    $this->view->form = $formFilter = new Ynadvsearch_Form_MusicSearch();
    if( $formFilter->isValid($p) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    // Show
    $viewer = Engine_Api::_()->user()->getViewer();
    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);
  }
}
