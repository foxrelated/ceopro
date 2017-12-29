<?php
class Ynmultilisting_AdminReviewsController extends Core_Controller_Action_Admin {
    public function indexAction() 
    {
    	$viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_reviews');
        
		$tableReview  = Engine_Api::_() -> getItemTable('ynmultilisting_review');
		
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Review_Search();
        $form->populate($this->_getAllParams());
        $params = $form->getValues();
		
		$oldTz = date_default_timezone_get();
   		date_default_timezone_set($viewer->timezone);
		if(!empty($values['from_date']))
	   		$from_date = strtotime($values['from_date']);
		if(!empty($values['to_date']))
	    	$to_date = strtotime($values['to_date']);
		if(!empty($values['from_date']))
	   		$params['from_date'] = date('Y-m-d H:i:s', $from_date);
		if(!empty($values['to_date']))
	    	$params['to_date'] = date('Y-m-d H:i:s', $to_date);
	    date_default_timezone_set($oldTz);
		  
        $page = $this->_getParam('page',10);
		$params['page'] = $page;
        $this->view->paginator = $paginator = $tableReview -> getReviewsPaginator($params);
		$paginator->setItemCountPerPage($page);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->formValues = $params;
    }
	
	public function deleteAction()
	{
		$id = $this->_getParam('id');
		$this->view->form = $form = new Ynmultilisting_Form_Admin_Review_Delete();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$review = Engine_Api::_()->getItem('ynmultilisting_review', $id);
        if ($review) {
        	
			//get listing
        	$listing = $review -> getParent();
							
        	//get tables
        	$tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');
			$tableRatingValue = Engine_Api::_() -> getDbTable('ratingvalues', 'ynmultilisting');
			$tableReviewValue = Engine_Api::_() -> getDbTable('reviewvalues', 'ynmultilisting');
            
			//delete values
			$tableRatingValue -> deleteReview($review -> getIdentity());
			$tableReviewValue -> deleteReview($review -> getIdentity());
		   
		    $review -> delete();
			if($listing)
			{
	            $listing -> review_count -= 1;
	            $listing -> rating  = $tableReview->getRateListing($listing -> getIdentity());
				$listing -> save();
			}
			
        }
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Review deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}
	
	 public function multiselectedAction() 
	 {
        $action = $this -> _getParam('select_action', 'Delete');
        $this->view->action = $action;
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true) {
            $ids_array = explode(",", $ids);
            switch ($action) {
                case 'Delete':
                    foreach ($ids_array as $id) {
                        $review = Engine_Api::_()->getItem('ynmultilisting_review', $id);
                        if ($review) {
                        	
							//get listing
							$listing = $review -> getParent();
							
                        	//get tables
				        	$tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');
							$tableRatingValue = Engine_Api::_() -> getDbTable('ratingvalues', 'ynmultilisting');
							$tableReviewValue = Engine_Api::_() -> getDbTable('reviewvalues', 'ynmultilisting');
				            
							//delete values
							$tableRatingValue -> deleteReview($review -> getIdentity());
							$tableReviewValue -> deleteReview($review -> getIdentity());
						   
						    $review -> delete();
							if($listing)
							{
					            $listing -> review_count -= 1;
					            $listing -> rating  = $tableReview->getRateListing($listing -> getIdentity());
								$listing -> save();
							}
			
                        }
                    }
                    break;
            }
            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }
	 
	 public function viewDetailAction()
	 {
	 	$id = $this->_getParam('id');
		$review = Engine_Api::_() -> getItem('ynmultilisting_review', $id);
		$this -> view -> review = $review;
	 }
}