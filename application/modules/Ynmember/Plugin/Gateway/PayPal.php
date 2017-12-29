<?php
class Ynmember_Plugin_Gateway_PayPal extends Payment_Plugin_Gateway_PayPal
{
  public function getGateway()
  {
    if( null === $this->_gateway ) {
      $class = 'Engine_Payment_Gateway_PayPal';
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
    if( $order -> gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }

    /**
     * Get related info
     *
     * @var $view Zend_View
     */

    $view = Zend_Registry::get('Zend_View');
    $currency = $this->getGateway()->getCurrency();
    $payment_requests = array();
    $payment_item = array();

    $payment_item = array_merge($payment_item, array(
      'L_PAYMENTREQUEST_0_NAME0' => $view->translate('Feature Member'),
      'L_PAYMENTREQUEST_0_DESC0' => $view->translate('Feature Member from %s', $view->layout()->siteinfo['title']),
      'L_PAYMENTREQUEST_0_AMT0' => $order->price
    ));

    $seller = $this->_gatewayInfo->config;

    $payment_requests = array_merge($payment_requests, array_merge(array(
      'PAYMENTREQUEST_0_AMT' => $order->price,
      'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID' => $seller,
      'PAYMENTREQUEST_0_DESC' => $view->translate('Feature Member'),
      'PAYMENTREQUEST_0_PAYMENTACTION' => 'order',
      'PAYMENTREQUEST_0_PAYMENTREQUESTID' => $order->getIdentity(),
      'PAYMENTREQUEST_0_INVNUM' => $params['order_id'],
      'PAYMENTREQUEST_0_CURRENCYCODE' => $currency,
      'PAYMENTREQUEST_0_NOTIFYURL' => $params['ipn_url'],
    ), $payment_item));

 		$params['driverSpecificParams']['PayPal'] = $payment_requests;
    $transaction = $this->createTransaction($params);
 		return $transaction;
 	}

  /**
   * Create a transaction object from specified parameters
   *
   * @return Engine_Payment_Transaction
   */
  public function createTransaction(array $params)
  {
    $transaction = new Engine_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }

  public function onPackageTransactionReturn(Ynmember_Model_Order $order, array $params = array())
  {
 	 $viewer = Engine_Api::_()->user()->getViewer();
	 $view = Zend_Registry::get('Zend_View');
     // Check that gateways match
     if( $order ->gateway_id != $this->_gatewayInfo->gateway_id ) {
       throw new Engine_Payment_Plugin_Exception('Gateways do not match');
     }

     //Get created orders
     if (!$order->isOrderPending()) {
       throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
     }

     // Check for cancel state - the user cancelled the transaction
     if( $params['state'] == 'cancel' ) {
       $order->onCancel();
       // Error
       throw new Payment_Model_Exception('Your payment has been cancelled and ' .
         'not been purchased. If this is not correct, please try again later.');
     }

     // Check params
     if ( empty($params['token']) ) {
       $order->onPaymentFailure();
       // This is a sanity error and cannot produce information a user could use
       // to correct the problem.
       throw new Payment_Model_Exception('There was an error processing your ' .
         'transaction. Please try again later.');
     }

     // Get details
     try {
       $data = $this->getService()->detailExpressCheckout($params['token']);
     } catch( Exception $e ) {
       // Cancel order and subscription?
       $order->onPaymentFailure();
       // This is a sanity error and cannot produce information a user could use
       // to correct the problem.
       throw new Payment_Model_Exception('There was an error processing your ' .
           'transaction. Please try again later.');
     }

     // Let's log it
     $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
         . print_r($data, true), Zend_Log::INFO);

     //Prepare variables
     $new_requests = array();

     $request = $data['PAYMENTREQUEST'][0];

     if ( null == ($order = Engine_Api::_()->getDbTable('orders', 'ynmember') -> findRow($request['PAYMENTREQUESTID'])) ) {
       $order->onPaymentFailure();

       throw new Payment_Model_Exception('CREDIT_No orders found');
     }

     if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
       $order->onPaymentFailure();

       throw new Payment_Model_Exception('Gateways do not match');
     }

     // Check order states
     if ($order->status == 'completed') {
       return 'completed';
     }

     $new_requests = array_merge($new_requests, array(
       'PAYMENTREQUEST_0_AMT' => $request['AMT'],
       'PAYMENTREQUEST_0_PAYMENTACTION' => 'sale',
       'PAYMENTREQUEST_0_PAYMENTREQUESTID' => $order->getIdentity(),
       'PAYMENTREQUEST_0_CURRENCYCODE' => $request['CURRENCYCODE'],
     ));

     try {
       $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], $new_requests);
     } catch( Exception $e ) {
       // Log the error
       $this->getGateway()->getLog()->log('DoExpressCheckoutPaymentError: '
           . $e->__toString(), Zend_Log::ERR);

       // Cancel order and subscription?
       $order->onPaymentFailure();
       // This is a sanity error and cannot produce information a user could use
       // to correct the problem.
       throw new Payment_Model_Exception('There was an error processing your ' .
           'transaction. Please try again later.');
     }

	 // Let's log it
	 $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
			. print_r($rdata, true), Zend_Log::INFO);

     $info = $rdata['PAYMENTINFO'][0];
     $order = Engine_Api::_()->getDbTable('orders', 'ynmember') -> findRow($info['PAYMENTREQUESTID']);
     $this->getGateway()->getLog()->log('TJError: '
      				. print_r($order->toArray(), true), Zend_Log::INFO);
     // Get payment state
     $paymentStatus = null;
     $orderStatus = null;

     switch( strtolower($info['PAYMENTSTATUS']) ) {
       case 'created':
       case 'pending':
         $paymentStatus = 'pending';
         $orderStatus = 'complete';
         break;

       case 'completed':
       case 'processed':
       case 'canceled_reversal': // Probably doesn't apply
         $paymentStatus = 'okay';
         $orderStatus = 'complete';
         break;

       case 'denied':
       case 'failed':
       case 'voided': // Probably doesn't apply
       case 'reversed': // Probably doesn't apply
       case 'refunded': // Probably doesn't apply
       case 'expired':  // Probably doesn't apply
       default: // No idea what's going on here
         $paymentStatus = 'failed';
         $orderStatus = 'failed'; // This should probably be 'failed'
         break;
     }
	 
     // Update order with profile info and complete status?
     $order->status = $orderStatus;
     $order->gateway_transaction_id = $info['TRANSACTIONID'];
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
     	'currency' => $info['CURRENCYCODE'],
     	'user_id' => $order->user_id,
     	'creation_date' => date("Y-m-d"),
     	'payment_transaction_id' => $info['TRANSACTIONID'],
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
	    'state' => $paymentStatus,
	    'gateway_transaction_id' => $info['TRANSACTIONID'],
	    'amount' => $order->price, // @todo use this or gross (-fee)?
	    'currency' => $info['CURRENCYCODE'],
	  ));
		
       $db->commit();
     } catch( Exception $e) {
       $db->rollBack();
       throw $e;
     }
	 

     // Check payment status
     if( $paymentStatus == 'okay' ) {
       // Payment success
       $order->onPaymentSuccess();
       $status = 'completed';
     }
     else if( $paymentStatus == 'pending' ) {

       // Payment pending
       $order->onPaymentPending();
       $status = 'pending';
     }
     else if( $paymentStatus == 'failed' ) {
       // Cancel order and subscription?
       $order->onPaymentFailure();

       if ($order instanceof Ynmember_Model_Order) {
         $order->onPaymentFailure();
         throw new Payment_Model_Exception('There was an error processing your ' .
                             'transaction. Please try again later.');
       }

       $order->onPaymentFailure();
       // Payment failed
       throw new Payment_Model_Exception('Your payment could not be ' .
                    'completed. Please ensure there are sufficient available funds ' .
                    'in your account.');
     }
     else {
       throw new Payment_Model_Exception('There was an error processing your ' .
                    'transaction. Please try again later.');
     }

     return $status;
 	}
}