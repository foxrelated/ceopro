<?php
class Ynmultilisting_Model_Listing extends Core_Model_Item_Abstract 
{
	protected $_parent_type = 'user';
	protected $_owner_type = 'user';
    
	public function getMediaType()
	{
	   return 'listing';
	}
	
	public function getFieldValue($field_id)
	{
		$valueTable = new Ynmultilisting_Model_DbTable_Values($this->getType(), 'values');
		$values = $valueTable -> getValues($this);
		$arrValue = array();
		foreach($values as $val)
		{
			$field = Engine_Api::_() -> getApi('fields','ynmultilisting') -> getFieldById($val -> field_id);
			if(isset($field) && $field -> type != "heading")
			{
				if(isset($arrValue[$val -> field_id])){
					$arrValue[$val -> field_id] .= ",".$val -> getValue();
				}else{
					$arrValue[$val -> field_id] = $val -> getValue();
				}
			}
		}
		if(!empty($arrValue))
		{
			$field = Engine_Api::_() -> getApi('fields','ynmultilisting') -> getFieldById($field_id);
			switch ($field -> type) {
				case 'multiselect':
				case 'multi_checkbox':
					$ids = explode(",", $arrValue[$field_id]);
					$arrMultiValue = array();
					foreach($ids as $option_id){
						if(!empty($option_id) && trim($option_id) != ""){
							$option = Engine_Api::_() -> fields() -> getOption($option_id, 'ynmultilisting_listing');
							$arrMultiValue[] = $option -> label;
						}
					}
					echo implode(', ', $arrMultiValue);
					break;
				case 'heading':
					break;
				default:
					print_r($arrValue[$field_id]);
					break;
			}
		}
		return;
	}
	
	public function getSingletonAlbum() 
	{
        $table = Engine_Api::_() -> getItemTable('ynmultilisting_album');
        $select = $table -> select() -> where('listing_id = ?', $this -> getIdentity())
						-> where('title = ?', 'Listing Profile')
						 -> order('album_id ASC') -> limit(1);
        $album = $table -> fetchRow($select);

        if(null === $album) {
        	$viewer = Engine_Api::_() -> user() -> getViewer();
            $album = $table -> createRow();
            $album -> setFromArray(array('title' => 'Listing Profile', 'user_id' => $viewer->getIdentity(), 'listing_id' => $this -> getIdentity()));
            $album -> save();
        }
        return $album;
    }
	
	public function getDescription()
    {
	    if(isset($this->short_description))
	    {
	      return strip_tags($this->short_description);
	    }
	    return '';
    }
	
	public function tags() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}
	
    public function getCategory() 
    {
        $category = Engine_Api::_()->getItem('ynmultilisting_category', $this->category_id);
        if ($category) {
            return $category;
        }
    }
    
	public function getListingType()
    {
    	return Engine_Api::_()->getItem('ynmultilisting_listingtype', $this->listingtype_id);
    }
	
    public function getCategoryTitle() 
    {
        $category = Engine_Api::_()->getItem('ynmultilisting_category', $this->category_id);
        if ($category) {
            return $category->getTitle();
        }
        else {
            return '';
        }
    }
    
    public function getSlug($str = NULL, $maxstrlen = 64)
	{
		$str = $this -> getTitle();
		if (strlen($str) > 32)
		{
			$str = Engine_String::substr($str, 0, 32) . '...';
		}
		$str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
		$str = strtolower($str);
		$str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
		$str = preg_replace('/-+/', '-', $str);
		$str = trim($str, '-');
		if (!$str)
		{
			$str = '-';
		}
		return $str;
	}
	
	public function getHref($params = array())
	{
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'ynmultilisting_profile',
			'reset' => true,
			'id' => $this -> getIdentity(),
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
    
	public function getPackage()
	{
		$table = Engine_Api::_()->getDbTable('packages', 'ynmultilisting');
		if(!empty($this->package_id))
		{
        	$select = $table->select()->where('package_id = ?', $this->package_id) -> limit(1);
        	return $table->fetchRow($select);
		}
		else
		{
			return new Ynmultilisting_Model_Package(array());
		}
	}
	
    public function isNew() 
    {
        $now = new DateTime();
        $creation_date = new DateTime($this->creation_date);
        $approved_date = new DateTime($this->approved_date);
        $new_days = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmultilisting_new_days', 3);
        if ($approved_date) 
        {
            $diff = date_diff($approved_date, $now);
        }
        else
        {
	        $diff = date_diff($creation_date, $now);
        } 
        $measure = ($diff->format('%a'));
        if ($measure <= $new_days)
        {
	        return true;
        } 
        return false;
    }
	
	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else if( $photo instanceof Storage_Model_File ) {
	      	$file = $photo->temporary();
	    }
		else if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Ynmultilisting_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'listing',
			'parent_id' => $this -> getIdentity()
		);
		// Save
		$storage = Engine_Api::_ ()->storage ();

		// Resize image (main)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 720, 720 )->write ( $path . '/m_' . $name )->destroy ();

		// Resize image (profile)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 400, 400 )->write ( $path . '/p_' . $name )->destroy ();

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
		$iSquare = $storage->create ( $path . '/is_' . $name, $params );

		$iMain->bridge ( $iProfile, 'thumb.profile' );
		$iMain->bridge ( $iSquare, 'thumb.icon' );

		// Remove temp files
		@unlink ( $path . '/p_' . $name );
		@unlink ( $path . '/m_' . $name );
		@unlink ( $path . '/is_' . $name );
		
		// Update row
		$this -> photo_id = $iMain -> getIdentity();
		$this -> save();
		
		//add photo to profile album listing
		$album = $this->getSingletonAlbum();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$photo = Engine_Api::_()->getItemTable("ynmultilisting_photo") -> createRow();
		$photo->collection_id = $album->album_id;
		$photo->listing_id = $this->getIdentity();
		$photo->user_id = $viewer->getIdentity();
        $photo->album_id = $album->album_id;
		$photo->file_id = $iMain -> getIdentity();
        $photo->save();
		return $this;
	}
    
    public function likes() 
    {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
    }
    
    public function comments() 
    {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
    }
    
    public function isOverMax() {
        $owner = $this->getOwner();
        $table = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting');
        $select = $table->select()->where('user_id = ?', $owner->getIdentity())->where('deleted = ?', '0')->where('listingtype_id = ?', $this->getListingType()->getIdentity());
        $count_listings = count($table->fetchAll($select));
        $max_listings_auth = $this->getListingType()->getPermission($owner, 'ynmultilisting_listing', 'max_listing');
        if ($max_listings_auth > 0 && $count_listings > $max_listings_auth) {
	        return true;
        } 
        return false;
    }
    
    public function ratingCount() 
    {
        $table = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $select = $table->select()
            ->where('listing_id = ?', $this->getIdentity())
            ->where('user_id <> ?', $this->user_id);
        $row = $table->fetchAll($select);
        $total = count($row);
        return $total;
    }

    public function reviewCount()
    {
        $table = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $select = $table->select()->where('listing_id = ?', $this->getIdentity());
        $row = $table->fetchAll($select);
        $total = count($row);
        return $total;
    }

    public function getRating() 
    {
        $table = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $rating_sum = $table->select()
            ->from($table->info('name'), new Zend_Db_Expr('SUM(overal_rating)'))
            ->group('listing_id')
            ->where('listing_id = ?', $this->getIdentity())
            ->query()
            ->fetchColumn(0)
        ;
        $total = $this->ratingCount();
        if ($total)
        {
            $rating = $rating_sum / $total;
        }
        else
        {
            $rating = 0;
        }
        return $rating;
    }
    
    public function checkRated() 
    {
        $table = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $rName = $table->info('name');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->where('listing_id = ?', $this->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity())
            ->limit(1);
        $row = $table->fetchAll($select);
        if (count($row) > 0)
        {
            return true;
        }
        return false;
    }

    public function sendEmailToFriends($recipients, $message) 
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        // Check recipients
        if( is_string($recipients) ) 
        {
			$recipients = preg_split("/[\s,]+/", $recipients);
        }
        if( is_array($recipients) ) 
        {
			$recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
        }
        if( !is_array($recipients) || empty($recipients) ) 
        {
			return 0;
        }
    
        // Check message
        $message = trim($message);
        $sentEmails = 0;
        $photo_url = ($this->getPhotoUrl('thumb.profile')) ? $this->getPhotoUrl('thumb.profile') : 'application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png';
        foreach( $recipients as $recipient ) 
        {
            $mailType = 'ynmultilisting_email_to_friends';
            $mailParams = array(
              'host' => $_SERVER['HTTP_HOST'],
              'email' => $recipient,
              'date' => time(),
              'sender_email' => $viewer->email,
              'sender_title' => $viewer->getTitle(),
              'sender_link' => $viewer->getHref(),
              'sender_photo' => $viewer->getPhotoUrl('thumb.icon'),
              'message' => $message,
              'object_link' => $this->getHref(),
              'object_title' => $this->title,
              'object_photo' => $photo_url,
              'object_description' => $this->description, 
            );
            
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $recipient,
              $mailType,
              $mailParams
            );
            $sentEmails++;
        }
        return $sentEmails;
    }

	public function delete()
	{
		// Delete all albums
		$albumTable = Engine_Api::_() -> getItemTable('ynmultilisting_album');
		$albumSelect = $albumTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($albumTable->fetchAll($albumSelect) as $listingAlbum)
		{
			$listingAlbum -> delete();
		}

		// Delete all topics
		$topicTable = Engine_Api::_() -> getItemTable('ynmultilisting_topic');
		$topicSelect = $topicTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($topicTable->fetchAll($topicSelect) as $listingTopic)
		{
			$listingTopic -> delete();
		}

		//Delete all announcment
		$reviewTable = Engine_Api::_() -> getItemTable('ynmultilisting_review');
		$reviewSelect = $reviewTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($reviewTable->fetchAll($reviewSelect) as $listingReview)
		{
			$listingReview -> delete();
		}

		//Delete reports
		$reportTable = Engine_Api::_() -> getItemTable('ynmultilisting_report');
		$reportSelect = $reportTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($reportTable->fetchAll($reportSelect) as $listingReport)
		{
			$listingReport -> delete();
		}
		
		if ($listing->photo_id) {
            $photo = Engine_Api::_()->getItem('storage_file', $listing->photo_id);
			if ($photo) $photo->remove();
        }
		
		//Delete import items
		Engine_Api::_()->getDbTable('moduleimports', 'ynmultilisting')->removeImportedItemsByListingId($this->getIdentity());
		$this -> deleted = 1;
		$this -> save();
	}

    public function getTitle() {
        return $this->title;
    }
    
    public function isAllowed($name) {
        return $this->getListingType()->checkAllow(null, 'ynmultilisting_listing', $name, $this);
    }

    public function isEditable()
    {
        return $this -> isAllowed('edit');
    }

    public function isDeletable()
    {
        return $this -> isAllowed('delete');
    }

    public function setApproved()
    {
        $listing = $this;
        $listing_id = $this -> getIdentity();
        $now =  date("Y-m-d H:i:s");

        //get feature
        $featureTable = Engine_Api::_() -> getDbTable('features', 'ynmultilisting');
        $featureRow = $featureTable -> getFeatureRowByListingId($listing_id);

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

    public function getReview()
    {
        $reviewTbl = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $select  = $reviewTbl -> select() -> where("listing_id = ? ", $this -> getIdentity());
        return $reviewTbl -> fetchRow($select);
    }

	//HOANGND check listing in compare list
	public function inCompare() {
        return Engine_Api::_()->ynmultilisting()->checkListingInCompare($this->getIdentity());
    }
	
	//HOANGND check listing in wishlist
	public function inWishlist($wishlist_id) {
		return Engine_Api::_()->getDbTable('wishlistlistings', 'ynmultilisting')->hasWishlistlisting($this->getIdentity(), $wishlist_id);
	}
	
	//HOANGND add listing to wishlist
	public function addToWishlist($wishlist_id) {
		Engine_Api::_()->getDbTable('wishlistlistings', 'ynmultilisting')->addListingToWishlist($this->getIdentity(), $wishlist_id);
	}

	//HOANGND get creation date
	function getCreationDate() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->creation_date));
        $date->setTimezone($timezone);
        return $date;
    }
	
	//HOANGND get expiration date
	function getExpirationDate() {
		if (!$this->expiration_date) return null;
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->expiration_date));
        $date->setTimezone($timezone);
        return $date;
    }
	
	//HOANGND get feature expiration date
	public function getFeatureExpirationDate() {
		if (!$this->feature_expiration_date) return null;
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->feature_expiration_date));
        $date->setTimezone($timezone);
        return $date;
	}
	
	//HOANGND get latest review of listing
	function getLatestReview() {
		$reviewTbl = Engine_Api::_()->getItemTable('ynmultilisting_review');
        $select  = $reviewTbl -> select() -> where("listing_id = ? ", $this -> getIdentity()) -> order('review_id DESC');
        return $reviewTbl -> fetchRow($select);
	}
}