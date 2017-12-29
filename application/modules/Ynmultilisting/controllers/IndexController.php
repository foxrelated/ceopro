<?php

class Ynmultilisting_IndexController extends Core_Controller_Action_Standard {
	public function init() {
	    // only show to member_level if authorized
		$params = $this->_getAllParams();
		if (!isset($params['action']) || $params['action'] != 'display-map-view') {
	    if( !$this->_helper->requireAuth()->setAuthParams('ynmultilisting_listing', null, 'view')->isValid() ) return;
		}
		
		$listingType = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
		if (!$listingType) {
			$url = $this->view->url(array(), 'user_general', true);
			header('location:' . $url);
			exit;
		}
	}
	
	public function indexAction() {
		$this->_helper->content->setEnabled();
	}
	
	public function browseAction() {
		$this->_helper->content->setEnabled()->setNoRender();
	}
    
	public function unsubscribeAction()
	{
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$email = $this ->_getParam('email');
		if(!empty($email))
		{
			$tableSubscriber = Engine_Api::_() -> getDbTable('subscribers', 'ynmultilisting');
			$tableSentListing = Engine_Api::_() -> getDbTable('sentlistings', 'ynmultilisting');
			$tableSubscriber -> deleteRowsByEmail($email);
			$tableSentListing -> deleteRowsByEmail($email);
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynmultilisting_general', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Unsubscribed sucessfully!'))));
	}
	
	public function subscribeListingAction()
	{
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		$category_id = $this ->_getParam('category_id');
		$latitude = $this ->_getParam('latitude');
		$longitude = $this ->_getParam('longitude');
		$within = $this ->_getParam('within');
		$email = $this ->_getParam('email');
		
		// Load all emails
	    $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
	    $bannedEmails = $bannedEmailsTable->getEmails();
		
		//check if email is not in banned list
		if(!in_array($email, $bannedEmails))
		{
			//get setting max subscribe email by IP
			$settings = Engine_Api::_()->getApi('settings', 'core');
			$maxEmail = $settings->getSetting('ynmultilisting_max_subscribeemail', 1);
			$maxGetSubscribePerEmail = $settings->getSetting('ynmultilisting_max_getsubscribeperemail', 1);
			
			//get table Subscriber
			$tableSubscriber = Engine_Api::_() -> getDbTable('subscribers', 'ynmultilisting');
			
			// Get ip address
            $db = Engine_Db_Table::getDefaultAdapter();
            $ipObj = new Engine_IP();
            $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
                
			//check if ip is valid
			$ipEmails = $tableSubscriber -> getRowsByIP($ipExpr);
			if(count($ipEmails) < $maxEmail || in_array($email, $ipEmails))
			{
				$number_subscriber = $tableSubscriber -> getRowsByEmail($email);
				if(count($number_subscriber) < $maxGetSubscribePerEmail)
				{
					$row = $tableSubscriber -> createRow();
					$row -> category_id = $category_id;
					$row -> latitude = $latitude;
					$row -> longitude = $longitude;
					$row -> within = $within;
					$row -> email = $email;
					$row -> ip = $ipExpr;
					$row -> save();
					echo Zend_Json::encode(array('json' => 'true', 'message' => 'Subscribe listing successfully.'));
				}
				else
				{
					echo Zend_Json::encode(array('json' => 'false', 'message' => 'Your email has reached the limit of get alert emails.'));
				}
			}
			else
			{
				echo Zend_Json::encode(array('json' => 'false', 'message' => 'Your IP has reached the limit of get alert emails.'));
			}
		}
		else
		{
			echo Zend_Json::encode(array('json' => 'false', 'message' => 'Your alert email was in banned list.'));
		}
        return true;
	}
	
    public function getMyLocationAction() {
        $latitude = $this -> _getParam('latitude');
        $longitude = $this -> _getParam('longitude');
        $values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
        echo $values;
        die ;
    }
	
	public function manageAction() {
		if (!$this -> _helper -> requireUser -> isValid())
			return;
        $this->view->video_type = $video_type = Engine_Api::_()->ynmultilisting()->getMultilistingVideoType();
		$this -> _helper -> content	-> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_() -> getApi('settings', 'core')
        							-> getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this -> view -> timezone = $timezone;
		$this -> view -> viewer = $viewer;

        //INIT parameters
		$form = new Ynmultilisting_Form_Search(array(
			'type' => 'ynmultilisting_listing'
		));
        $params = $this -> _getAllParams();
		if ($form->isValid($params)) {
			$params = $form->getValues();
		} else {
			$params = array();
		}
		$params['user_id'] = $viewer -> getIdentity();
        $params['direction'] = 'DESC';
		$params['listingtype_id'] = Engine_Api::_() -> ynmultilisting() -> getCurrentListingTypeId();
        $this -> view -> formValues = $params;
        $p_arr = array();
        foreach ($params as $k => $v) {
            $p_arr[] = $k;
            $p_arr[] = $v;
        }
        $params_str = implode('/', $p_arr);
        $this -> view -> params_str = $params_str;
		$tableListing = Engine_Api::_() -> getItemTable('ynmultilisting_listing');
        $page = $params['page'];
        if (!$page){
            $page = 1;
        }
        $paginator = $tableListing -> getListingsPaginator($params);
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		$paginator -> setItemCountPerPage(10);
        $this -> view -> paginator = $paginator;
        $this -> view -> can_import = true;
        $this -> view -> can_export = true;

	}
	
	public function followAction()
	{
		// Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$owner_id = $this->_getParam('owner_id');
		$followTable = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');
		$row = $followTable -> getRow($viewer->getIdentity(), $owner_id);
		if($row)
		{
			if($this->_getParam('status') == 1)
			{
				$row -> status = 1;
				$row -> save();
				echo Zend_Json::encode(array('json' => 'true'));
        		return true;
			}
			else 
			{
				$row -> status = 0;
				$row -> save();
				 echo Zend_Json::encode(array('json' => 'false'));
       			 return true;
			}
		}
		else 
		{
			$new_row = $followTable -> createRow();
			$new_row -> user_id = $viewer->getIdentity();
			$new_row -> owner_id = $owner_id;
			$new_row -> status = 1; 
			$new_row -> save();
            $owner = Engine_Api::_()->getItem('user', $owner_id);
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($owner, $viewer, $owner, 'ynmultilisting_listing_follow_owner');
        	echo Zend_Json::encode(array('json' => 'true'));
        	return true;
		}
	}

	public function placeOrderAction() 
    {
    	$settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> listing = $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $this ->_getParam('id'));
		$this -> view -> package = $package = Engine_Api::_() -> getItem('ynmultilisting_package', $this ->_getParam('package_id'));
       	if(empty($package))
		{
			$this -> view -> package = $package =  new Ynmultilisting_Model_Package(array());
		}
       	$this -> view -> feature_day_number = $feature_day_number = $this ->_getParam('feature_day_number');
       	
		if($listing->user_id != $viewer->getIdentity())
        {
            $message = $this -> view -> translate('You do not have permission to do this.');
            return $this -> _redirector($message);
        }
        
        if (!($package -> getIdentity()) && !$feature_day_number) {
            $message = $this -> view -> translate('Please select package or set feature day.');
            return $this -> _redirector($message);
        }
		//check if feature listing
		if($feature_day_number)
		{
			//check auth
			if($feature_day_number <= 0)
			{
				$message = $this -> view -> translate('Invalid feature day.');
            	return $this -> _redirector($message);
			}
		}
		
		if($package -> getIdentity())
		{
			$package_id = $package -> getIdentity();
		}
		//Credit
        //check permission
        // Get level id
        $id = $viewer->level_id;
    	$action_type = "";
		
		//check auth pay with credit
        if ($listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'use_credit')) {
            $allowPayCredit = 0;
            $credit_enable = Engine_Api::_() -> hasModuleBootstrap('yncredit');
            if ($credit_enable)
            {
            	if($package -> getIdentity()){
					$action_type = 'publish_multilisting';
				}
				else {
					$action_type = 'feature_multilisting';
				}
                $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
                $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
				if($type_spend)
				{
					$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
					$select = $creditTbl->select()
		                ->where("level_id = ? ", $id)
		                ->where("type_id = ?", $type_spend -> type_id)
		                ->limit(1);
		            $spend_credit = $creditTbl->fetchRow($select);
					if($spend_credit)
					{
		               $allowPayCredit = 1;
		            }
				}
			}
            $this -> view -> allowPayCredit = $allowPayCredit;
        };
        
		$package_price = 0;
		$total_pay = 0;
		if($package -> getIdentity())
		{
			$package_price = $package -> price;
			$this -> view -> total_pay = $total_pay = $total_pay + $package_price;
		}
		if($feature_day_number)
		{
			$this -> view -> feature_fee = $feature_fee = $settings->getSetting('ynmultilisting_feature_fee', 0);
			$this -> view -> total_pay = $total_pay = $total_pay + $feature_day_number * $feature_fee;
		}
	   //if package free
	   if($total_pay == 0)
	   {
			//core - buyListing
			$db = Engine_Api::_()->getDbtable('listings', 'ynmultilisting')->getAdapter();
			$db->beginTransaction();
			try 
			{
				if($package -> getIdentity())
				{
					Engine_Api::_() -> ynmultilisting() -> buyListing($listing->getIdentity(), $package_id);
				}
				if($feature_day_number)
				{
					Engine_Api::_() -> ynmultilisting() -> featureListing($listing->getIdentity(), $feature_day_number);
				}
				Engine_Api::_() -> ynmultilisting() -> approveListing($listing->getIdentity());
				$db -> commit();
				
			} 
			catch (Exception $e) {
		      $db->rollBack();
		      throw $e;
		    }
		    
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'id' => $listing -> getIdentity(),
					'slug' => $listing -> getSlug(),
				), 'ynmultilisting_profile', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Success...'))
			 ));
		}  
	   
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit)) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
		
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynmultilisting');
		
        if ($row = $ordersTable -> getLastPendingOrder()) {
           $row -> delete();
        }
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
        try 
        {
        	if($package -> getIdentity())
			{
	            $ordersTable -> insert(array(
	            	'user_id' => $viewer -> getIdentity(), 
		            'creation_date' => new Zend_Db_Expr('NOW()'), 
		            'package_id' => $package_id, 
		            'item_id' => $listing -> getIdentity(),
		            'price' => $total_pay, 
		            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				));
			}
			if($feature_day_number) 
			{
				$order = $ordersTable -> getLastPendingOrder();
				if(!empty($order))
				{
					$order -> featured = true;
					$order -> feature_day_number = $feature_day_number;
					$order -> save();
				}
				else
				{
					$ordersTable -> insert(array(
		            	'user_id' => $viewer -> getIdentity(), 
			            'creation_date' => new Zend_Db_Expr('NOW()'), 
			            'featured' => true,
						'feature_day_number' => $feature_day_number,
			            'item_id' => $listing -> getIdentity(),
			            'price' => $total_pay, 
			            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
					));
				}
			}
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
		
        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
    }

    public function updateOrderAction() 
    {
        $type = $this ->_getParam('type');
        $id = $this ->_getParam('id');
        if(isset($type))
        {
            switch ($type) {
                
                case 'paycredit':
					$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynmultilisting');
					$order = $ordersTable -> getLastPendingOrder();
                    return $this -> _forward('success', 'utility', 'core', 
                        array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                        array(
	                        'action' => 'pay-credit', 
	                        'item_id' => $id,
							'order_id' => $order -> getIdentity()
						), 'ynmultilisting_general', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                default:
                    
                    break;
            }
        }

        $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $id);
            
        $gateway_id = $this -> _getParam('gateway_id', 0);
        if (!$gateway_id) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynmultilisting');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'ynmultilisting', 'cancel_route' => 'ynmultilisting_transaction', 'return_route' => 'ynmultilisting_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'transaction', 'action' => 'process', 'order_id' => $order -> getIdentity(), ), 'ynmultilisting_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
	public function payCreditAction()
    {
    	$credit_enable = Engine_Api::_() -> hasModuleBootstrap('yncredit');
        if (!$credit_enable)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
		$order = Engine_Api::_()->getItem('ynmultilisting_order', $this->_getParam('order_id'));
		if(!$order)
        {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
		$action_type = "";
		$featured = $order -> featured;
		$package_id = $order -> package_id;
		if($package_id)
		{
			$action_type = 'publish_multilisting';
		}
		else
		{
			$action_type = 'feature_multilisting';
		}
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
		$this-> view -> item = $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $item_id);
	    $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
		
        $cancel_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(
	          array(
	            'action' => 'place-order',
	            'id' => $item_id,
	            'packageId' => $order -> package_id
	          ), 'ynmultilisting_general', true);
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
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynmultilisting');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
			$description = "";
			$view = Zend_Registry::get('Zend_View');
			$package_price = 0;
			if($package_id)
			{
				Engine_Api::_() -> ynmultilisting() -> buyListing($listing->getIdentity(), $order -> package_id);
				$package = Engine_Api::_() -> getItem('ynmultilisting_package', $package_id);
				$package_price = $package -> price;
				$description = $this ->view ->translate('Buy Listing');
				/**
		         * Call Event from Affiliate
		         */
				if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))	
				{
					$params['module'] = 'ynmultilisting';
					$params['user_id'] = $order->user_id;
					$params['rule_name'] = 'publish_multilisting';
					$params['total_amount'] = $package_price;
					$params['currency'] = $order->currency;
		        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
				}
		        /**
		         * End Call Event from Affiliate
		         */
			}
			if($featured) 
			{
				Engine_Api::_() -> ynmultilisting() -> featureListing($listing->getIdentity(), $order -> feature_day_number);
				if(!empty($description))
				{
					$description .= " - ".$view ->translate('Feature Listing');
				}
				else
				{
					$description = $view ->translate('Feature Listing');
				}
				/**
		         * Call Event from Affiliate
		         */
				if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))	
				{
					$params['module'] = 'ynmultilisting';
					$params['user_id'] = $order->user_id;
					$params['rule_name'] = 'feature_multilisting';
					$params['total_amount'] = $order->price - $package_price;
					$params['currency'] = $order->currency;
		        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
				}
		        /**
		         * End Call Event from Affiliate
		         */
			}
			Engine_Api::_() -> ynmultilisting() -> approveListing($listing->getIdentity());
			
			//save transaction
	     	$transactionsTable->insert(array(
		     	'creation_date' => date("Y-m-d"),
		     	'status' => 'completed',
		     	'gateway_id' => '-3',
		     	'amount' => $order->price,
		     	'currency' => $order->currency,
		     	'user_id' => $order->user_id,
		     	'item_id' => $order->item_id,
		     	'description' => $description,
			 ));
			 
			 //send notification to admin
			 $admins = Engine_Api::_() -> user() -> getSuperAdmins();
			 foreach($admins as $admin)
			 {
			 	$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			 	$notifyApi -> addNotification($admin, $listing, $listing, 'ynmultilisting_listing_new_transaction');
			 }
			 
	        $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
        Engine_Api::_()->yncredit()-> spendCredits($viewer, (-1) * $credits, $viewer->getTitle(), $action_type, $viewer);
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id' => $listing->getIdentity(), 'slug' => $listing -> getSlug()), 'ynmultilisting_profile', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }

	protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynmultilisting_general', true), 'messages' => array($message)));
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
    
    //HOANGND export action
    public function exportAction() {
    	ob_start();
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> view -> form = $form = new Ynmultilisting_Form_Export();
        // Check method and data validity.
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $tableListings = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $select = $tableListings -> select() -> where('user_id = ?', $viewer->getIdentity()) -> where('deleted = ?', 0) -> where('listingtype_id = ?', Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId());
        $listings = $tableListings -> fetchAll($select);
        if(count($listings) == 0) {
            return;
        }
        //export to file
        $filename = "/tmp/csv-" . date( "m-d-Y" ) . ".csv";
        $realPath = realpath( $filename );
        if ( false === $realPath ) {
            touch( $filename );
            chmod( $filename, 0777 );
        }
        $filename = realpath( $filename );
        $handle = fopen( $filename, "w" );
        $finalData = array();
        foreach ( $listings as $item ) {
            
            //Populate Tag
            $tagStr = '';
            foreach ($item->tags()->getTagMaps() as $tagMap) {
                $tag = $tagMap -> getTag();
                if (!isset($tag -> text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag -> text;
            }
            
            $finalData[] = array(
                strip_tags($item -> title),
                $tagStr,
                $item -> short_description, 
                $item -> description,
                $item -> about_us,
                $item -> price,
                $item -> location,
                $item -> longitude,
                $item -> latitude,
                $item -> category_id,
                $item->getOwner()->email,
            );
        }
        $type_export = $this ->_getParam('type_export');
        if($type_export == 'xls') {
            //Export to xls file
            $xls = new Ynmultilisting_Api_ExcelExport('UTF-8', false, 'mylistings');
            $xls->addArray($finalData);
            $xls->generateXML('mylistings');
        }
        elseif($type_export == 'csv') {
            //Export to csv file
            foreach ( $finalData as $finalRow ) {
                fputcsv( $handle, $finalRow);
            }
            fclose($handle);
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $csvname = 'mylistings.csv';
            $this->getResponse()->setRawHeader( "Content-Type: application/csv; charset=UTF-8" )
                ->setRawHeader( "Content-Disposition: attachment; filename=".$csvname )
                ->setRawHeader( "Content-Transfer-Encoding: binary" )
                ->setRawHeader( "Expires: 0" )
                ->setRawHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0" )
                ->setRawHeader( "Pragma: public" )
                ->setRawHeader( "Content-Length: " . filesize( $filename ) )
                ->sendResponse();
            // fix for print out data
            readfile($filename);
            unlink($filename);
        }
        $this -> view -> status = TRUE;
        exit();
    }

    public function createAction()
    {
    	$this -> _helper -> content -> setEnabled();
    	// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		// Check authorization to post listing.
		$currentListingType = Engine_Api::_() -> ynmultilisting() -> getCurrentListingType();
	    if(!$currentListingType -> checkPermission(null, 'ynmultilisting_listing', 'create')) {
	    	return $this -> _helper -> requireAuth() -> forward();
	    }
		
        //check max listing user can create
        $viewer = Engine_Api::_()->user()->getViewer();
        $max = $currentListingType->getPermission(null, 'ynmultilisting_listing', 'max_listing');
        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $select = $table->select()-> where('user_id = ?', $viewer->getIdentity()) -> where('deleted = ?', '0') -> where('listingtype_id = ?', $currentListingType->getIdentity());
            
        $raw_data = $table->fetchAll($select);
        if (($max != 0) && (sizeof($raw_data) >= $max)) 
        {
        	$this -> view -> notCreateMore = true;
        }

		$table = Engine_Api::_() -> getItemTable('ynmultilisting_package');
		$select = $table -> select() 
		-> where('`show` = 1') 
		-> where('`deleted` = 0') 
		;
		
		$this -> view -> packages = $packages = $table -> fetchAll($select);
    }
   
    public function createStepTwoAction() 
    {
    	// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		// Check authorization to post listing.
		$currentListingType = Engine_Api::_() -> ynmultilisting() -> getCurrentListingType();
	    if(!$currentListingType -> checkPermission(null, 'ynmultilisting_listing', 'create')) {
	    	return $this -> _helper -> requireAuth() -> forward();
	    }
		
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
        //check max listings user can create
        $max = $currentListingType->getPermission(null, 'ynmultilisting_listing', 'max_listing');
        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $select = $table->select()-> where('user_id = ?', $viewer->getIdentity()) -> where('deleted = ?', '0') -> where('listingtype_id = ?', $currentListingType->getIdentity());
        $raw_data = $table->fetchAll($select);
        if (($max != 0) && (sizeof($raw_data) >= $max)) {
            echo ('Your listings are reach limit. Plese delete some listings for creating new.');
            $this -> _helper -> content -> setNorender();
            return;
        }
        
		//get package
		$package_id = $this ->_getParam('package_id');
		$package = Engine_Api::_() -> getItem('ynmultilisting_package', $package_id);
		
		if(!$package -> getIdentity())
		{
			$message = $this -> view -> translate('Please select package.');
            return $this -> _redirector($message);
		}
		$this -> view -> package = $package;
		$this -> view -> listingtype = $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
		
        
	    $tableCategory = Engine_Api::_()->getItemTable('ynmultilisting_category');
        
        // Check max of listings can be add.
        $table = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $count_listings = count($table->fetchAll($select));
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_listings_auth = $permissionsTable->getAllowed('ynmultilisting_listing', $viewer->level_id, 'max_listings');
        if ($max_listings_auth == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynmultilisting_listing')
                ->where('name = ?', 'max_listings'));
            if ($row) {
                $max_listings_auth = $row->value;
            }
        }
        $categories = $listingtype -> getCategories();
 		$firstCategory = $categories[1];
		$category_id = $this -> _getParam('category_id', $firstCategory->category_id);

		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynmultilisting_category', $category_id);
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynmultilisting_listing');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
		{
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array(
				'topLevelId' => $profileTypeField -> field_id,
				'topLevelValue' => $category -> option_id,
			);
		}
        
       	$this -> view -> form = $form = new Ynmultilisting_Form_Create( array(
				'category' => $category,
				'formArgs' => $formArgs,
       			'package' => $package,
		));
        
        
        //check max of listings can be add
        if ($max_listings_auth > 0 && $count_listings >= $max_listings_auth) {
            $this->view->error = true;
            $this->view->message = 'Number of your listings is maximum. Please delete some listings for creating new.';
            return;    
        }

		if(!Engine_Api::_()->hasItemType('video') && !Engine_Api::_()->hasItemType('ynultimatevideo_video'))
		{
			$form -> removeElement('upload_videos');
			$form -> removeElement('to');
		}
		// Populate category list.
		//$categories = Engine_Api::_() -> getItemTable('ynmultilisting_category') -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item)
		{
			$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
		}

		if (count($form -> category_id -> getMultiOptions()) < 1)
		{
			$form -> removeElement('category_id');
		}
		//populate category
		if($category_id)
		{
			$form -> category_id -> setValue($category_id);
		}
		else
		{
			$form->addError('Create listing require at least one category. Please contact admin for more details.');
		}
		//populate currency
		$supportedCurrencies = array();
		$gateways = array();
		$gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		foreach ($gatewaysTable->fetchAll(/*array('enabled = ?' => 1)*/) as $gateway)
		{
			$gateways[$gateway -> gateway_id] = $gateway -> title;
			$gatewayObject = $gateway -> getGateway();
			$currencies = $gatewayObject -> getSupportedCurrencies();
			if (empty($currencies))
			{
				continue;
			}
			$supportedCurrencyIndex[$gateway -> title] = $currencies;
			if (empty($fullySupportedCurrencies))
			{
				$fullySupportedCurrencies = $currencies;
			}
			else
			{
				$fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
			}
			$supportedCurrencies = array_merge($supportedCurrencies, $currencies);
		}
		$supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

		$translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
		$fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
		$supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
		
		$form -> getElement('currency') -> setMultiOptions(array(
			'Please select one' => array_merge($fullySupportedCurrencies, $supportedCurrencies)
		));
		$submit_button = $this -> _getParam('submit_button');
		$save_draft = $this -> _getParam('save_draft');
		if (!isset($submit_button))
		{
			if (!isset($save_draft))
				return;
		}
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		
		if (!$form -> isValid($posts))
		{
			return;
		}
		
		// Process
		$values = $form -> getValues();
		
		$values['location'] = $values['location_address'];
		$values['latitude'] = $values['lat'];
		$values['longitude'] = $values['long'];
		$values['user_id'] = $viewer -> getIdentity();

		$db = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Create listing
			$table = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting');
			$listing = $table -> createRow();
			$listing -> setFromArray($values);
			$listing -> status = 'draft';
			if (!empty($values['toValues'])) {
				$main_video = Engine_Api::_()->getItemByGuid($values['toValues']);
				$listing -> video_id = $main_video->getIdentity();
				$listing -> video_type = $main_video->getType();
			}
			$listing -> approved_status = 'pending';
			$listing -> listingtype_id = $listingtype -> getIdentity();
			$listing -> save();

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$listing -> tags() -> addTagMaps($viewer, $tags);

			$search_table = Engine_Api::_() -> getDbTable('search', 'core');
			$select = $search_table -> select() -> where('type = ?', 'ynmultilisting_listing') -> where('id = ?', $listing -> getIdentity());
			$row = $search_table -> fetchRow($select);
			if ($row)
			{
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			else
			{
				$row = $search_table -> createRow();
				$row -> type = 'ynmultilisting_listing';
				$row -> id = $listing -> getIdentity();
				$row -> title = $listing -> title;
				$row -> description = $listing -> description;
				$row -> keywords = $values['tags'];
				$row -> save();
			}

			// Set photo
			if (!empty($values['photo']))
			{
				$listing -> setPhoto($form -> photo);
			}
			//Set video
			if(!empty($values['toValues']))
			{
				$tableMappings = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
				$row = $tableMappings -> createRow();
				$toValues = explode('_',$values['toValues']);
				$item_id = end($toValues);
                $video_option = Engine_Api::_()->ynmultilisting()->getMultilistingVideoType();
			    $row -> setFromArray(array(
			       'listing_id' => $listing -> getIdentity(),
			       'item_id' => $item_id,
			       'user_id' => $viewer->getIdentity(),				       
			       'type' => 'profile_'.$video_option,
			       'creation_date' => date('Y-m-d H:i:s'),
			       'modified_date' => date('Y-m-d H:i:s'),
			       ));
			    $row -> save();
			}
			// Add fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($listing);
			$customfieldform -> saveValues();
			
			$db -> commit();

		    //set authorization
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment', 'share', 'photo', 'discussion');
            if(Engine_Api::_()->hasItemType('video')) {
                array_push($auth_arr, 'video'); 
            }
            
            foreach ($auth_arr as $elem) {
                $auth_elem = 'auth_'.$elem;
                $auth_role = $values[$auth_elem];
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
            {
                Engine_Api::_()->yncredit()-> hookCustomEarnCredits($listing -> getOwner(), $listing -> title, 'ynmultilisting_new', $listing);
			}
			
			 //send email
	        $params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
	        $params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST'];
	        $href =
	            'http://'. @$_SERVER['HTTP_HOST'].
	            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $listing -> getIdentity(), 'slug' => $listing -> getSlug()),'ynmultilisting_profile',true);
	        $params['listing_link'] = $href;
	        $params['listing_name'] = $listing -> getTitle();
	        try{
	            Engine_Api::_()->getApi('mail','ynmultilisting')->send($listing -> getOwner(), 'ynmultilisting_listing_created',$params);
	        }
	        catch(exception $e)
	        {
	            //keep silent
	        }
        }
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		if (isset($save_draft))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'id' => $listing -> getIdentity()
				), 'ynmultilisting_profile', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		if (isset($submit_button))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'place-order',
					'id' => $listing -> getIdentity(),
					'package_id' => $package_id,
					'feature_day_number' => $values['feature_day_number']
				), 'ynmultilisting_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
    }

    public function filterCategoryAction()
    {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $typeId = $this -> _getParam('type_id', 0);
        if ($typeId)
        {
            $type = Engine_Api::_()->getItem('ynmultilisting_listingtype', $typeId);
			$categories = $type -> getCategories();
			unset($categories[0]);
			$all = $this->view->translate('All');
			$str = "<option value='all'>{$all}</option>";
			foreach ($categories as $category) {
				$id = $category->getIdentity();
				$text = str_repeat("-- ", $category->level - 1).$category->getTitle();	
				$str .= "<option value='{$id}'>{$text}</option>";
			}
            echo $str; exit;
        }
        echo ''; exit;
    }

    public function displayMapViewAction()
    {
        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
	    $listingIds = $this->_getParam('ids', '');
	    if ($listingIds != '')
	    {
	    	$listingIds = explode("_", $listingIds);
	    }
	    $select = $table -> select();
	    
		if (is_array($listingIds) && count($listingIds))
		{
			$select -> where ("listing_id IN (?)", $listingIds);
		}
		else 
		{
			$select -> where ("0 = 1");
		}
		$listings = $table->fetchAll($select);
			
        $datas = array();
        $contents = array();
        $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ;
        $icon_new = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynmultilisting/externals/images/icon-New.png';

        foreach($listings as $listing) {
            if($listing -> latitude) {
                $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynmultilisting/externals/images/maker.png';

                if($listing->featured) {
                    $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynmultilisting/externals/images/feature_maker.png';
                }
                else
                {
                    if(!$listing->isNew()) {
                        $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynmultilisting/externals/images/old-maker.png';
                    }
                }
                $datas[] = array(
                    'listings_id' => $listing -> getIdentity(),
                    'latitude' => $listing -> latitude,
                    'longitude' => $listing -> longitude,
                    'icon' => $icon
                );
                if($listing->isNew())
                {
                    $new = "<img src='".$icon_new."' style='float: left; margin-right: 10px;'/>";
                }else{
                    $new = "";
                }
                $contents[] = '
                    <div class="ynmultilisting-maps-main" style="width: auto;">
                        <div class="ynmultilisting-maps-content" style="overflow: hidden; line-height: 20px;">
                            '.$new.'
                            <div style="overflow:hidden; float: left;">
                                <a href="'.$listing->getHref().'" class="ynmultilisting-maps-title" style="color: #679ac0; font-weight: bold; font-size: 14px; text-decoration: none; float: left; clear: both;" target="_parent">
                                    '.$listing->title.'
                                </a>
                            </div>
                        </div>
                    </div>
                ';
            }
        }

        echo $this ->view -> partial('_map_view.tpl', 'ynmultilisting',array('datas'=>Zend_Json::encode($datas), 'listings'=>$listings , 'contents' => Zend_Json::encode($contents)));
        exit();
    }
}
