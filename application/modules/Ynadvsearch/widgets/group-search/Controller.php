<?php
class Ynadvsearch_Widget_GroupSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $advgroup_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('advgroup');
    if($advgroup_enable) {
        return $this -> setNoRender();
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    // Form
    $this->view->form = $formFilter = new Ynadvsearch_Form_GroupSearch();
    $defaultValues = $formFilter->getValues();

    if( !$viewer || !$viewer->getIdentity() ) {
      $formFilter->removeElement('view_group');
    }

    // Populate options
    $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
    $formFilter->category_id->addMultiOptions($categories);

    // Populate form data
    if( $formFilter->isValid($p) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();

    if( $viewer->getIdentity() && @$values['view_group'] == 1 ) {
      $values['users'] = array();
      foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if( $user_id ) {
      $values['user_id'] = $user_id;
    }
  }
}
