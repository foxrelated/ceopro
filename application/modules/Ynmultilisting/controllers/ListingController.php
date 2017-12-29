<?php
class Ynmultilisting_ListingController extends Core_Controller_Action_Standard {
	
	public function init() {
		if (0 !== ($listing_id = (int)$this -> _getParam('listing_id')) && null !== ($listing = Engine_Api::_() -> getItem('ynmultilisting_listing', $listing_id)))
		{
			Engine_Api::_() -> core() -> setSubject($listing);
		}
		$this -> _helper -> requireSubject('ynmultilisting_listing');
	}
	
	public function indexAction(){}
	
	public function packageChangeAction()
	{
		$listing = Engine_Api::_() -> core() -> getSubject();
		
		$currentPackage = Engine_Api::_() -> getItem('ynmultilisting_package', $listing -> package_id);
		
		$packageId = $this ->_getParam('packageId');
		$changePackage = Engine_Api::_() -> getItem('ynmultilisting_package', $packageId);
		
		$this -> view -> form = $form = new Ynmultilisting_Form_ChangePackage();
		if(empty($currentPackage))
		{
			$description = $this->view->translate('YNMULTILISTING_DASHBOARD_PACKAGE_MAKENEW');
		    $description = vsprintf($description, array(
		      $changePackage -> getTitle(),
	    	));
			$form -> setTitle('Buy Package');
			$form -> submit -> setLabel('Buy');
		}
		else 
		{
			$description = $this->view->translate('YNMULTILISTING_DASHBOARD_PACKAGE_WARNING');
		    $description = vsprintf($description, array(
		      $currentPackage -> getTitle(),
		      $changePackage -> getTitle(),
	    	));
		}
		
		$form->setDescription($description);
		// Not post/invalid
	    if( !$this->getRequest()->isPost() ) 
	    {
	    	return;
	    }
		
		$urlRedirect = $this -> view -> url(array(
	                'action' => 'place-order', 
	                'id' => $listing -> getIdentity(),
					'package_id' => $packageId
				), 'ynmultilisting_general', true);
				
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRedirect' => $urlRedirect,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
		
	}
	
	public function packageAction()
	{	
		$this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		if($listing->user_id != $viewer->getIdentity())
        {
           return $this -> _helper -> requireAuth() -> forward();
        }
	
		$table = Engine_Api::_() -> getItemTable('ynmultilisting_package');
		$select = $table -> select() -> where('`show` = 1') -> where('`deleted` = 0') -> order('order ASC');
		if($listing -> package_id != 0)
		{
			$this -> view -> currentPackage = $currentPackage = Engine_Api::_() -> getItem('ynmultilisting_package', $listing -> package_id);
			$select -> where('package_id <> ?', $currentPackage -> getIdentity());
		}
		
		$packages = $table -> fetchAll($select);
		$this -> view -> packages = $packages;
	}
	
	
	public function publishAction()
	{
		// Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// check and support social publisher
		if(Engine_Api::_() -> hasModuleBootstrap('socialpublisher'))
		{
			$listing = Engine_Api::_() -> core() -> getSubject();
			if ($listing)
			{
				$api = Engine_Api::_() -> socialpublisher();
				$resource_type = $listing -> getType();
				$resource_id = $listing -> getIdentity();
				$enable_settings = $api -> getTypeSettings($resource_type);
				$module_settings = $api -> getUserTypeSettings($viewer -> getIdentity(), $resource_type);
				$is_popup = ($enable_settings['active'] && count($module_settings['providers']));
				// item privacy satisty
				if ($is_popup)
				{
					switch ($module_settings['option'])
					{
						case Socialpublisher_Plugin_Constants::OPTION_ASK :
							// open popup
							$params = array(
								'action' => 'share',
								'resource_id' => $resource_id,
								'resource_type' => $resource_type,
							);
							$url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'socialpublisher_general');
							break;
						case Socialpublisher_Plugin_Constants::OPTION_AUTO :
							if (!empty($module_settings['providers']))
							{
								$providers = $module_settings['providers'];
								foreach ($providers as $provider)
								{
									$values = array(
										'service' => $provider,
										'user_id' => $viewer -> getIdentity()
									);
									$obj = Engine_Api::_() -> socialbridge() -> getInstance($provider);
									$token = $obj -> getToken($values);
									$default_status = $api -> getDefaultStatus(array(
										'viewer' => Engine_Api::_() -> user() -> getViewer(),
										'resource' => $listing,
										'title' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.general.site.title', $this -> view -> translate('SocialEngine Site'))
									));
									$photo_url = $api -> getPhotoUrl($listing);
									$post_data = $api -> getPostData($provider, $listing, $default_status, $photo_url);
									if (!empty($_SESSION['socialbridge_session'][$provider]))
									{
										try
										{
											$obj -> postActivity($post_data);
										}
										catch(Exception $e)
										{
										}
									}
									else
									{
										$_SESSION['socialbridge_session'][$provider]['access_token'] = $token -> access_token;
										$_SESSION['socialbridge_session'][$provider]['secret_token'] = $token -> secret_token;
										$_SESSION['socialbridge_session'][$provider]['owner_id'] = $token -> uid;
										try
										{
											$obj -> postActivity($post_data);
										}
										catch(Exception $e)
										{
										}
									}
								}
							}
							break;
						case Socialpublisher_Plugin_Constants::OPTION_NOT_ASK :
							break;
						default :
							break;
					}
				}
				if (isset($_SERVER['HTTPS']) &&
				    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
				    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
				    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
				  $protocol = 'https://';
				}
				else {
				  $protocol = 'http://';
				}
				$urlA =  $protocol . $_SERVER['HTTP_HOST'] . $url;
				return $this->_redirect($urlA);
			}
		}
	}
	
	public function deleteAction() {
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$listing = Engine_Api::_() -> core() -> getSubject();
		
		//check authorization for deleting listing.
        if (!$listing->isAllowed('delete')) {
            $this->view->error = true;
            $this->view->message = $this -> view -> translate('You don\'t have permission to delete this listing.');
            return;    
        }
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynmultilisting_Form_Delete();

		if (!$listing)
		{
			$this -> view -> error = false;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$db = $listing -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$listing -> delete();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
    		$notifyApi -> addNotification($listing -> getOwner(), $listing, $listing, 'ynmultilisting_listing_status_change', array('status' => 'deleted'));
			
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This listing has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynmultilisting_general', true),
			'messages' => Array($this -> view -> message)
		));
	}
	
	public function featureAction()
    {
  		$settings = Engine_Api::_()->getApi('settings', 'core');
		$fee_feature = $settings->getSetting('ynmultilisting_feature_fee', 0);
        $listing = Engine_Api::_() -> core() -> getSubject();
		
		$package = Engine_Api::_() -> getItem('ynmultilisting_package', $listing -> package_id);
	  	if(!($package -> getIdentity()))
		{
			return $this->_helper->requireSubject()->forward();
		}
	  	// Get form
		$this -> view -> form = $form = new Ynmultilisting_Form_Feature(array(
			'fee' => $fee_feature,
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
							'action' => 'place-order',
							'id' => $listing -> getIdentity(),
							'feature_day_number' => $this->_getParam('day'),
							), 'ynmultilisting_general', true);
							
		$this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRedirect' => $redirect_url,
            'format' => 'smoothbox',
            'messages' => array($this->view->translate("Please wait..."))
        ));
    }
	
	public function selectThemeAction()
	{
		$this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		if (in_array($listing -> status, array('draft', 'expired')))
		{
			return $this -> _helper -> requireAuth() -> forward();
		} 
		
		if(!$listing -> getPackage() -> getIdentity())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		// Check method and data validity.
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$listing -> theme = $this->_getParam('theme');	
		$listing -> save();
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Change theme successfully!')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}
	
	public function publishCloseAction()
	{
		$this -> view -> status = true;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$listing = Engine_Api::_() -> core() -> getSubject();
		
		if (!$listing -> isOwner($viewer))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("You don't have permission to close this listing.");
			return;
		}
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynmultilisting_Form_PublishClose();
		if($listing -> status == 'closed') {
			$form->setTitle('Open Listing')
            ->setDescription('Are you sure you want to open this listing?');
			$form->submit->setLabel('Open Listing');
			$changeStatus = 'open';
			$statusNotification = 'opened';
			$message = Zend_Registry::get('Zend_Translate') -> _('Listing has been opened.');
		}
		elseif($listing -> status == 'open') {
			$form->setTitle('Close Listing')
            ->setDescription('Are you sure you want to close this listing? It will force the listing to be closed before its expiration date.');
            $form->submit->setLabel('Close Listing');
			$changeStatus = 'closed';
			$statusNotification = 'closed';
			$message = Zend_Registry::get('Zend_Translate') -> _('Listing has been closed.');
		}
		
		if (!$listing)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Listing does not exists.");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$listing -> status = $changeStatus;
		$listing -> save();
		
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
        $notifyApi -> addNotification($listing -> getOwner(), $listing, $listing, 'ynmultilisting_listing_status_change', array('status' => $statusNotification));
			
		$this -> view -> message = $message;
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => $message,
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}
	    
    public function directionAction() {
        $this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
    }
    
    public function emailToFriendsAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;
        $viewer = Engine_Api::_() -> user() -> getViewer();
       
        $this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		
        if (!$listing) {
            return $this->_helper->requireSubject()->forward();
        }   
		
        $this->view->form = $form = new Ynmultilisting_Form_EmailToFriends();
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        
        if (!$form -> isValid($this -> getRequest() -> getPost())) {
            return;
        }
        $values = $form -> getValues();
        $sentEmails = $listing -> sendEmailToFriends($values['recipients'], @$values['message']);
        
        $message = Zend_Registry::get('Zend_Translate') -> _("$sentEmails email(s) have been sent.");
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRefresh' => false,
            'smoothboxClose' => true,
            'messages' => $message
        ));
    }

    public function transferOwnerAction() {
    	
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();

        if (!$listing) {
            return $this -> _helper -> requireSubject -> forward();
        }

        if (!$viewer -> isAdmin() && !$listing -> isOwner($viewer)) {
            return $this -> _helper -> requireAuth -> forward();
        }

        $this -> view -> form = $form = new Ynmultilisting_Form_TransferOwner();

        if (!$this -> getRequest() -> getPost()) {
            return;
        }

        if (!$form -> isValid($this -> getRequest() -> getPost())) {
            return;
        }
        //Process
        $values = $form -> getValues();
        $db = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting') -> getAdapter();
        $db -> beginTransaction();
        $friend = Engine_Api::_() -> user() -> getUser($values['toValues']);
        $tranfer_self = false;
        try {
            if ($listing -> user_id != $values['toValues']) {
                $listing -> user_id = $values['toValues'];
                $listing -> save();
                $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
                $action = $activityApi -> addActivity(Engine_Api::_()->getItem('user', $values['toValues']), $listing, 'ynmultilisting_listing_transfer');
                if ($action) {
                    $action -> attach($listing);
                }
            }
            else {
                $tranfer_self = true;
            }

            $db -> commit();
        } catch(Exception $e) {
            $db -> rollback();
            throw $e;
        }
        if ($tranfer_self) {
            return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => false, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You already is owner of this listing.')), ));
        }
        else return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('This listing is tranferred successfully.')), ));
    }
	
	public function printAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> listing = $subject = Engine_Api::_() -> core() -> getSubject();
        if(($subject-> status !='open') || ($subject -> approved_status !='approved'))
        {
            if($subject -> user_id != $viewer -> getIdentity())
            {
                return $this -> _helper -> requireAuth() -> forward();
            }
        }
        //get photos
        $this -> view -> album = $album = $subject -> getSingletonAlbum();
        $this -> view -> photos = $photos = $album -> getCollectiblesPaginator();
        $photos -> setCurrentPageNumber(1);
        $photos -> setItemCountPerPage(100);

        // get enabled video type
        $video_type = Engine_Api::_()->ynmultilisting()->getMultilistingVideoType();

        //get videos
        if ($video_type == 'video' || $video_type == 'ynvideo')
        {
            if(Engine_Api::_()->hasModuleBootstrap('video') || Engine_Api::_()->hasModuleBootstrap('ynvideo'))
            {
                $tableMappings = Engine_Api::_()->getDbTable('mappings','ynmultilisting');
                $params['listing_id'] = $subject -> getIdentity();
                $this -> view -> videos = $videos = $tableMappings -> getVideosPaginator($params);
                $videos -> setCurrentPageNumber(1);
                $videos -> setItemCountPerPage(100);
            }
        }
        //$this -> _helper -> content -> setEnabled();
    }

    public function editAction()
    {

        // Return if guest try to access to create link.
        if (!$this -> _helper -> requireUser -> isValid())
            return;

        $this -> _helper -> content -> setEnabled();
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $listing_id = $this->_getParam('listing_id');
        if(empty($listing_id))
        {
            return $this->_helper->requireSubject()->forward();
        }
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $listing_id);
		if(!$listing)
        {
        	return $this->_helper->requireSubject()->forward();
		}
        // Check authorization to edit listing.
        if (!$listing->isAllowed('edit')){
        	return $this->_helper->requireSubject()->forward();
        }
        //get package
        $package_id = $listing -> package_id;
        $package = Engine_Api::_() -> getItem('ynmultilisting_package', $package_id);
		
        //get listing type
        $this -> view -> listingtype = $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();

        $tableCategory = Engine_Api::_()->getItemTable('ynmultilisting_category');
        $category_id = $this -> _getParam('category_id', $listing->category_id);

        //get current category
        $category = Engine_Api::_() -> getItem('ynmultilisting_category', $category_id);
        if(!$category)
        {
            $categories = $listingtype -> getCategories();
            unset($categories[0]);
            $category = $categories[1];
        }
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

        $this -> view -> form = $form = new Ynmultilisting_Form_Edit( array(
            'item' => $listing,
            'category' => $category,
			'formArgs' => $formArgs,
       		'package' => $package,
        ));
		
        if($listing -> category_id != $category_id)
        {
            $this -> view -> switchCategory = '1';
        }
        $this -> view -> theme = $listing->theme;

        if(!Engine_Api::_()->hasItemType('video') && !Engine_Api::_()->hasItemType('ynultimatevideo_video'))
        {
            $form -> removeElement('upload_videos');
            $form -> removeElement('to');
        }

        // Populate category list.
        $categories = $listingtype -> getCategories();
        unset($categories[0]);

        foreach ($categories as $item)
        {
            $form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
        }
        if (count($form -> category_id -> getMultiOptions()) < 1)
        {
            $form -> removeElement('category_id');
        }
        if($listing -> status != 'draft')
        {
            $form -> removeElement('submit_button');
        }
		
        //populate location
        $form -> populate(array('location_address' => $listing->location));
        $form -> populate(array('lat' => $listing->latitude));
        $form -> populate(array('long' => $listing->longitude));

        //Populate Tag
        $tagStr = '';
        foreach ($listing->tags()->getTagMaps() as $tagMap)
        {
            $tag = $tagMap -> getTag();
            if (!isset($tag -> text))
                continue;
            if ('' !== $tagStr)
                $tagStr .= ', ';
            $tagStr .= $tag -> text;
        }
        $form -> populate(array('tags' => $tagStr, ));

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

        $form -> populate($listing -> toArray());

        //$form -> category_id -> setValue($category_id);
        $submit_button = $this -> _getParam('submit_button');
        $edit_button = $this -> _getParam('edit_button');

        //populate auth
        
        //set authorization
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $auth_arr = array('view', 'comment', 'share', 'photo', 'discussion');
        if(Engine_Api::_()->hasItemType('video')) {
            array_push($auth_arr, 'video'); 
        }
        
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($listing, $role, $elem)) {
                	$authVar = 'auth_'.$elem;
                    if ($form->$authVar)
                        $form->$authVar->setValue($role);
                }
            }
        }

        if (!isset($submit_button))
        {
            if (!isset($edit_button))
            {
                //Check if it edit category
                if($listing -> category_id != $category_id)
                {
                	//populate category
					$form -> category_id -> setValue($category_id);
                    $form->addError('Please note that all the informations of the existing category will be cleared when switching to another category.');
                }
                return;
            }
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


        $db = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting') -> getAdapter();
        $db -> beginTransaction();
        try
        {
            //Check if it edit category
            if($listing -> category_id != $category_id)
            {
                $old_category_id = $listing -> category_id;
                $isEditCategory = true;
            }
            // Edit listing
            $listing -> setFromArray($values);
            if(!empty($values['toValues']))
            {
				$main_video = Engine_Api::_()->getItemByGuid($values['toValues']);
				$listing -> video_id = $main_video->getIdentity();
				$listing -> video_type = $main_video->getType();
            }
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
//            var_dump($values);die;
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

            // Remove old data custom fields if edit category
            if($isEditCategory)
            {
                $old_category = Engine_Api::_()->getItem('ynmultilisting_category', $old_category_id);
                $tableMaps = Engine_Api::_() -> getDbTable('maps','ynmultilisting');
                $tableValues = new Ynmultilisting_Model_DbTable_Values($listing -> getType(), 'values');
                $tableSearch = Engine_Api::_() -> getDbTable('search','ynmultilisting');
                if($old_category)
                {
                    $fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_category->option_id));
                    $arr_ids = array();
                    if(count($fieldIds) > 0)
                    {
                        //clear values in search table
                        $searchItem  = $tableSearch->fetchRow($tableSearch -> select() -> where('item_id = ?', $listing->getIdentity()) -> limit(1));
                        foreach($fieldIds as $id)
                        {
                            try{
                                $column_name = 'field_'.$id -> child_id;
                                $searchItem -> $column_name = NULL;
                                $arr_ids[] = $id -> child_id;
                            }
                            catch(exception $e)
                            {
                                continue;
                            }
                        }
                        $searchItem -> save();
                        //delele in values table
                        if(count($arr_ids) > 0)
                        {
                            $valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $listing->getIdentity()) -> where('field_id IN (?)', $arr_ids));
                            foreach($valueItems as $item)
                            {
                                $item -> delete();
                            }
                        }
                    }
                }
            }
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
        if(isset($edit_button))
        {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'id' => $listing -> getIdentity(),
					'slug' => $listing -> getSlug(),
                ),  'ynmultilisting_profile', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
        if (isset($submit_button))
        {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'action' => 'package',
                    'listing_id' => $listing -> getIdentity()
                ),  'ynmultilisting_specific', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
    }
	
	//HOANGND add listing to compare list
	public function addToCompareAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $id = $this->_getParam('listing_id');
        $json = array(
            'error' => 0,
            'message' => ''
        );
        if (is_null($id)) {
            $json['error'] = 1;
            $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
            echo Zend_Json::encode($json);
            return;
        }
        
        $value = $this->_getParam('value');
        if ($value) {
            $count = Engine_Api::_()->ynmultilisting()->addListingToCompare($id);
            if ( $count === false) {
                $json['error'] = 1;
                $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
            }

        }
        else {
            if (Engine_Api::_()->ynmultilisting()->removeComparelisting($id, null) === false) {
                $json['error'] = 1;
                $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
            }
        }
        echo Zend_Json::encode($json);
        return;
    }
}
