<?php

class Ynadvsearch_Widget_EventResultController extends Engine_Content_Widget_Abstract {

  public function indexAction()
  {
    $event_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('event');
    if(!$event_enable)
    {
        return $this -> setNoRender();
    }
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $filter = $request -> getParam('filter', 'future');
    if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
    $this->view->filter = $filter;

    // Create form
    $this->view->formFilter = $formFilter = new Event_Form_Filter_Browse();
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
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $values = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $this->view->formValues = $values;
    if( $viewer->getIdentity() && @$values['view'] == 1 ) {
      $values['users'] = array();
      foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

     // check to see if request is for specific user's listings
    if( ($user_id = $this->_getParam('user')) ) {
      $values['user_id'] = $user_id;
    }
    
    $page = 1;
    if(!empty($values['page']))
    {
        $page = $values['page'];
    }
    
     //count result
    $this->view->total_content =  Engine_Api::_()->getItemTable('event')->getEventPaginator(array('search' => 1))->getTotalItemCount();
    $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
    $contentType = $table->getContentType('event');
    if ($contentType) $this->view->label_content = $contentType->title;
    
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('event')
            ->getEventPaginator($values);
    $paginator->setCurrentPageNumber($page);
  }
}