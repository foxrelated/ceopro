<?php

class Ynmultilisting_ReviewController extends Core_Controller_Action_Standard
{
    public function indexAction()
    {
        $this -> _helper -> content	-> setEnabled();
        $this -> view -> viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
            ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
        $table = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $listingTbl = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $form = new Ynmultilisting_Form_Review_Search();
        $params = $this->_getAllParams();
        if ($form->isValid($params)) {
            $params = $form->getValues();
        } else {
            $params = array();
        }
        $this -> view -> formValues = $params;

        if(!empty($params['review_for']))
        {
            $listing_id = array();
            $list_reviewer = $listingTbl -> getListingsByText($params['review_for']);
            foreach($list_reviewer as $item)
            {
                $listing_id[] = $item -> getIdentity();
            }
            $params['listing_ids'] = $listing_id;
        }

        if(!empty($params['review_by']))
        {
            $user_id = array();
            $list_review_for = Engine_Api::_() -> ynmultilisting() -> getUsersByName($params['review_by']);
            foreach($list_review_for as $item)
            {
                $user_id[] = $item ->  getIdentity();
            }
            $params['user_ids'] = $user_id;
        }

        if(!empty($params['category_id']))
        {
            $listing_id = array();
            $listings = $listingTbl -> getListingByCategoryId($params['category_id']);
            foreach($listings as $listing)
            {
                $listing_id[] = $listing ->  getIdentity();
            }
            if (is_array($params['listing_ids']))
            {
                $params['listing_ids'] = array_merge($listing_id, $params['listing_ids']);
            }
            else
            {
                $params['listing_ids'] = $listing_id;
            }
        }

        if (!empty($params['orderby']))
        {
            switch ($params['orderby']) {
                case 'creation_date':
                    $params['order'] = 'creation_date';
                    $params['direction'] = 'DESC';
                    break;
                case 'most_rating':
                    $params['order'] = 'overal_rating';
                    $params['direction'] = 'DESC';
                    break;
                case 'least_rating':
                    $params['order'] = 'overal_rating';
                    $params['direction'] = 'ASC';
                    break;
                case 'helpful_count':
                    $params['order'] = 'helpful_count';
                    $params['direction'] = 'DESC';
                    break;
            }
        }
		$currentListingTypeId = Engine_Api::_() -> ynmultilisting() -> getCurrentListingTypeId();
		$params['listingtype_id'] = $currentListingTypeId;
        $this -> view -> paginator = $paginator = $table->getReviewsPaginator($params);
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 10));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

        // Add count to title if configured
        if( $this->_getParam('titleCount', true) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }


    public function viewAction()
    {
        $this -> _helper -> content	-> setEnabled();
        $reviewId = $this->_getParam('id');
        if( $reviewId )
        {
            $review = Engine_Api::_()->getItem('ynmultilisting_review', $reviewId);
        }
        if (!$review)
        {
            return $this->_helper->requireSubject()->forward();
        }

        if (!$review->isViewable()) {
            return $this->_helper->requireAuth()->forward();
        }
		
		$listingTypeId = $review -> getListingType() -> getIdentity();
		$currentListingTypeId = Engine_Api::_() -> ynmultilisting() -> getCurrentListingTypeId();
		if($listingTypeId != $currentListingTypeId){
			Engine_Api::_() -> ynmultilisting() -> setCurrentListingType($listingTypeId);
		}	
		
        if( !Engine_Api::_()->core()->hasSubject() )
        {
            Engine_Api::_()->core()->setSubject($review);
        }

        $this -> view -> can_report_reviews = true;
        $this -> view -> can_share_reviews = $can_share_reviews = true;
        $this -> view -> review = $review;
    }

    public function createAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $id = $this->_getParam('id');
        if(empty($id))
        {
            return $this->_helper->requireSubject()->forward();
        }

        $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $id);
        if(!$listing)
        {
            return $this->_helper->requireSubject()->forward();
        }

        $this->view->can_review = $can_review = $listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'review');
        if(!$can_review)
        {
            return $this -> _helper -> requireAuth() -> forward();
        }

        //check hasReviewed
        $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');
        $hasReviewed = $tableReview -> checkHasReviewed($id, $viewer -> getIdentity());

        if($hasReviewed)
        {
            $this -> view -> error = 1;
            $this -> view -> message = $this -> view -> translate('You have reviewed this listing');
        }

        $ratingTypes = array();
        $tableRatingType = Engine_Api::_() -> getItemTable('ynmultilisting_ratingtype');
        $ratingTypes = $tableRatingType -> getAllRatingTypes($listing -> category_id);

        $reviewTypes = array();
        $tableReviewType = Engine_Api::_() -> getItemTable('ynmultilisting_reviewtype');
        $reviewTypes = $tableReviewType -> getAllReviewTypes($listing -> category_id);

        // Get form
        $this -> view -> form = $form = new Ynmultilisting_Form_Review_Create(array(
            'listing' => $listing,
            'ratingTypes' => $ratingTypes,
            'reviewTypes' => $reviewTypes,
        ));

        // Check stuff
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }

        $values = $form -> getValues();

        //check rating empty
        foreach($ratingTypes as $item)
        {
            $param_rating = 'review_rating_'.$item -> getIdentity();
            $row_rating = $this->_getParam($param_rating);
            if(empty($row_rating))
            {
                $form -> addError('Please rating all!');
                return;
            }
        }

        //save general review
        $review = Engine_Api::_() -> getItemTable('ynmultilisting_review') -> createRow();
        $review -> listing_id = $id;
        $review -> user_id = $viewer -> getIdentity();
        $review -> title = $values['title'];
        $review -> pros = $values['pros'];
        $review -> cons = $values['cons'];
        $review -> overal_rating = $this ->_getParam('review_rating');
        $review -> overal_review = $values['overal_review'];
        $review -> creation_date = date("Y-m-d H:i:s");
        $review -> modified_date = date("Y-m-d H:i:s");
        $review -> save();

        // Set auth
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $commentMax = array_search('everyone', $roles);
        foreach( $roles as $i => $role ) {
            $auth->setAllowed($review, $role, 'comment',   ($i <= $commentMax));
        }

        // Get Tables
        $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');
        $tableRatingValue = Engine_Api::_() -> getDbTable('ratingvalues', 'ynmultilisting');
        $tableReviewValue = Engine_Api::_() -> getDbTable('reviewvalues', 'ynmultilisting');

        // General Rating
        $listing -> rating = $tableReview->getRateListing($listing -> getIdentity());
        $listing -> review_count += 1;
        $listing -> save();

        // Specific Rating
        foreach($ratingTypes as $item)
        {
            $ratingValue = $tableRatingValue -> createRow();
            $ratingValue -> listing_id = $id;
            $ratingValue -> ratingtype_id = $item -> getIdentity();
            $param_rating = 'review_rating_'.$item -> getIdentity();
            $ratingValue -> rating = $this->_getParam($param_rating);
            $ratingValue -> review_id = $review -> getIdentity();
            $ratingValue -> creation_date = date("Y-m-d H:i:s");
            $ratingValue -> modified_date = date("Y-m-d H:i:s");
            $ratingValue -> save();
        }

        // Specific Review
        foreach($reviewTypes as $item)
        {
        	
			$param_review = 'review_'.$item -> getIdentity();
			$content = $this->_getParam($param_review);
			$class = new Engine_Filter_HtmlSpecialChars;
			$content = $class -> filter($content);
			$class = new Engine_Filter_Censor;
			$content = $class -> filter($content);
			$class = new Engine_Filter_EnableLinks;
			$content = $class -> filter($content);
			
            $reviewValue = $tableReviewValue -> createRow();
            $reviewValue -> reviewtype_id = $item -> getIdentity();
            $param_review = 'review_'.$item -> getIdentity();
            $reviewValue -> listing_id = $id;
            $reviewValue -> content = $content;
            $reviewValue -> review_id = $review -> getIdentity();
            $reviewValue -> creation_date = date("Y-m-d H:i:s");
            $reviewValue -> modified_date = date("Y-m-d H:i:s");
            $reviewValue -> save();
        }

        //notification
        $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
        $notifyApi -> addNotification($listing -> getOwner(), $review, $listing, 'ynmultilisting_listing_add_item', array('label' => 'review'));

        //send email
        $params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
        $params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST'];
        $href =
            'http://'. @$_SERVER['HTTP_HOST'].
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $listing -> getIdentity(), 'slug' => $listing -> getSlug()),'ynmultilisting_profile',true);
        $params['listing_link'] = $href;
        $params['listing_name'] = $listing -> getTitle();
        try{
            Engine_Api::_()->getApi('mail','ynmultilisting')->send($listing -> getOwner(), 'ynmultilisting_listing_reviewed',$params);
        }
        catch(exception $e)
        {
            //keep silent
        }

        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Review Submitted Successfully.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

    public function editAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $id = $this->_getParam('id');
        $review = Engine_Api::_() -> getItem('ynmultilisting_review', $id);
        if(empty($review))
        {
            $this->_helper->requireSubject()->forward();
        }

        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $review -> listing_id);
        if(!$listing)
        {
            $this->_helper->requireSubject()->forward();
        }

        if(!$review -> isEditable())
        {
            return $this -> _helper -> requireAuth() -> forward();
        }

        $ratingTypes = array();
        $tableRatingType = Engine_Api::_() -> getItemTable('ynmultilisting_ratingtype');
        $ratingTypes = $tableRatingType -> getAllRatingTypes($listing -> category_id);

        $reviewTypes = array();
        $tableReviewType = Engine_Api::_() -> getItemTable('ynmultilisting_reviewtype');
        $reviewTypes = $tableReviewType -> getAllReviewTypes($listing -> category_id);


        // Get form
        $this -> view -> form = $form = new Ynmultilisting_Form_Review_Edit(array(
            'listing' => $listing,
            'ratingTypes' => $ratingTypes,
            'reviewTypes' => $reviewTypes,
            'item' => $review,
        ));
		
		$review -> pros = htmlspecialchars_decode($review -> pros);
		$review -> pros = strip_tags($review -> pros);
		
		$review -> cons = htmlspecialchars_decode($review -> cons);
		$review -> cons = strip_tags($review -> cons);
		
		$review -> overal_review = htmlspecialchars_decode($review -> overal_review);
		$review -> overal_review = strip_tags($review -> overal_review);
		
        $form -> populate($review -> toArray());

        // Check stuff
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }

        //check rating empty
        foreach($ratingTypes as $item)
        {
            $param_rating = 'review_rating_'.$item -> getIdentity();
            $row_rating = $this->_getParam($param_rating);
            if(empty($row_rating))
            {
                $form -> addError('Please rating all!');
                return;
            }
        }

        $values = $form -> getValues();

        //save general review
        
        $class = new Engine_Filter_HtmlSpecialChars;
		$values['overal_review'] = $class -> filter($values['overal_review']);
		$class = new Engine_Filter_Censor;
		$values['overal_review'] = $class -> filter($values['overal_review']);
		$class = new Engine_Filter_EnableLinks;
		$values['overal_review'] = $class -> filter($values['overal_review']);
		
		$class = new Engine_Filter_HtmlSpecialChars;
		$values['pros'] = $class -> filter($values['pros']);
		$class = new Engine_Filter_Censor;
		$values['pros'] = $class -> filter($values['pros']);
		$class = new Engine_Filter_EnableLinks;
		$values['pros'] = $class -> filter($values['pros']);
		
		$class = new Engine_Filter_HtmlSpecialChars;
		$values['cons'] = $class -> filter($values['cons']);
		$class = new Engine_Filter_Censor;
		$values['cons'] = $class -> filter($values['cons']);
		$class = new Engine_Filter_EnableLinks;
		$values['cons'] = $class -> filter($values['cons']);
        
        
        $review -> title = $values['title'];
        $review -> pros = $values['pros'];
        $review -> cons = $values['cons'];
        $review -> overal_rating = $this ->_getParam('review_rating');
        $review -> overal_review = $values['overal_review'];
        $review -> modified_date = date("Y-m-d H:i:s");
        $review -> save();

        // Get Tables
        $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');
        $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');
        $tableRatingValue = Engine_Api::_() -> getDbTable('ratingvalues', 'ynmultilisting');
        $tableReviewValue = Engine_Api::_() -> getDbTable('reviewvalues', 'ynmultilisting');

        // General Rating
        $listing -> rating = $tableReview->getRateListing($listing -> getIdentity());
        $listing -> save();

        // Specific Rating
        foreach($ratingTypes as $item)
        {
            $ratingValue = $tableRatingValue -> getRowRatingThisType($item -> getIdentity(), $review->getIdentity());
            if(!$ratingValue)
            {
                //if not
                $ratingValue = $tableRatingValue -> createRow();
                $ratingValue -> ratingtype_id = $item -> getIdentity();
                $param_rating = 'review_rating_'.$item -> getIdentity();
                $ratingValue -> rating = $this->_getParam($param_rating);
                $ratingValue -> review_id = $review -> getIdentity();
                $ratingValue -> creation_date = date("Y-m-d H:i:s");
                $ratingValue -> modified_date = date("Y-m-d H:i:s");
                $ratingValue -> save();
            }
            else
            {
                //if has
                $param_rating = 'review_rating_'.$item -> getIdentity();
                $ratingValue -> rating = $this->_getParam($param_rating);
                $ratingValue -> modified_date = date("Y-m-d H:i:s");
                $ratingValue -> save();
            }
        }

        // Specific Review
        foreach($reviewTypes as $item)
        {
        	$param_review = 'review_'.$item -> getIdentity();
			$content = $this->_getParam($param_review);
			$class = new Engine_Filter_HtmlSpecialChars;
			$content = $class -> filter($content);
			$class = new Engine_Filter_Censor;
			$content = $class -> filter($content);
			$class = new Engine_Filter_EnableLinks;
			$content = $class -> filter($content);
			
            $reviewValue = $tableReviewValue -> getRowReviewThisType($item -> getIdentity(), $review->getIdentity());
            if(!$reviewValue)
            {
                //if not
                $reviewValue = $tableReviewValue -> createRow();
                $reviewValue -> reviewtype_id = $item -> getIdentity();
                $reviewValue -> content = $content;
                $reviewValue -> review_id = $review -> getIdentity();
                $reviewValue -> creation_date = date("Y-m-d H:i:s");
                $reviewValue -> modified_date = date("Y-m-d H:i:s");
                $reviewValue -> save();
            }
            else
            {
                //if has
                $reviewValue -> content = $content;
                $reviewValue -> modified_date = date("Y-m-d H:i:s");
                $reviewValue -> save();
            }
        }

        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Edit Rate Successfully.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

    public function deleteAction() {
        $tab = $this -> getRequest() -> getParam('tab');
        $page = $this -> getRequest() -> getParam('page');

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $review = Engine_Api::_() -> getItem('ynmultilisting_review', $this -> getRequest() -> getParam('id'));
        $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $review->listing_id);
        if (!$review) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Review does not exists.");
            return;
        }

        if (!$viewer->getIdentity()) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to delete this review.");
            return;
        }

        if(!$viewer -> isSelf($review -> getOwner()))
        {
            if(!$review->isDeletable())
            {
                $this -> view -> error = true;
                $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to delete this review.");
                return;
            }
        }

        if (!$listing) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists.");
            return;
        }

        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> view -> form = $form = new Ynmultilisting_Form_Review_Delete();

        if (!$this -> getRequest() -> isPost()) {
            return;
        }

        $db = $review -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            //get tables
            $tableReview = Engine_Api::_() -> getItemTable('ynmultilisting_review');
            $tableRatingValue = Engine_Api::_() -> getDbTable('ratingvalues', 'ynmultilisting');
            $tableReviewValue = Engine_Api::_() -> getDbTable('reviewvalues', 'ynmultilisting');

            //delete values
            $tableRatingValue -> deleteReview($review -> getIdentity());
            $tableReviewValue -> deleteReview($review -> getIdentity());

            $review -> delete();
            $listing -> review_count -= 1;
            $listing -> rating  = $tableReview->getRateListing($listing -> getIdentity());
            $listing -> save();

            $db -> commit();
        }
        catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This review has been deleted.');

        if(isset($tab) && isset($page))
        {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id' => $listing->getIdentity(), 'slug' => $listing -> getSlug(), 'tab' => $tab, 'page' => $page), 'ynmultilisting_profile', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        else
        {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynmultilisting_review', true),
                'messages' => Array($this -> view -> message)
            ));
        }
    }

    public function usefulAction()
    {
        $review_id = $this->_getParam('review_id');
        $value = $this->_getParam('value');
        $inline = $this->_getParam('inline', false);
        if( !$this->getRequest()->isPost() ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }
        $this->view->review = $review = Engine_Api::_()->getItem('ynmultilisting_review', $review_id);
        if (!review){
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('This review is not existed.');
            return;
        }
        $viewer = Engine_Api::_()->user() ->getViewer();
        if (!$viewer->getIdentity()){
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Can not set useful');
            return;
        }
        $usefulTbl = Engine_Api::_()->getDbTable('usefuls', 'ynmultilisting');
        $row = $usefulTbl->getUseFul($viewer->getIdentity(), $review_id);
        if (!$row)
        {
            $row = $usefulTbl->createRow();
        }
        $row->setFromArray(array(
            'review_id' => $review_id,
            'user_id' => $viewer->getIdentity(),
            'value' => $value,
        ));
        $row->save();
        $params = $review->getReviewUseful();
        if (isset($params['yes_count']))
        {
            $review->helpful_count += (int)$params['yes_count'];
        }
        if (isset($params['no_count']))
        {
            $review->helpful_count += (int)$params['no_count'];
        }
        $review->save();
        $params['inline'] = ($inline) ? true : false;
        echo $this->view->partial(
            '_useful.tpl',
            'ynmultilisting',
            $params
        ); exit;
    }
}

