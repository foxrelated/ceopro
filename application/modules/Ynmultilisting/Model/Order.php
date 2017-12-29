<?php
class Ynmultilisting_Model_Order extends Core_Model_Item_Abstract
{
	protected $_searchTriggers = false;
	protected $_type = 'ynmultilisting_order';
	protected $_statusChanged;
	
	public function getPackageParams($arr = array())
    {
        $params =array();
        $view = Zend_Registry::get('Zend_View');
        // General
        $params['name'] = $view -> translate('Buy Listing');
        $params['price'] = $arr['price'];
        $params['description'] = $view -> translate('Buy Listing from %s', $view -> layout() -> siteinfo['title']);
        $params['vendor_product_id'] = $this -> getGatewayIdentity($arr['user_id'], $arr['price']);
        $params['tangible'] = false;
        $params['recurring'] = false;
        return $params;
    }
    
    public function getGatewayIdentity($user_id = 0, $fee = 100)
    {
        return 'ynmultilisting_' . $user_id . '_fee_' . $fee;
    }
	
	public function isOrderPending()
	{
		return ($this -> status == 'pending') ? true : false;
	}

	public function onPaymentPending()
	{
		$this -> _statusChanged = false;
		if (in_array($this -> status, array(
			'initial',
			'pending'
		)))
		{
			// Change status
			if ($this -> status != 'pending')
			{
				$this -> status = 'pending';
				$this -> _statusChanged = true;
			}
		}
		$this -> save();
		return $this;
	}

	public function onPaymentSuccess()
	{
		$this -> _statusChanged = false;
		$buyer = Engine_Api::_() -> getItem('user', $this -> user_id);

		// Change status
		if ($this -> status != 'completed')
		{
			$this -> status = 'completed';
			$this -> payment_date = new Zend_Db_Expr('NOW()');
			$this -> _statusChanged = true;
		}
		$this -> save();

		return $this;
	}

	public function onPaymentFailure()
	{
		$this -> _statusChanged = false;

		// Change status
		if ($this -> status != 'failed')
		{
			$this -> status = 'failed';
			$this -> payment_date = new Zend_Db_Expr('NOW()');
			$this -> _statusChanged = true;
		}
		$this -> save();

		return $this;
	}

	public function didStatusChange()
	{
		return $this -> _statusChanged;
	}

	public function cancel()
	{
		$this -> active = false;
		// Need to do this to prevent clearing the user's session
		$this -> onCancel();
		return $this;
	}

	public function onCancel()
	{
		$this -> _statusChanged = false;
		if (in_array($this -> status, array(
			'pending',
			'cancelled'
		)))
		{
			// Change status
			if ($this -> status != 'cancelled')
			{
				$this -> status = 'cancelled';
				$this -> _statusChanged = true;
			}
		}
		$this -> save();
		return $this;
	}

	public function isChecked()
	{
		if ($this -> status != 'completed')
			return false;
		$table = Engine_Api::_() -> getDbTable('transactions', 'ynmultilisting');
		$select = $table -> select() -> setIntegrityCheck(false) -> from($table -> info('name'), 'transaction_id') -> where('gateway_transaction_id = ?', $this -> gateway_transaction_id) -> where('state = ?', 'okay');

		return (bool)$table -> fetchRow($select);
	}

	public function getSource()
	{
		if($this->package_id != 0)
		{
			$table = Engine_Api::_() -> getDbTable('packages', 'ynmultilisting');
			$select = $table -> select() -> where('package_id = ?', $this -> package_id) -> limit(1);
			$row = $table -> fetchRow($select);
			return $row;
		}
		else
		{
			return $this;
		}
	}

	public function getUser()
	{
		return Engine_Api::_() -> getItem('user', $this -> user_id);
	}

	public function getGatewayTitle()
	{
		$gatewaysTable = Engine_Api::_() -> getDbTable('gateways', 'payment');
		$select = $gatewaysTable -> select() -> where('gateway_id = ?', $this -> gateway_id) -> limit(1);
		return $gatewaysTable -> fetchRow($select) -> title;
	}

	public function onPackageTransactionReturn(array $params = array())
	{
		// Get related info
		$user = $this -> getUser();
		$item = $this -> getSource();

		// Check order states
		if ($this -> status == 'completed')
		{
			return 'completed';
		}

		// Check for cancel state - the user cancelled the transaction
		if (isset($params['state']) && $params['state'] == 'cancel')
		{
			$this -> onCancel();
			// Error
			throw new Payment_Model_Exception('Your payment has been cancelled and ' . 'not been purchased. If this is not correct, please try again later.');
		}
		
		 $featured = $this -> featured;
		 $package_id = $this -> package_id;
		
		 // Insert transaction
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynmultilisting');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
	     	$description = "";
			$view = Zend_Registry::get('Zend_View');
			$package_price = 0;
			if($package_id)
			{
				Engine_Api::_() -> ynmultilisting() -> buyListing($this->item_id, $this -> package_id);
				$description = $view ->translate('Buy listing');
				$package = Engine_Api::_() -> getItem('ynmultilisting_package', $package_id);
				$package_price = $package -> price;
				/**
		         * Call Event from Affiliate
		         */
				if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))	
				{
					$params['module'] = 'ynmultilisting';
					$params['user_id'] = $this->user_id;
					$params['rule_name'] = 'publish_multilisting';
					$params['total_amount'] = $package_price;
					$params['currency'] = $this->currency;
		        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
				}
		        /**
		         * End Call Event from Affiliate
		         */
			}
			if($featured)
			{
				Engine_Api::_() -> ynmultilisting() -> featureListing($this->item_id, $this -> feature_day_number);
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
					$params['user_id'] = $this->user_id;
					$params['rule_name'] = 'feature_multilisting';
					$params['total_amount'] = $this->price - $package_price;
					$params['currency'] = $this->currency;
		        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
				}
		        /**
		         * End Call Event from Affiliate
		         */
			}
			Engine_Api::_() -> ynmultilisting() -> approveListing($this->item_id);
			
			//save transaction
	     	$transactionsTable->insert(array(
		     	'creation_date' => date("Y-m-d"),
		     	'status' => 'completed',
		     	'gateway_id' => $this -> gateway_id,
		     	'amount' => $this->price,
		     	'currency' => $this->currency,
		     	'user_id' => $this->user_id,
		     	'item_id' => $this->item_id,
		     	'payment_transaction_id' => $params['transaction_id'],
		     	'description' => $description,
			 ));
			 
			 //send notification to admin
			 $admins = Engine_Api::_() -> user() -> getSuperAdmins();
			 $listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $this->item_id);
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
		$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
		$transactionsTable -> insert(array(
			'user_id' => $this -> user_id,
			'gateway_id' => $this -> gateway_id,
			'timestamp' => new Zend_Db_Expr('NOW()'),
			'order_id' => $this -> order_id,
			'type' => 'Multiple Listings',
			'state' => 'okay',
			'gateway_transaction_id' => $params['transaction_id'],
			'amount' => (isset($params['amount'])?$params['amount']:$this -> price), // @todo use this or gross (-fee)?
			'currency' => $params['currency']
		));
		$this -> onPaymentSuccess();
		return 'completed';
	}
}
