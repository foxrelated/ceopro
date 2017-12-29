<?php
class Ynmember_Model_Order extends Core_Model_Item_Abstract
{
	protected $_type = 'ynmember_order';
	protected $_statusChanged;
	public function isOrderPending()
	{
		return ($this -> status == 'pending') ? true : false;
	}
	
	public function isOneTime()
	{
		return true;
	}
	
	public function getSource()
	{
		return $this;
	}
	
	public function getPackageParams($arr = array())
    {
        $params =array();
        $view = Zend_Registry::get('Zend_View');
        // General
        $params['name'] = $view -> translate('Feature Member');
        $params['price'] = $arr['price'];
        $params['description'] = $view -> translate('Feature Member from %s', $view -> layout() -> siteinfo['title']);
        $params['vendor_product_id'] = $this -> getGatewayIdentity($arr['user_id'], $arr['price']);
        $params['tangible'] = false;
        $params['recurring'] = false;
        return $params;
    }
    
    public function getGatewayIdentity($user_id = 0, $fee = 100)
    {
        return 'ynmember_' . $user_id . '_fee_' . $fee;
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
		$table = Engine_Api::_() -> getDbTable('transactions', 'ynmember');
		$select = $table -> select() -> setIntegrityCheck(false) -> from($table -> info('name'), 'transaction_id') -> where('gateway_transaction_id = ?', $this -> gateway_transaction_id) -> where('state = ?', 'okay');

		return (bool)$table -> fetchRow($select);
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
		$view = Zend_Registry::get('Zend_View');
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

		// Insert member transaction
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynmember');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
	     	//active feature
	     	$featureTable =  Engine_Api::_() -> getItemTable('ynmember_feature');
			$featureRow  = $featureTable -> getFeatureRowByUserId($this->user_id);
			
			if($this->number_day == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$now =  date("Y-m-d H:i:s");
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($this->number_day." ".$type));
			if(!empty($featureRow)) //used to feature member
			{
				if($featureRow -> active == 1)
				{
					$expiration_date = date_add(date_create($featureRow->expiration_date),date_interval_create_from_date_string($this->number_day." ".$type));
				}
				$featureRow -> modified_date = $now;
				$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$featureRow -> active = 1;
				$featureRow -> save();  
			}
			else //first time
			{
				$featureRow = $featureTable -> createRow();
				$featureRow -> user_id = $this->user_id;
				$featureRow -> creation_date = $now;
				$featureRow -> modified_date = $now;
				$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$featureRow -> active = 1;
				$featureRow -> save();  
			}
			$description = $view->translate(array('Feature in %s day', 'Feature in %s days', $this -> number_day), $this -> number_day);
			
			/**
	         * Call Event from Affiliate
	         */
			if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))	
			{
				$params['module'] = 'ynmember';
				$params['user_id'] = $this->user_id;
				$params['rule_name'] = 'feature_member';
				$params['total_amount'] = $this->price;
				$params['currency'] = $this->currency;
	        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
			}
	        /**
	         * End Call Event from Affiliate
	         */
			
			//save transaction
	     	$transactionsTable->insert(array(
	     	'creation_date' => date("Y-m-d"),
	     	'status' => 'completed',
	     	'gateway_id' => $this -> gateway_id,
	     	'amount' => $this->price,
	     	'currency' => $this->currency,
	     	'user_id' => $this->user_id,
	     	'payment_transaction_id' => $params['transaction_id'],
	     	'description' => $description,
		  ));
	      $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
		 
		// Update order with profile info and complete status?
		$this -> gateway_transaction_id = $params['transaction_id'];
		$this -> save();

		// Insert transaction
		$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
		$transactionsTable -> insert(array(
			'user_id' => $this -> user_id,
			'gateway_id' => $this -> gateway_id,
			'timestamp' => new Zend_Db_Expr('NOW()'),
			'order_id' => $this -> order_id,
			'type' => 'Feature Member',
			'state' => 'okay',
			'gateway_transaction_id' => $params['transaction_id'],
			'amount' => (isset($params['amount'])?$params['amount']:$this -> price), // @todo use this or gross (-fee)?
			'currency' => $params['currency']
		));
		$this -> onPaymentSuccess();
		return 'completed';
	}
}
