<?php
class Ynadvsearch_Widget_EventSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    
    $ynevent_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynevent');
    if($ynevent_enable) {
        return $this -> setNoRender();
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    $filter = !empty($p['filter']) ? $p['filter'] : 'future';
    if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
    $this->view->filter = $filter;

    // Create form
      $this->view->form = $formFilter = new Ynadvsearch_Form_EventSearch();
      $defaultValues = $formFilter->getValues();
      
      if( !$viewer || !$viewer->getIdentity() ) {
        $formFilter->removeElement('view');
      }

      // Populate options
      foreach( Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row ) {    
        $formFilter->category_id->addMultiOption($row['category_id'], $row['title']);
      }
      if (count($formFilter->category_id->getMultiOptions()) <= 1) {
        $formFilter->removeElement('category_id');
      }


    // Populate form data
    if( $formFilter->isValid($p) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();

    if( $formFilter instanceof Event_Form_Filter_Browse ) {
      if( $viewer->getIdentity() && @$values['view'] == 1 ) {
        $values['users'] = array();
        foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
          $values['users'][] = $memberinfo->user_id;
        }
      }

      $values['search'] = 1;

      if( $filter == "past" ) {
        $values['past'] = 1;
      } else {
        $values['future'] = 1;
      }

       // check to see if request is for specific user's listings
      if( ($user_id = $this->_getParam('user')) ) {
        $values['user_id'] = $user_id;
      }
    }
  }
}
