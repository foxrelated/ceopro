<?php
class Ynmultilisting_Plugin_Gateway_2Checkout extends Payment_Plugin_Gateway_2Checkout
{
	
  public function getGateway()
  {
    if( null === $this->_gateway ) 
    {
      $class = 'Engine_Payment_Gateway_2Checkout';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
        'config' => (array) $this->_gatewayInfo->config,
        'testMode' => $this->_gatewayInfo->test_mode,
        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
      ));
      if( !($gateway instanceof Engine_Payment_Gateway) ) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }
    return $this->_gateway;
  }

  public function createPackageTransaction(Ynmultilisting_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    //Get unique orders
    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }
    $gatewayPlugin = $this->_gatewayInfo->getGateway();
    if ($this->_gatewayInfo->enabled &&
      method_exists($gatewayPlugin, 'createProduct') &&
      method_exists($gatewayPlugin, 'editProduct') &&
      method_exists($gatewayPlugin, 'detailVendorProduct')
    ) {
      // If it throws an exception, or returns empty, assume it doesn't exist?
      try {
        $info = $gatewayPlugin->detailVendorProduct($order->getGatewayIdentity($order->user_id, $order->price));
      } catch (Exception $e) {
        $info = false;
      }
      // Create
      if (!$info) {
      	$arr['user_id'] = $order->user_id;
		$arr['price'] = $order->price; 
        $gatewayPlugin->createProduct($order->getPackageParams($arr));
      }
    }
    // Do stuff to params
    $params['fixed'] = true;
    $params['skip_landing'] = true;

    // Lookup product id for this subscription
    $productInfo = $this->getService()->detailVendorProduct($order->getGatewayIdentity($order->user_id, $order->price));
    $params['product_id'] = $productInfo['product_id'];
    $params['quantity'] = 1;
    // Create transaction
    $transaction = $this->createTransaction($params);
    return $transaction;
  }

  public function onPackageTransactionReturn(Ynmultilisting_Model_Order $order, array $params = array())
  {
  	$viewer = Engine_Api::_()->user()->getViewer();
	$view = Zend_Registry::get('Zend_View');
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    //Get created orders
    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }

    $user = $order->getUser();

    // Check order states
    if ($order->status == 'completed') {
      return 'completed';
    }
	
	// Let's log it
    $this->getGateway()->getLog()->log('Return: '
      . print_r($params, true), Zend_Log::INFO);
	
    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') 
    {
      $order->onCancel();
      // Error
      throw new Payment_Model_Exception('Your payment has been cancelled and ' .
        'not been purchased. If this is not correct, please try again later.');
    }
    // Check for processed
    if (empty($params['credit_card_processed'])) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
	
    // Ensure product ids match
    if ($params['merchant_product_id'] != $order->getGatewayIdentity($order->user_id, $order->price)) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure order ids match
    if ($params['order_id'] != $order->order_id &&
      $params['merchant_order_id'] != $order->order_id
    ) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure vendor ids match
    if ($params['sid'] != $this->getGateway()->getVendorIdentity()) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Validate return
    try {
      $this->getGateway()->validateReturn($params);
    } catch (Exception $e) {
      /*if (!$this->getGateway()->getTestMode()) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
      } else {
        echo $e; // For test mode
      }*/
    }
    // Update order with profile info and complete status?
    $order->gateway_transaction_id = $params['order_number'];
    $order->save();
	
	$featured = $order -> featured;
	$package_id = $order -> package_id;
	
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
			Engine_Api::_() -> ynmultilisting() -> buyListing($order -> item_id, $order -> package_id);
			$description = $view ->translate('Buy Listing');
			$package = Engine_Api::_() -> getItem('ynmultilisting_package', $package_id);
			$package_price = $package -> price;
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
			Engine_Api::_() -> ynmultilisting() -> featureListing($order -> item_id, $order -> feature_day_number);
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
		Engine_Api::_() -> ynmultilisting() -> approveListing($order -> item_id);
		
		//save transaction
     	$transactionsTable->insert(array(
	     	'creation_date' => date("Y-m-d"),
	     	'status' => 'completed',
	     	'gateway_id' => $this->_gatewayInfo->gateway_id,
	     	'amount' => $order->price,
	     	'currency' => $order->currency,
	     	'user_id' => $order->user_id,
	     	'item_id' => $order->item_id,
	     	'payment_transaction_id' => $params['order_number'],
	     	'description' => $description,
		 ));
		 
		  //send notification to admin
		 $admins = Engine_Api::_() -> user() -> getSuperAdmins();
		 $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $order->item_id);
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
	
	// Insert transaction
	 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
     $db = $transactionsTable->getAdapter();
     $db->beginTransaction();

     try {
     	$transactionsTable->insert(array(
	    'user_id' => $order->user_id,
	    'gateway_id' => $this->_gatewayInfo->gateway_id,
	    'timestamp' => new Zend_Db_Expr('NOW()'),
	    'order_id' => $order->getIdentity(),
	    'type' => 'Multiple Listings',
	    'state' => 'okay', 
	    'gateway_transaction_id' => $params['order_number'],
	    'amount' => $order->price, // @todo use this or gross (-fee)?
	    'currency' => $params['currency_code'],
	  ));
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
	
    $order->onPaymentSuccess();
    return 'completed';
  }
}
