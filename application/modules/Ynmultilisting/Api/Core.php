<?php
class Ynmultilisting_Api_Core extends  Core_Api_Abstract {
	
	public function generateSubsribeEmail($toSendListingIds, $email)
	{
		$html = "";
		$view = Zend_Registry::get('Zend_View');
		$html =  $view -> partial('_subscribe_listing.tpl', 'ynmultilisting', array(
			'toSendListingIds' => $toSendListingIds,
			'email' => $email,
		));
		if(!empty($toSendListingIds))
			return $html;
		else {
			return null;
		}
	}

    public function getUsersByName($name)
    {
        $tableUser = Engine_Api::_() -> getItemTable('user');
        $select = $tableUser -> select();
        $select -> where('displayname LIKE ?', '%'.$name.'%');
        $result = $tableUser -> fetchAll($select);
        return $result;
    }

	public function sendSubsribeMail()
	{
		$tableSubscriber = Engine_Api::_() -> getDbTable('subscribers', 'ynmultilisting');
		$tableListing = Engine_Api::_() -> getItemTable('ynmultilisting_listing');
		$list_emails = $tableSubscriber -> getEmails();
		foreach ($list_emails as $email_row)
		{
			$rows = $tableSubscriber -> getRowsByEmail($email_row -> email);
			$listing_listingId = array();
			foreach($rows as $record)
			{
				if($record['category_id'] == 0)
				{
					$params['category_id'] = 'all';
				}
				else
				{
					$params['category_id'] = $record['category_id'];
				}
				$params['lat'] = $record['latitude'];
				$params['long'] = $record['longitude'];
				$params['within'] = $record['within'];
                if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi') == 'km')
                    $params['within'] *= Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.mi.to.km', 0.621371);
				$params['status'] = 'open';
				$params['approved_status'] = 'approved';
				$list_listings = $tableListing -> fetchAll($tableListing -> getListingsSelect($params));
				foreach($list_listings as $listing)
				{
					if(!in_array($listing -> getIdentity(), $listing_listingId))
					{
						array_push($listing_listingId, $listing->getIdentity());
					}
				}
			}
			//check which job is already sent
			$tableSentListing = Engine_Api::_() -> getDbTable('sentlistings', 'ynmultilisting');
			$listSentListing = $tableSentListing -> getListingIdsByEmail($email_row -> email);
			$toSendListingIds = array_diff($listing_listingId, $listSentListing);
			//send mail
			$html = Engine_Api::_() -> ynmultilisting() -> generateSubsribeEmail($toSendListingIds, $email_row -> email);
			$sendTo = $email_row -> email;
		  	$params = array();
			if(!empty($html))
			{
				foreach($toSendListingIds as $toSendListingId)
				{
					$sentJobRow = $tableSentListing -> createRow();
					$sentJobRow -> listing_id = $toSendListingId;
					$sentJobRow -> email = $email_row -> email;
					$sentJobRow -> save();
				}
				Engine_Api::_()->getApi('mail','ynmultilisting')->send($sendTo, 'ynmultilisting_subscribe_listing',$params, $html);
			}
		}
	}
	
	public function typeCreate($label) {
		$field = Engine_Api::_() -> fields() -> getField('1', 'ynmultilisting_listing');
		// Create new blank option
		$option = Engine_Api::_() -> fields() -> createOption('ynmultilisting_listing', $field, array('field_id' => $field -> field_id, 'label' => $label, ));
		// Get data
		$mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynmultilisting_listing');
		$metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynmultilisting_listing');
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynmultilisting_listing');
		// Flush cache
		$mapData -> getTable() -> flushCache();
		$metaData -> getTable() -> flushCache();
		$optionData -> getTable() -> flushCache();

		return $option -> option_id;
	}
	
	public function countVideoByListing($listing, $isProfile = null){
		if(empty($isProfile)){
			$isProfile = false;
		}
		$tableMapping = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
		$name = $tableMapping->info('name');
   		$select = $tableMapping->select()
                    ->from($name, 'COUNT(*) AS count');
		$select -> where('listing_id =?', $listing -> getIdentity());
		if($isProfile){
			$select -> where('type = ?', 'profile_video');
		}else{
			$select -> where('type = ?', 'video');
		}
    	return $select->query()->fetchColumn(0);
	}

    public function countUltimateVideoByListing($listing, $isProfile = null){
        if(empty($isProfile)){
            $isProfile = false;
        }
        $tableMapping = Engine_Api::_() -> getDbTable('mappings', 'ynmultilisting');
        $name = $tableMapping->info('name');
        $select = $tableMapping->select()
            ->from($name, 'COUNT(*) AS count');
        $select -> where('listing_id =?', $listing -> getIdentity());
        if($isProfile){
            $select -> where('type = ?', 'profile_ynultimatevideo_video');
        }else{
            $select -> where('type = ?', 'ynultimatevideo_video');
        }
        return $select->query()->fetchColumn(0);
    }

	public function checkIsEditor($listingTypeID, $user = null)
	{
		$editorTbl = Engine_Api::_() -> getDbTable('editors', 'ynmultilisting');
		return $editorTbl -> checkIsEditor($listingTypeID, $user);
	}
	
	public function getGateway($gateway_id)
	{
		return $this -> getPlugin($gateway_id) -> getGateway();
	}
	
	public function getPlugin($gateway_id)
	{
		if (null === $this -> _plugin)
		{
			if (null == ($gateway = Engine_Api::_() -> getItem('payment_gateway', $gateway_id)))
			{
				return null;
			}
			Engine_Loader::loadClass($gateway -> plugin);
			if (!class_exists($gateway -> plugin))
			{
				return null;
			}
			$class = str_replace('Payment', 'Ynmultilisting', $gateway -> plugin);

			Engine_Loader::loadClass($class);
			if (!class_exists($class))
			{
				return null;
			}

			$plugin = new $class($gateway);
			if (!($plugin instanceof Engine_Payment_Plugin_Abstract))
			{
				throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
			}
			$this -> _plugin = $plugin;
		}
		return $this -> _plugin;
	}
	
	public function approveListing($listing_id)
	{
		$listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $listing_id);
		$now =  date("Y-m-d H:i:s");
		//get feature
		$featureTable = Engine_Api::_() -> getDbTable('features', 'ynmultilisting');
		$featureRow = $featureTable -> getFeatureRowByListingId($listing_id);
		
		//check autoapproved
		if($listing -> getListingType() -> checkPermission(null, 'ynmultilisting_listing', 'auto_approve'))
		{
			//get package
			$package = Engine_Api::_() -> getItem('ynmultilisting_package', $listing -> package_id);
			if($package -> getIdentity())
			{
				if($package->valid_amount == 1)
				{
					$type = 'day';
				}
				else 
				{
					$type = 'days';
				}
				$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($package->valid_amount." ".$type));
				$listing -> approved_date = $now;
				$listing -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			}	
			
			//get feature
			$featureTable = Engine_Api::_() -> getDbTable('features', 'ynmultilisting');
			$featureRow = $featureTable -> getFeatureRowByListingId($listing_id);
			if($featureRow)
			{
				//check if feature not approved
				if($featureRow -> active == 0)
				{
					
					if($featureRow -> feature_day_number == 1)
					{
						$type = 'day';
					}
					else 
					{
						$type = 'days';
					}
					$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($featureRow -> feature_day_number." ".$type));
					
					$featureRow -> active = 1;
					$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");;
					$featureRow -> feature_day_number = 0;
					$featureRow -> modified_date = $now;
					$featureRow -> save();
					
					$listing -> feature_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");;
					$listing -> featured = 1;
					
					$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
					$notifyApi -> addNotification($listing -> getOwner(), $listing, $listing, 'ynmultilisting_listing_status_change', array('status' => 'featured'));
				}
			}
	
			if(!$listing -> approved)
			{
				//add activity
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($listing -> getOwner(), $listing, 'ynmultilisting_listing_create');
				if($action) {
					$activityApi->attachActivity($action, $listing);
				}
				
				//send notification to follower
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$owner = $listing -> getOwner();
				// get follower
				$tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');
				$select = $tableFollow -> select() -> where('owner_id = ?', $owner -> getIdentity()) -> where('status = 1');
				$follower = $tableFollow -> fetchAll($select);
				foreach($follower as $row)
				{
					$person = Engine_Api::_()->getItem('user', $row -> user_id);
					$notifyApi -> addNotification($person, $owner, $listing, 'ynmultilisting_listing_follow');
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
		            Engine_Api::_()->getApi('mail','ynmultilisting')->send($listing -> getOwner(), 'ynmultilisting_listing_approved',$params);
		        }
		        catch(exception $e)
		        {
		            //keep silent
		        }
			}
			
			$listing -> approved_status = 'approved';
			$listing -> status = 'open';
			$listing -> approved = true;
			$listing -> approved_date = $now;
			$listing -> save();
			
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($listing -> getOwner(), $listing, $listing, 'ynmultilisting_listing_approve');
		}
	}
	
	public function buyListing($listing_id, $package_id)
	{
		$listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $listing_id);
		$listing -> last_payment_date = date("Y-m-d H:i:s");
		$listing -> package_id = $package_id;
		$listing -> approved_status = 'pending';
		$listing -> status = 'open';
		$listing -> save();  
	}
	
	public function featureListing($listing_id, $feature_day_number)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$now =  date("Y-m-d H:i:s");
		$featureTable = Engine_Api::_() -> getDbTable('features', 'ynmultilisting');
		$featureRow = $featureTable -> getFeatureRowByListingId($listing_id);
		$listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $listing_id);
		
		if(!empty($featureRow)) //used to feature listing
		{
			$featureRow -> modified_date = $now;
			$featureRow -> feature_day_number += $feature_day_number;
			$featureRow -> save();  
			
			if($featureRow -> active == 1)
			{
				if($featureRow -> feature_day_number == 1)
				{
					$type = 'day';
				}
				else 
				{
					$type = 'days';
				}
				if($featureRow -> expiration_date)
				{
					$expiration_date = date_add(date_create($featureRow -> expiration_date),date_interval_create_from_date_string($featureRow -> feature_day_number." ".$type));
				}
				else 
				{
					$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($featureRow -> feature_day_number." ".$type));
				}
				
				$featureRow -> active = 1;
				$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");;
				$featureRow -> feature_day_number = 0;
				$featureRow -> save();
				
				$listing -> feature_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");;
				$listing -> featured = 1;
				$listing -> save();
				
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($listing -> getOwner(), $listing, $listing, 'ynmultilisting_listing_status_change', array('status' => 'featured'));
			}
		}
		else //first time
		{
			$featureRow = $featureTable -> createRow();
			$featureRow -> user_id = $viewer -> getIdentity();
			$featureRow -> listing_id = $listing_id;
			$featureRow -> creation_date = $now;
			$featureRow -> modified_date = $now;
			$featureRow -> feature_day_number = $feature_day_number;
			$featureRow -> save();  
		}
	}
	
	 public function subPhrase($string, $length = 0) {
        if (strlen ( $string ) <= $length)
            return $string;
        $pos = $length;
        for($i = $length - 1; $i >= 0; $i --) {
            if ($string [$i] == " ") {
                $pos = $i + 1;
                break;
            }
        }
        return substr ( $string, 0, $pos ) . "...";
    }
     
    public function setPhoto($resource, $photo, $parent = null) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        }
        else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        }
        else {
            throw new Ynmultilisting_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        if ($parent) {
            $params = array(
                'parent_type' => $parent->getType(),
                'parent_id' => $parent -> getIdentity()
            );
        }
        else {
            $viewer = Engine_Api::_()->user()->getViewer();
            $params = array(
                'parent_type' => 'user',
                'parent_id' => $viewer -> getIdentity()
            );
        }
        // Save
        $storage = Engine_Api::_ ()->storage ();

        // Resize image (main)
        $image = Engine_Image::factory ();
        $image->open ( $file )->resize ( 720, 720 )->write ( $path . '/m_' . $name )->destroy ();

        // Resize image (profile)
        $image = Engine_Image::factory ();
        $image->open ( $file )->resize ( 240, 240 )->write ( $path . '/p_' . $name )->destroy ();

        // Resize image (normal)
        $image = Engine_Image::factory ();
        $image->open ( $file )->resize ( 110, 110 )->write ( $path . '/in_' . $name )->destroy ();

        // Resize image (icon)
        $image = Engine_Image::factory ();
        $image->open ( $file );

        $size = min ( $image->height, $image->width );
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample ( $x, $y, $size, $size, 50, 50 )->write ( $path . '/is_' . $name )->destroy ();

        // Store
        $iMain = $storage->create ( $path . '/m_' . $name, $params );
        $iProfile = $storage->create ( $path . '/p_' . $name, $params );
        $iIconNormal = $storage->create ( $path . '/in_' . $name, $params );
        $iSquare = $storage->create ( $path . '/is_' . $name, $params );

        $iMain->bridge ( $iProfile, 'thumb.profile' );
        $iMain->bridge ( $iIconNormal, 'thumb.normal' );
        $iMain->bridge ( $iSquare, 'thumb.icon' );

        // Remove temp files
        @unlink ( $path . '/p_' . $name );
        @unlink ( $path . '/m_' . $name );
        @unlink ( $path . '/in_' . $name );
        @unlink ( $path . '/is_' . $name );
        // Update row
        $resource -> photo_id = $iMain -> getIdentity();
        $resource -> save();
        return true;
    }

    //HOANGND get current listing type id
    public function getCurrentListingTypeId() {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_listingtype');
        $listingtype_id = $myNamespace->listingtype_id;
		$listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
        if (!$listingtype || !$listingtype->show) {
            $listingtype_id = $this->setCurrentListingType();
        }
        return $listingtype_id;
    }
    
    //HOANGND get current listing type
    public function getCurrentListingType() {
        $listingtype_id = $this->getCurrentListingTypeId();
		if ($listingtype_id) {
			$listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
			return ($listingtype && $listingtype->show) ? $listingtype : null;
		}
		else return null;
    }
    
    //HOANGND set current listing type
    public function setCurrentListingType($listingtype_id = 0) {
        if (!$listingtype_id) {
            $listingtype = Engine_Api::_()->getDbTable('listingtypes', 'ynmultilisting')->getDefaultListingType();
            $listingtype_id = ($listingtype) ? $listingtype->getIdentity() : 0;
        }
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_listingtype');
        $myNamespace->listingtype_id = $listingtype_id;
        return $listingtype_id;
    }
    
    //HOANGND get available pages name can manage page 
    public function getPagesName() {
        $arr = array (
            'ynmultilisting_index_index',
            'ynmultilisting_index_browse',
            'ynmultilisting_index_manage',
            'ynmultilisting_wishlist_index',
            'ynmultilisting_wishlist_manage',
            'ynmultilisting_review_index',
            'ynmultilisting_profile_index',
            'ynmultilisting_wishlist_view'
        );
        return $arr;
    }
	
	//HOANGND get avalable categories in compare list of current listingtype
	public function getAvailableCategories() {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
        $compare_list = $myNamespace->compare_list;
		if(empty($compare_list))
			return array();
		$listingtype_id = $this->getCurrentListingTypeId();
		$listingtypeCompare = (!empty($compare_list[$listingtype_id])) ? $compare_list[$listingtype_id] : array();
		if(empty($listingtypeCompare))
			return array();
        $categories = array_keys($listingtypeCompare);
        $categories_str = implode(',', $categories);
        if (empty($categories)) return array();
        $categoryTbl = Engine_Api::_()->getItemTable('ynmultilisting_category');
        $select = $categoryTbl -> select();
        $select -> where('category_id IN (?)', $categories);
        $select -> order(new Zend_Db_Expr("FIELD(category_id, $categories_str)"));
        $result = $categoryTbl->fetchAll($select);
        return $result;
    }
	
	//HOANGND count compare listings of category
	public function countComparelistingsOfCategory($category_id) {
        return count($this->getCompareListingsOfCategory($category_id));
    }
	
	//HOANGND remove category in compare
	public function removeCompareCategory($id) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();
        unset($myNamespace->compare_list[$listingtype_id][$id]);
        return count($myNamespace->compare_list[$listingtype_id]);
    }
	
	//HOANGND get photo span
	function getPhotoSpan($item, $type = null) {
  		if (!is_null($type)) {
  			$photoUrl = $item->getPhotoUrl($type);
  		}
  		else {
  			$photoUrl = $item->getPhotoUrl();
			if (!$photoUrl) {
				$photoUrl = $item->getPhotoUrl('thumb.profile');
			}
  		}

  		// set default photo
  		if (!$photoUrl) {
			$view = Zend_Registry::get("Zend_View");
			$photoUrl = $view->baseUrl().'/application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png';
		}

  		return '<a href = "'.$item -> getHref().'" title = "'.$item -> getTitle().'"><span class="ynmultilisting-item-photo-cover" style="background-image:url('.$photoUrl.');"></span></a>';
  	}
	
	//HOANGND remove listing in compare list
	public function removeComparelisting($id, $category_id) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();
        $compare_list = $myNamespace->compare_list[$listingtype_id];
        if (is_null($category_id)) {
            foreach ($compare_list as $key => $value) {
                $compare_listings = explode(',', $value);
                $pos = array_search($id, $compare_listings);
                if (false !== $pos) {
                    unset($compare_listings[$pos]);
                    if (!count($compare_listings)) {
                        unset($myNamespace->compare_list[$listingtype_id][$key]);
                    }
                    else {
                        $compare_listings_str = implode(',', $compare_listings);
                        $myNamespace->compare_list[$listingtype_id][$key] = $compare_listings_str;
                    }
                    return count($compare_listings);
                }
            }
            return false;
        }
        else {
            $compare_listings = $compare_list[$category_id];
            $compare_listings = explode(',', $compare_listings);
            $key = array_search($id, $compare_listings);
            if (false !== $key) {
                unset($compare_listings[$key]);
                if (empty($compare_listings)) {
                    unset($myNamespace->compare_list[$listingtype_id][$category_id]);
                }
                else {
                    $compare_listings_str = implode(',', $compare_listings);
                    $myNamespace->compare_list[$listingtype_id][$category_id] = $compare_listings_str;
                }
                return count($compare_listings);
            }
            else return false;
        }
    }
	
	//HOANGND add Listing to compare list
	public function addListingToCompare($id) {
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
        if (!$listing) return false;
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();
        $compare_list = $myNamespace->compare_list[$listingtype_id];
        $category = $listing->getCategory();
		if (!$category) return false;
		$category_id = $category->getIdentity();
  		$count = 0;
        if (isset($compare_list[$category_id])) {
            $compare_listings = $myNamespace->compare_list[$listingtype_id][$category_id];
            $compare_listings_arr = explode(',', $compare_listings);
            if (in_array($id, $compare_listings_arr)) return false;
            else {
                $myNamespace->compare_list[$listingtype_id][$category_id] = $compare_listings.','.$id;
                $count = count($compare_listings_arr) + 1;
            }
        }
        else {
            $myNamespace->compare_list[$listingtype_id][$category_id] = $id;
            $count = 1;
        }
        return $count;
    }
	
	
	//HOANGND check listing in compare list
	public function checkListingInCompare($id) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();
		if (empty($myNamespace->compare_list[$listingtype_id])) return false;
        $compare_list = $myNamespace->compare_list[$listingtype_id];
        foreach ($compare_list as $value) {
            $valueArr = explode(',', $value);
            if (in_array($id, $valueArr)) return true;
        }
        return false;
    }
	
	//HOANGND check is mobile
	function isMobile() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/(android|iphone|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|psp|treo)/i', $user_agent)) {
                return true;
            }
            return false;
         }
         else {
            return false;
         }
    }

	//HOANGND add category to compare
	public function addCompareCategory($id) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();
		if (!isset($myNamespace->compare_list[$listingtype_id]) || !isset($myNamespace->compare_list[$listingtype_id][$id])) {
			$myNamespace->compare_list[$listingtype_id][$id] = '';
		}
    }

	//HOANGND get listings on compare list of category
	public function getCompareListingsOfCategory($category_id) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();
		$listingtype_compare = $myNamespace->compare_list[$listingtype_id];
		if (empty($listingtype_compare)) return array();
        $compare_listings = (!empty($listingtype_compare[$category_id])) ? $listingtype_compare[$category_id] : array();
       	if (empty($compare_listings)) return array();
        $compare_listings_arr = explode(',', $compare_listings);
        if (empty($compare_listings_arr)) return array();
        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $select = $table -> select();
		$select -> where ('status = ?', 'open') -> where('approved_status = ?', 'approved') -> where('deleted = ?', '0') -> where('search = ?', '1');
        $select -> where('listing_id IN (?)', $compare_listings_arr)->order(new Zend_Db_Expr("FIELD(listing_id, $compare_listings)"));
        $result = $table->fetchAll($select);
        return $result;
    }
	
	//HOANGND get prev category
	public function getPrevCategory($category_id) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();       
        $compare_list = $myNamespace->compare_list[$listingtype_id];
        $categories = array_keys($compare_list);
        $index = array_search($category_id, $categories);
        if (!$index) {
            return false;
        }
        return $categories[$index-1];
    }
	
	//HOANGND get next category
	public function getNextCategory($category_id) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');       
        $listingtype_id = $this->getCurrentListingTypeId();       
        $compare_list = $myNamespace->compare_list[$listingtype_id];
        $categories = array_keys($compare_list);
        $index = array_search($category_id, $categories);
        if (($index === false) || ($index == (count($categories)-1))) {
            return false;
        }
        return $categories[$index+1];
    }
	
	//HOANGND sort compare listings
	public function updateCompareCategory($category_id, $newArr) {
        $myNamespace = new Zend_Session_Namespace('ynmultilisting_compare');
		$listingtype_id = $this->getCurrentListingTypeId();
        $myNamespace->compare_list[$listingtype_id][$category_id] = implode(',', $newArr);
    }

    /**
     * @param $name
     * @return bool
     */
    public function checkYouNetPlugin($name) {
        $table = Engine_Api::_ ()->getDbTable ( 'modules', 'core' );
        $select = $table->select ()->where ( 'name = ?', $name )->where ( 'enabled  = 1' );
        $result = $table->fetchRow ( $select );
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getAllVideoTypes()
    {
        $videoTypeOptions = array();
        $video_enable = Engine_Api::_() -> ynmultilisting() -> checkYouNetPlugin('video');
        $ynvideo_enable = Engine_Api::_() -> ynmultilisting() -> checkYouNetPlugin('ynvideo');
        $ynultimatevideo_enable = Engine_Api::_() -> ynmultilisting() -> checkYouNetPlugin('ynultimatevideo');
        if ($video_enable || $ynvideo_enable)
            $videoTypeOptions['video'] = $ynvideo_enable ? 'Advanced Videos' : 'Video';
        if ($ynultimatevideo_enable)
            $videoTypeOptions['ynultimatevideo_video'] = 'Ultimate Videos';

        return $videoTypeOptions;
    }

    /**
     * @return array
     */
    public function getEnabledVideoTypes()
    {
        $videoTypes = array('video', 'ynvideo', 'ynultimatevideo_video');
        foreach ($videoTypes as $key => $videoType)
        {
            $module = explode('_', $videoType);
            if( count($module) > 2 ) {
                $id = array_pop($module);
                $module = array(join('_', $module), $id);
            }
            if (!$this->checkYouNetPlugin($module[0]))
                unset($videoTypes[$key]);
        }
        return array_values($videoTypes);
    }

    /**
     * @return mixed|string
     */
    public function getMultilistingVideoType()
    {
        $videoTypes = $this->getEnabledVideoTypes();
        $videoType = '';
        if (!empty($videoTypes)) {
            $videoSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmultilisting_video_type', 'video');
            if (in_array($videoSetting, $videoTypes))
                $videoType = $videoSetting;
            else {
                $videoType = $videoTypes[0];
            }
        }
        return $videoType;
    }
}