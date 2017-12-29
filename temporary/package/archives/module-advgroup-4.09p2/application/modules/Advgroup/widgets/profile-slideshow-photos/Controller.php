<?php

class Advgroup_Widget_ProfileSlideshowPhotosController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('group');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    $this->view->allowLoop =  $allowLoop = ($this->_getParam('allowLoop') == '1') ? true : false;
    $this->view->effect = $effect = $this->_getParam('effect', 'fade');
    
     // Get paginator
    $photos = $subject->getGroupPhoto();
	
	$params = array('is_featured' => 1);
	//print_r($album -> toArray()); die;
	
    $this->view->photos = $photos;

   
    
   
    // Do not render if nothing to show and cannot upload
       if( $photos->count() <= 0) 
       {
         return $this->setNoRender();
       }
   

  }
}