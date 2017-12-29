<?php

class Ynadvsearch_Widget_VideoResultController extends Engine_Content_Widget_Abstract {

  public function indexAction()
  {
  	$video_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('video');
	if(!$video_enable)
	{
		return $this -> setNoRender();
	}
	
     // Permissions
     $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('video', null, 'create');
    
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Make form
    // Note: this code is duplicated in the video.browse-search widget
    $this->view->form = $form = new Video_Form_Search();
    
    // Process form
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
	$values = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $this->view->formValues = $values;

    $values['status'] = 1;
    $values['search'] = 1;

    $this->view->category = @$values['category'];
    $this->view->text = @$values['text'];


    if( !empty($values['tag']) ) {
      $this->view->tag = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
    }
    
    // check to see if request is for specific user's listings
    $user_id = $values['user'];
    if( $user_id ) {
      $values['user_id'] = $user_id;
    }

    // Get videos
    $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 6);
    $paginator->setItemCountPerPage($items_count);
	$page = 1;
	if(!empty($values['page']))
	{
		$page = $values['page']; 
	}
    $paginator->setCurrentPageNumber($page);
	
	 //count result
    $this->view->total_content = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator(array('status' => 1, 'search' => 1))->getTotalItemCount();
    $table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
    $contentType = $table->getContentType('video');
    if ($contentType) $this->view->label_content = $contentType->title;
	
  }
}
