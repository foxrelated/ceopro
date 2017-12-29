<?php
class Ynmember_IndexController extends Core_Controller_Action_Standard
{
	  public function init()
	  {
		$require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
	    if(!$require_check)
	    {
	      if(!$this->_helper->requireUser()->isValid()) 
	      	return;
		}
	  }
	  public function indexAction()
	  {
    	$this->_helper->content->setNoRender()->setEnabled();
	  }
	  
	  
	  public function directionAction() 
	  {
        $id = $this -> _getParam('id', 0);
		$type = $this -> _getParam('type', 0);
        if (!$id || !$type) {
            return $this->_helper->requireAuth()->forward();
        }
        switch ($type) {
            case 'work':
                $item_type = 'ynmember_workplace';
                break;
            case 'live':
                $item_type = 'ynmember_liveplace';
                break;
			case 'study':
                $item_type = 'ynmember_studyplace';
                break;	
        }
        $this->view->item = $item = Engine_Api::_()->getItem($item_type, $id);
        if (empty($item)) {
            return $this->_helper->requireAuth()->forward();
        }   
      }
	  
	  public function viewinfoAction()
	  {
	  	
	  	$id = $this->_getParam('id');
		$this -> view -> user = $user = Engine_Api::_()->getItem('user', $id);
		//get review about self
		$reviewTable = Engine_Api::_()-> getItemTable('ynmember_review');
		$this -> view -> reviews = $reviews = $reviewTable -> getAllReviewsByResourceId($id);
		//get rating
		$tableRating = Engine_Api::_()-> getItemTable('ynmember_rating');
		$this -> view -> ratings = $ratings = $tableRating -> fetchAll($tableRating -> select() -> where('resource_id = ?', $id));
		
		// Load fields view helpers
      	$view = $this->view;
      	$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		
	  }
	  public function rateMemberAction()
	  {
	  	$viewer = Engine_Api::_() -> user() -> getViewer();
	  	$id = $this->_getParam('id');
		if(empty($id))
		{
			$this->_helper->requireSubject()->forward();
		}
		$can_review_members = ($this->_helper->requireAuth()->setAuthParams('ynmember_user', null, 'can_review_members') -> checkRequire());
	  	// Viewer can not rate and review
	  	$user = Engine_Api::_()->getItem('user', $id);
		if(!$user)
		{
			$this->_helper->requireSubject()->forward();
		}
		if($viewer -> isSelf($user))
		{
			$can_review_oneself = ($this->_helper->requireAuth()->setAuthParams('ynmember_user', null, 'can_review_oneself') -> checkRequire());
			if(!$can_review_oneself)
			{
				return $this -> _helper -> requireAuth() -> forward();
			}
		}
		else {
			if(!$can_review_members)
			{
				return $this -> _helper -> requireAuth() -> forward();
			}
		}
		//check hasReviewed
		$tableReview = Engine_Api::_() -> getItemTable('ynmember_review');
		$HasReviewed = $tableReview -> checkHasReviewed($id, $viewer -> getIdentity());
		
		if($HasReviewed)
		{
			$this->_helper->requireSubject()->forward();
		}
		
		$rating_types = array();
		$tableRatingType = Engine_Api::_() -> getItemTable('ynmember_ratingtype');
		$rating_types = $tableRatingType -> getAllRatingTypes();
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynmember_review');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
		{
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array(
				'topLevelId' => $profileTypeField -> field_id,
				'topLevelValue' => 1,
			);
		}
		
	  	// Get form
		$this -> view -> form = $form = new Ynmember_Form_Rate(array(
			'user' => $user,
			'ratingTypes' => $rating_types,
			'formArgs' => $formArgs,
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
		foreach($rating_types as $item)
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
		$review = Engine_Api::_() -> getItemTable('ynmember_review') -> createRow();
		$review -> resource_id = $id;
		$review -> user_id = $viewer -> getIdentity();
		$review -> title = $values['title'];
		$review -> summary = $values['summary'];
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
		
		// Add fields
		$customfieldform = $form -> getSubForm('fields');
		$customfieldform -> setItem($review);
		$customfieldform -> saveValues();		
		// Rating
		
		//Specific General Rating
		$tableRating = Engine_Api::_() -> getItemTable('ynmember_rating');
		$row_general = $tableRating -> createRow();
		$row_general -> resource_id = $id;
		$row_general -> user_id = $viewer -> getIdentity();
		$row_general -> rating_type = '0'; // 0 means general rating
		$row_general -> rating = $this->_getParam('review_rating');
		$row_general -> review_id = $review -> getIdentity();
		$row_general -> save();
		
		// General Rating
		$user -> rating = $tableRating->getRateResouce($id);
		$user -> review_count = $user -> review_count++;
		$user -> save();
		
		// Specific Rating
		foreach($rating_types as $item)
		{
			$row = $tableRating -> createRow();
			$row -> resource_id = $id;
			$row -> user_id = $viewer -> getIdentity();
			$row -> rating_type = $item -> getIdentity();
			$param_rating = 'review_rating_'.$item -> getIdentity();
			$row -> rating = $this->_getParam($param_rating);
			$row -> review_id = $review -> getIdentity();
			$row -> save();
		}
		
		if ($review->resource_id != $review->user_id)
		{
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			$reviewUserLink = $this->view->url(array('controller' => 'review', 'action' => 'detail', 'id' => $review->getIdentity()), 'ynmember_extended');
			$reviewLabel = "<a href='{$reviewUserLink}'>{$this->view->translate('review')}</a>";
			$notifyApi->addNotification($user, $viewer, $user, 'ynmember_rated', array(
	          			'label' => $this->view->translate('you'),
						'text' => $reviewLabel
			));
		}
		
		if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
        {
            Engine_Api::_()->yncredit()-> hookCustomEarnCredits($viewer, $user -> getTitle(), 'ynmember_rate', $user);
		}
			
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Rated Successfully.')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	  }
	  
	  public function editRateMemberAction()
	  {
	  	$viewer = Engine_Api::_() -> user() -> getViewer();
	  	$id = $this->_getParam('id');
		$review = Engine_Api::_() -> getItem('ynmember_review', $id);
		if(empty($review))
		{
			$this->_helper->requireSubject()->forward();
		}
		 $can_edit_own_review = ($this->_helper->requireAuth()->setAuthParams('ynmember_review', null, 'can_edit_own_review') -> checkRequire());
	  	// Viewer can not rate and review
	  	if(!$can_edit_own_review)
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
	  	$user = Engine_Api::_()->getItem('user', $review -> resource_id);
		if(!$user)
		{
			$this->_helper->requireSubject()->forward();
		}
		if($review -> user_id != $viewer -> getIdentity())
		{
			$this->_helper->requireAuth()->forward();
		}
		$rating_types = array();
		$tableRating = Engine_Api::_() -> getItemTable('ynmember_rating');
		//$ratings = $tableRating -> fetchAll($tableRating -> select() -> where('review_id = ?', $id));
		
		$tableRatingType = Engine_Api::_() -> getItemTable('ynmember_ratingtype');
		$rating_types = $tableRatingType -> getAllRatingTypes();
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynmember_review');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
		{
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array(
				'topLevelId' => $profileTypeField -> field_id,
				'topLevelValue' => 1,
			);
		}
		
	  	// Get form
		$this -> view -> form = $form = new Ynmember_Form_EditRate(array(
			'user' => $user,
			//'ratings' => $ratings,
			'ratingTypes' => $rating_types,
			'formArgs' => $formArgs,
			'item' => $review,
		));
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
		foreach($rating_types as $item)
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
		$review -> title = $values['title'];
		$review -> summary = $values['summary'];
		$review -> modified_date = date("Y-m-d H:i:s");
		$review -> save();
		
		// Add fields
		$customfieldform = $form -> getSubForm('fields');
		$customfieldform -> setItem($review);
		$customfieldform -> saveValues();		
		
		//Specific General Rating
		$tableRating = Engine_Api::_() -> getItemTable('ynmember_rating');
		$select = $tableRating -> select() 
							   -> where('resource_id = ?', $review -> resource_id)
							   -> where('user_id = ?', $viewer -> getIdentity())
							   -> where('review_id = ?', $review -> getIdentity())
							   -> where('rating_type = 0');
		
		$row_general = $tableRating -> fetchRow($select);
		$row_general -> rating = $this->_getParam('review_rating');
		$row_general -> save();
		
		// Specific Rating
		foreach($rating_types as $item)
		{
			$row = $tableRating -> getRowRatingThisType($item -> getIdentity(), $user -> getIdentity(), $viewer -> getIdentity(), $review->getIdentity());
			if(!$row)
			{
				$row = $tableRating -> createRow();
			}
			$row -> resource_id = $review -> resource_id;
			$row -> user_id = $viewer -> getIdentity();
			$row -> rating_type = $item -> getIdentity();
			$param_rating = 'review_rating_'.$item -> getIdentity();
			$row -> rating = $this->_getParam($param_rating);
			$row -> review_id = $review -> getIdentity();
			$row -> save();
		}
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Edit Rate Successfully.')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	  }
	  
	  public function featureMemberAction()
	  {
	  		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	  		$viewer = Engine_Api::_() -> user() -> getViewer();
			$feature_fee = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynmember_user', 'feature_fee');
		    if ($feature_fee == null) {
				$row = $permissionsTable->fetchRow($permissionsTable->select()
				->where('level_id = ?', $viewer->level_id)
				->where('type = ?', 'ynmember_user')
				->where('name = ?', 'feature_fee'));
				if ($row) {
					$feature_fee= $row->value;
				}
			}	
		  	// Get form
			$this -> view -> form = $form = new Ynmember_Form_Feature(array(
				'fee' => $feature_fee,
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
			$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'controller' => 'index',
								'action' => 'place-order',
								'day' => $this->_getParam('day'),
								), 'ynmember_general', true);
								
			$this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $redirect_url,
                'format' => 'smoothbox',
                'messages' => array($this->view->translate("Please wait!"))
            ));
	  }
	  
	  public function placeOrderAction()
	 {
		$this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
		$this -> view -> day = $day = $this->_getParam('day');
		$feature_fee = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynmember_user', 'feature_fee');
	    if ($feature_fee== null) {
			$row = $permissionsTable->fetchRow($permissionsTable->select()
			->where('level_id = ?', $viewer->level_id)
			->where('type = ?', 'ynmember_user')
			->where('name = ?', 'feature_fee'));
			if ($row) {
				$feature_fee= $row->value;
			}
		}
		if($feature_fee == 0)
		{
			$featureTable =  Engine_Api::_() -> getItemTable('ynmember_feature');
			$featureRow  = $featureTable -> getFeatureRowByUserId($viewer->getIdentity());
			
			if($day == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$now =  date("Y-m-d H:i:s");
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($day." ".$type));
			if(!empty($featureRow)) //used to feature member
			{
				if($featureRow -> active == 1)
				{
					$expiration_date = date_add(date_create($featureRow->expiration_date),date_interval_create_from_date_string($day." ".$type));
				}
				$featureRow -> modified_date = $now;
				$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");;
				$featureRow -> active = 1;
				$featureRow -> save();  
			}
			else //first time
			{
				$featureRow = $featureTable -> createRow();
				$featureRow -> user_id = $viewer -> getIdentity();
				$featureRow -> creation_date = $now;
				$featureRow -> modified_date = $now;
				$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");;
				$featureRow -> active = 1;
				$featureRow -> save();  
				
			}
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'module' => 'user',
					'controller' => 'profile',
					'action' => 'index',
					'id' => $viewer->getIdentity(),
				), 'user_profile', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Feature successfully...'))
			));
		}
		
		//Credit
		//check permission
		// Get level id
		$id = $viewer -> level_id;
		$allowPayCredit = 0;
		$credit_enable = Engine_Api::_() -> ynmember() -> checkYouNetPlugin('yncredit');
		if ($credit_enable)
		{
			$typeTbl = Engine_Api::_() -> getDbTable("types", "yncredit");
			$select = $typeTbl -> select() -> where("module = 'yncredit'") -> where("action_type = 'feature_member'") -> limit(1);
			$type_spend = $typeTbl -> fetchRow($select);
			if ($type_spend)
			{
				$creditTbl = Engine_Api::_() -> getDbTable("credits", "yncredit");
				$select = $creditTbl -> select() -> where("level_id = ? ", $id) -> where("type_id = ?", $type_spend -> type_id) -> limit(1);
				$spend_credit = $creditTbl -> fetchRow($select);
				if ($spend_credit)
				{
					$allowPayCredit = 1;
				}
			}
		}
		$this -> view -> allowPayCredit = $allowPayCredit;
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		if ((!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit))
		{
			return $this -> _redirector();
		}
		$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynmember');
		if ($row = $ordersTable -> getLastPendingOrder())
		{
			$row -> delete();
		}
		$db = $ordersTable -> getAdapter();
		$db -> beginTransaction();
		
		$this->view->feature_fee = $feature_fee;
		$this->view->currency =  $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
		try
		{
			$ordersTable -> insert(array(
				'user_id' => $viewer -> getIdentity(),
				'creation_date' => new Zend_Db_Expr('NOW()'),
				'user_id' => $viewer -> getIdentity(),
				'price' => $feature_fee*$day,
				'currency' => $currency,
				'number_day' => $day,
			));
			// Commit
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		// Gateways
		$gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
		$gateways = $gatewayTable -> fetchAll($gatewaySelect);

		$gatewayPlugins = array();
		foreach ($gateways as $gateway)
		{
			$gatewayPlugins[] = array(
				'gateway' => $gateway,
				'plugin' => $gateway -> getGateway()
			);
		}
		$this -> view -> gateways = $gatewayPlugins;
	}

	public function updateOrderAction()
	{
		$type = $this -> _getParam('type');
		if (isset($type))
		{
			switch ($type)
			{
				case 'paycredit' :
					
					$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynmember');
					$order = $ordersTable -> getLastPendingOrder();
					if (!$order)
					{
						return $this -> _redirector();
					}
					
					return $this -> _forward('success', 'utility', 'core', array(
						'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
							'controller' => 'index',
							'action' => 'pay-credit',
							'order_id' => $order -> getIdentity(),
						), 'ynmember_general', true),
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
					));
					break;
				default :
					break;
			}
		}

		$gateway_id = $this -> _getParam('gateway_id', 0);
		if (!$gateway_id)
		{
			return $this -> _redirector();
		}

		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
		$gateway = $gatewayTable -> fetchRow($gatewaySelect);
		if (!$gateway)
		{
			return $this -> _redirector();
		}

		$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynmember');
		$order = $ordersTable -> getLastPendingOrder();
		if (!$order)
		{
			return $this -> _redirector();
		}
		$order -> gateway_id = $gateway -> getIdentity();
		$order -> save();

		$this -> view -> status = true;
		if (!in_array($gateway -> title, array(
			'2Checkout',
			'PayPal'
		)))
		{
			$this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'action' => 'process-advanced',
					'order_id' => $order -> getIdentity(),
					'm' => 'ynmember',
					'cancel_route' => 'ynmember_transaction',
					'return_route' => 'ynmember_transaction',
				), 'ynpayment_paypackage', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		else
		{
			$this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'transaction',
					'action' => 'process',
					'order_id' => $order -> getIdentity(),
				), 'ynmember_extended', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
	}
	
	public function payCreditAction()
    {
        $credit_enable = Engine_Api::_() -> ynmember() -> checkYouNetPlugin('yncredit');
        if (!$credit_enable)
        {
            return $this -> _redirector();
        }
        $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'feature_member'")->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            return $this -> _redirector();
        }
		$order = Engine_Api::_()->getItem('ynmember_order', $this->_getParam('order_id'));
		if(!$order)
        {
            return $this -> _redirector();
        }
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
        $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
		
        $cancel_url = Zend_Controller_Front::getInstance()->getRouter()
                ->assemble(
                  array(
                    'action' => 'place-order',
                    'day' => $order -> number_day
                  ), 'ynmember_general', true);
	    //publish fee
        $this -> view -> total_pay = $total_pay =  $order -> price ;    
        $credits = ceil(($total_pay * $defaultPrice * $numbers));
        $this -> view -> cancel_url = $cancel_url;
        $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
        if (!$balance) 
        {
          $currentBalance = 0;
        } else 
        {
          $currentBalance = $balance->current_credit;
        }
        $this->view->currentBalance = $currentBalance;
        $this->view->credits = $credits;
        $this->view->enoughCredits = $this->_checkEnoughCredits($credits);
    
        // Check method
        if (!$this->getRequest()->isPost()) 
        {
          return;
        }
    
        // Insert member transaction
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynmember');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
	     	//active feature
	     	$featureTable =  Engine_Api::_() -> getItemTable('ynmember_feature');
			$featureRow  = $featureTable -> getFeatureRowByUserId($order->user_id);
			
			if($order->number_day == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$now =  date("Y-m-d H:i:s");
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($order->number_day." ".$type));
			if(!empty($featureRow)) //used to feature member
			{
				if($featureRow -> active == 1)
				{
					$expiration_date = date_add(date_create($featureRow->expiration_date),date_interval_create_from_date_string($order->number_day." ".$type));
				}
				$featureRow -> modified_date = $now;
				$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$featureRow -> active = 1;
				$featureRow -> save();  
			}
			else //first time
			{
				$featureRow = $featureTable -> createRow();
				$featureRow -> user_id = $order->user_id;
				$featureRow -> creation_date = $now;
				$featureRow -> modified_date = $now;
				$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$featureRow -> active = 1;
				$featureRow -> save();  
			}
			$description = $this ->view ->translate(array('Feature in %s day', 'Feature in %s days', $order -> number_day), $order -> number_day);
			
			/**
	         * Call Event from Affiliate
	         */
			if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))	
			{
				$params['module'] = 'ynmember';
				$params['user_id'] = $order->user_id;
				$params['rule_name'] = 'feature_member';
				$params['total_amount'] = $order->price;
				$params['currency'] = $order->currency;
	        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
			}
	        /**
	         * End Call Event from Affiliate
	         */
			
			//save transaction
	     	$transactionsTable->insert(array(
	     	'creation_date' => date("Y-m-d"),
	     	'status' => 'completed',
	     	'gateway_id' => '-3',
	     	'amount' => $order->price,
	     	'currency' => $order->currency,
	     	'user_id' => $order->user_id,
	     	'payment_transaction_id' => $params['transaction_id'],
	     	'description' => $description,
		  ));
	      $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
		
        Engine_Api::_()->yncredit()-> spendCredits($viewer, (-1) * $credits, $viewer->getTitle(), 'feature_member', $viewer);
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'profile', 'action' => 'index', 'id' => $order -> user_id), 'user_profile', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }
	
	protected function _redirector()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'controller' => 'profile',
								'action' => 'index',
								'id' => $viewer -> getIdentity(),
		), 'user_profile', true);	
			
		$this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => $redirect_url,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Error!'))
		));
	}

	protected function _checkEnoughCredits($credits)
	{
	    $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
	    if (!$balance) {
	      return false;
	    }
	    $currentBalance = $balance->current_credit;
	    if ($currentBalance < $credits) {
	      return false;
	    }
	    return true;
	}
	  
	public function suggestAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer->getIdentity() ) 
		{
      		$data = null;
		}
		else
		{
			$table_user = Engine_Api::_() -> getDbtable('users', 'user');
			$table_user_name = $table_user -> info('name');
			$data = array();

			//$table = Engine_Api::_() -> getItemTable('event');
			//$select = $event -> membership() -> getMembersObjectSelect();
			$select = $viewer -> membership() -> getMembersObjectSelect();
			if (0 < ($limit = (int)$this -> _getParam('limit', 10)))
			{
				$select -> limit($limit);
			}

			if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value'))))
			{
				$select -> where('displayname LIKE ?', '%' . $text . '%');
			}
			foreach ($select->getTable()->fetchAll($select) as $friend)
			{
				$data[] = array(
					'type' => 'user',
					'id' => $friend -> getIdentity(),
					'guid' => $friend -> getGuid(),
					'label' => $friend -> getTitle(),
					'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'),
					'url' => $friend -> getHref(),
				);
			}
		}
		return $this -> _helper -> json($data);
	}
	
	public function privacyAction()
	{    
		 // Render
    	$this->_helper->content
        // ->setNoRender()
        ->setEnabled();
        
	    $user = Engine_Api::_()->user()->getViewer();
	    $settings = Engine_Api::_()->getApi('settings', 'core');
	    $auth = Engine_Api::_()->authorization()->context;
	
	    $this->view->form = $form = new Ynmember_Form_Settings_Privacy(array(
	      'item' => $user,
	    ));
	
	    // Hides options from the form if there are less then one option.
	    if( count($form->get_notification_privacy->options) <= 1 ) {
	      $form->removeElement('get_notification_privacy');
	    }
	
	    // Populate form
	    $form->populate($user->toArray());
	    
	    // Check if post and populate
	    if( !$this->getRequest()->isPost() ) {
	      return;
	    }
	
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	      $this->view->status = false;
	      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
	      return;
	    }
	
	    $form->save();
	    $user->setFromArray($form->getValues())->save();
	    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
	    
	   
	}

	public function getMutualFriendsAction()
	{
		$except_id = $this->_getParam('except_id');
		$viewer = Engine_Api::_()->user()->getViewer();
	    // Don't render this if friendships are disabled
	    if( !Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible ) {
	    	 $this->_helper->requireSubject()->forward();
	    }
		
	    // Get subject and check auth
	    $subject = Engine_Api::_()->getItem('user', $this->_getParam('subject_id'));
	
	    // If no viewer or viewer==subject, don't display
	    if( !$viewer->getIdentity() || $viewer->isSelf($subject) || !$subject->getIdentity() ) {
	      	$this->_helper->requireSubject()->forward();
	    }
	
	    // Diff friends
	    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
	    $friendsName = $friendsTable->info('name');
	
	    // Mututal friends/following mode
	    $col1 = 'resource_id';
	    $col2 = 'user_id';
	
	    $select = new Zend_Db_Select($friendsTable->getAdapter());
	    $select
	      ->from($friendsName, $col1)
	      ->join($friendsName, "`{$friendsName}`.`{$col1}`=`{$friendsName}_2`.{$col1}", null)
	      ->where("`{$friendsName}`.{$col2} = ?", $viewer->getIdentity())
	      ->where("`{$friendsName}_2`.{$col2} = ?", $subject->getIdentity())
	      ->where("`{$friendsName}`.active = ?", 1)
	      ->where("`{$friendsName}_2`.active = ?", 1)
	      ;
	    // Now get all common friends
	    $uids = array();
	    foreach( $select->query()->fetchAll() as $data ) {
	      if(!empty($except_id))
		  {
		  	 if($except_id == $data[$col1])
			 {
			 	continue;
			 }
		  }
	      $uids[] = $data[$col1];
	    }
	
	    // Get paginator
	    $usersTable = Engine_Api::_()->getItemTable('user');
	    $select = $usersTable->select()
	      ->where('user_id IN(?)', $uids)
	      ;
	
	    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
	
	    // Set item count per page and current page number
	    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 6));
	    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	}
	
	//view map view
	public function displayMapViewAction()
	{
		$featureTable = Engine_Api::_() -> getItemTable('ynmember_feature');
	    $userTbl = Engine_Api::_()-> getItemTable('user');
	    $userTblName = $userTbl -> info('name');
	    $userIds = $this->_getParam('ids', '');
	    $params = $this->_getAllParams();
	    if ($userIds != '')
	    {
	    	$userIds = explode("_", $userIds);
	    }
	    $select = $userTbl -> select()
			-> where("enabled = 1") 
			-> where("verified = 1") 
			-> where("approved = 1");
	    
		if (is_array($userIds) && count($userIds))
		{
			$select -> where ("$userTblName.user_id IN (?)", $userIds);
		}
		else 
		{
			$select -> where ("0 = 1");
		}
		
		//Get your location
		$target_distance = $base_lat = $base_lng = "";
		if (isset($params['lat']) && $params['lat'])
			$base_lat = $params['lat'];
		if (isset($params['long']) && $params['long'])
			$base_lng = $params['long'];
	    
		//Get target distance in miles
		if (isset($params['within']))
			$target_distance = $params['within'];

		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) 
		{
			$workPlaceTbl = Engine_Api::_()->getItemTable('ynmember_workplace');
			$workPlaceData = $workPlaceTbl->getLocations($base_lat, $base_lng, $target_distance);
			$workUserIds = array_keys($workPlaceData);
			
			$livePlaceTbl = Engine_Api::_()->getItemTable('ynmember_liveplace');
			$livePlaceData = $livePlaceTbl->getLocations($base_lat, $base_lng, $target_distance);
			$liveUserIds = array_keys($livePlaceData);
		} else {
            $workPlaceTbl = Engine_Api::_()->getItemTable('ynmember_workplace');
            $workPlaceData = $workPlaceTbl->getAllLocations();
            $workUserIds = array_keys($workPlaceData);

            $livePlaceTbl = Engine_Api::_()->getItemTable('ynmember_liveplace');
            $livePlaceData = $livePlaceTbl->getAllLocations();
            $liveUserIds = array_keys($livePlaceData);
        }
		$users = $userTbl->fetchAll($select);
		$datas = array();
		$contents = array();
		$http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
		$icon_guest = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynmember/externals/images/ynmember-maps-person.png';
		
		foreach($users as $user)
		{			
			if (in_array($user->getIdentity(), $workUserIds))
			{
				$latitude = $workPlaceData[$user->getIdentity()]['latitude'];
				$longitude = $workPlaceData[$user->getIdentity()]['longitude'];
			}
			else if (in_array($user->getIdentity(), $liveUserIds))
			{
				$latitude = $livePlaceData[$user->getIdentity()]['latitude'];
				$longitude = $livePlaceData[$user->getIdentity()]['longitude'];
			}
			else 
			{
				continue;
			}
			
			if($latitude && $longitude)	
			{				
				$icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynmember/externals/images/maker.png';
				
				if($featureTable -> getFeatureRowByUserId($user -> getIdentity()))
				{
					$icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynmember/externals/images/feature_maker.png';
				}
				$datas[] = array(	
						'user_id' => $user -> getIdentity(),				
						'latitude' => $latitude,
						'longitude' => $longitude,
						'icon' => $icon
					);
				$memicon = "<img src='".$icon_guest."' />";
				$contents[] = '
					<div class="ynmember-maps-main" style="height: 20px; overflow: hidden;">	
	      				<div class="ynmember-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">	      					
							<span style="margin-right: 5px; font-size: 11px;">
								'.$memicon.'
							</span>								
							<a href="'.$user->getHref().'" class="ynmember-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
								'.$user->getTitle().'
							</a>								
			      		</div>
					</div>
				';
			}
		}
		echo $this ->view -> partial('_map_view.tpl', 'ynmember',array('datas'=>Zend_Json::encode($datas), 'contents' => Zend_Json::encode($contents)));
		exit();
	}

	public function getMyLocationAction()
	{
		$latitude = $this -> _getParam('latitude');
		$longitude = $this -> _getParam('longitude');
		$values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
		echo $values;
		die ;
	}
}
