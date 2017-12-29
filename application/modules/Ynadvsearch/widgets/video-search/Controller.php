<?php
class Ynadvsearch_Widget_VideoSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $ynvideo_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynvideo');
    if($ynvideo_enable) {
        return $this -> setNoRender();
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Make form
    $this->view->form = $form = new Ynadvsearch_Form_VideoSearch();
    
    // Process form
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    if( $form->isValid($p) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = $values;

    $values['status'] = 1;
    $values['search'] = 1;

    $this->view->category = $values['category'];


    if( !empty($values['tag']) ) {
      $this->view->tag = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
    }
    
    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if( $user_id ) {
      $values['user_id'] = $user_id;
    }
  }
}
