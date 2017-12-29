<?php
class Ynmember_Plugin_Gateway_2Checkout extends Payment_Plugin_Gateway_2Checkout
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

  public function createPackageTransaction(Ynmember_Model_Order $order, array $params = array())
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

  public function onPackageTransactionReturn(Ynmember_Model_Order $order, array $params = array())
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
			$featureRow -> user_id = $viewer -> getIdentity();
			$featureRow -> creation_date = $now;
			$featureRow -> modified_date = $now;
			$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			$featureRow -> active = 1;
			$featureRow -> save();  
		}
		$description = $view->translate(array('Feature in %s day', 'Feature in %s days', $order -> number_day), $order -> number_day);
		
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
     	'status' => 'completed',
     	'gateway_id' => $this->_gatewayInfo->gateway_id,
     	'amount' => $order->price,
     	'currency' => $params['currency_code'],
     	'user_id' => $order->user_id,
     	'creation_date' => date("Y-m-d"),
     	'payment_transaction_id' => $params['order_number'],
     	'description' => $description,
	  ));
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
	    'type' => 'Feature Member',
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
