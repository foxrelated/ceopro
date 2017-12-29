<?php
class Ynmultilisting_VideoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($listing_id = (int)$this -> _getParam('listing_id')) && null !== ($listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $listing_id)))
			{
				Engine_Api::_() -> core() -> setSubject($listing);
			}
		}
	}
	
	public function manageAction()
	{
		//Checking Video Plugin - Viewer required -View privacy
		if(!Engine_Api::_()->hasItemType('video'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		
		if (!$listing->isAllowed('view'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		//Get viewer, listing, search form
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $form = new Ynmultilisting_Form_Video_Search();

		// Check create video authorization
		$this -> view -> canCreate = $canCreate = $listing->isAllowed('video');

		//Prepare data filer
		$params = array();
		$params = $this -> _getAllParams();
		$params['listing_id'] = $listing -> getIdentity();
		$params['user_id'] = $viewer -> getIdentity();
		$form -> populate($params);
		$this -> view -> formValues = $formValues = $form -> getValues();
		$params['title'] = $formValues['title'];
		$params['owner'] = $formValues['owner'];
		//Get data
		$tableMappings = Engine_Api::_()->getDbTable('mappings', 'ynmultilisting');
		$this -> view -> paginator = $paginator = $tableMappings -> getWidgetVideosPaginator($params);
		 // Set item count per page and current page number
   		$paginator -> setItemCountPerPage(6);
        $paginator -> setCurrentPageNumber($this->_getParam('page'), 1);
	}
	
	public function listAction()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		
		//Check viewer and subject requirement
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		
		//Checking Ynvideo Plugin - View privacy
		$ynvideo_enable = Engine_Api::_() -> hasModuleBootstrap('ynvideo');
		$video_enable = Engine_Api::_() -> hasModuleBootstrap('video');
		$this->view->form = $form = new Ynmultilisting_Form_Video_Manage();
		if (!$video_enable && !$ynvideo_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		
		//Get viewer, listing, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		
		// Check create video authorization
		if ($viewer -> getIdentity() == $listing->user_id)
		{
			$this -> view -> canCreate = $listing -> isAllowed('video');
		}
		else
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		//Get data
		$tableMapping = Engine_Api::_()->getDbTable('mappings', 'ynmultilisting');
		$params['listing_id'] = $this->_getParam('listing_id');
		$this -> view -> paginator = $paginator = $tableMapping->getVideosPaginator($params);
		
		foreach( $paginator as $video )
	    {
	      $subform = new Ynmultilisting_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));
	      if($video->video_id == $listing->video_id)
	        $subform->removeElement('delete');
	      $subform->populate($video->toArray());
	      $form->addSubForm($subform, $video->getGuid());
	    }
		
		// Check post/form
	    if( !$this->getRequest()->isPost() ) {
	          return;
	    }
	        
	    $post = $this->getRequest()->getPost();
	    if(!$form->isValid($post))
	      return;
		$cover = $this->_getParam('cover');
	    // Process
	     foreach( $paginator as $video )
	      {
	        $subform = $form->getSubForm($video->getGuid());
	        $subValues = $subform->getValues();
	        $subValues = $subValues[$video->getGuid()];
	        unset($subValues['video_id']);
	
	        if( isset($cover) && $cover == $video->video_id) {
	          $listing->video_id = $video->video_id;
	          $listing->save();
	        }
	
	        if( isset($subValues['delete']) && $subValues['delete'] == '1' )
	        {
	          if( $listing->video_id == $video->video_id ){
			    $listing->video_type = null;
	            $listing->video_id = 0;
	            $listing->save();
	          }
			 $select =  $tableMapping -> select() -> where('listing_id = ?', $listing->getIdentity()) -> where('item_id=?',$video->video_id) -> limit(1);
	          $mapping_row = $tableMapping->fetchRow($select);
			  $mapping_row->delete();
	          $video->delete();
	        }
	        else
	        {
	          $video->setFromArray($subValues);
	          $video->save();
	        }
	      }
	  return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'ynmultilisting_general', true);
	}

		
	public function suggestAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		//Checking Ynvideo Plugin - View privacy
		$video_enable = Engine_Api::_() -> hasModuleBootstrap('video');
		$ynvideo_enable = Engine_Api::_() -> hasModuleBootstrap('ynvideo');
		$ynultimate_video = Engine_Api::_()->hasModuleBootstrap('ynultimatevideo');
		if (!$video_enable && !$ynvideo_enable && !$ynultimate_video)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		// get enabled video type
		$video_type = Engine_Api::_()->ynmultilisting()->getMultilistingVideoType();

		if ($video_type == 'video' || $video_type == 'ynvideo')
		{
			if ($video_enable || $ynvideo_enable) {
				//Get data from module ynvideos and se videos
				$table = Engine_Api::_() -> getItemTable('video');
				$select = $table -> select() ->order('title ASC');
				if( $this -> _getParam('text'))
				{
					$text = $this -> _getParam('text');
					$select->where('title LIKE ?', '%'.$text.'%');
					$select->where('owner_id = ?', $viewer->getIdentity());
				}
				$tags =  $table->fetchAll($select);
				$data = array();
				$video_type = $ynultimate_video ? 'ynultimatevideo_video' : 'video';
				foreach ($tags as $tag) {
					$data[] = array('id' => $tag -> getGUID(), 'label' => $tag -> title);
				}
			}
		}
		else if ($video_type == 'ynultimatevideo_video')
		{
			//Get data from module ynultimate videos
			if ($ynultimate_video) {
				$table_ynultimatevideo = Engine_Api::_()->getItemTable('ynultimatevideo_video');
				$select_ultimatevideo = $table_ynultimatevideo->select()->order('title ASC');
				if ($this->_getParam('text')) {
					$text = $this->_getParam('text');
					$select_ultimatevideo->where('title LIKE ?', '%'.$text.'%');
					$select_ultimatevideo->where('owner_id = ?', $viewer->getIdentity());
				}
				$videos = $table_ynultimatevideo->fetchAll($select_ultimatevideo);
				foreach ($videos as $video)
				{
					$data[] = array('id' => $video -> getGUID(), 'label' => $video -> title);
				}
			}
		}

		if ($this -> _getParam('sendNow', true)) {
			return $this -> _helper -> json($data);
		} else {
			$this -> _helper -> viewRenderer -> setNoRender(true);
			$data = Zend_Json::encode($data);
			$this -> getResponse() -> setBody($data);
		}
	}
}
?>
