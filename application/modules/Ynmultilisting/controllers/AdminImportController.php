<?php
class Ynmultilisting_AdminImportController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_import');
    }
        
    public function fileAction() {

        //get max import listings settings
        $this->view->max_import = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmultilisting_max_import', 100);
		$this -> view -> form = $form = new Ynmultilisting_Form_Import_File();
		if(!Engine_Api::_()->hasItemType('video')) {
			$form -> removeElement('auth_video');
		}
		$packages = Engine_Api::_()->getDbTable('packages', 'ynmultilisting')->getPackageAssoc(array('show' => 1));
		if (empty($packages)) {
			$this->view->error = true;
			$this->view->message = $this->view->translate('Can not found any available packages. Please add some first!');
		}
		else {
			$form->package_id->setMultiOptions($packages);
		}
        $this->view->videoEnable = Engine_Api::_()->hasItemType('video');
    }
	
    public function fileOneByOneAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer->getIdentity()) {
            echo Zend_Json::encode(array('status' => false, 'message' => $this->view->translate('You don\'t have permission to do this.')));  
            return;
        }
        
        // If not post or form not valid, return
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => $this->view->translate('The request is invalid.')));  
            return;
        }

        $data = json_decode($this->_getParam('listing'));
        $auth_listing = json_decode($this->_getParam('auth'));
        $auto_improve = $this->_getParam('approved');
		$package_id = $this->_getParam('package_id');
		$package = Engine_Api::_()->getItem('ynmultilisting_package', $package_id);
		
		if (!$package) {
            echo Zend_Json::encode(array('status' => false, 'message' => $this->view->translate('Package not found.')));  
            return;
        }
		
        if(isset($data[0]))
            $title = strip_tags($data[0]);
        if(isset($data[1]))
             $tag = strip_tags($data[1]);
        if(isset($data[2]))
            $short_description = $data[2];
        if(isset($data[3]))
            $description = $data[3];
        if(isset($data[4]))
            $about_us = $data[4];
        if(isset($data[5]))
            $price = strip_tags($data[5]);
        if(isset($data[6]))
            $location = strip_tags($data[6]);
		if(isset($data[7]))
            $longitude = strip_tags($data[7]);
		if(isset($data[8]))
            $latitude = strip_tags($data[8]);
        if(isset($data[9]))
            $category_id = strip_tags($data[9]);
        if(isset($data[10]))
            $email = strip_tags($data[10]);
        if(empty($title) || empty($short_description)){
            echo Zend_Json::encode(array('status' => true, 'message' => $this->view->translate('Title or Short Description is empty.')));  
            return;
        }
        
        $table = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $listing = $table->createRow();
            $listing -> title = $title;
            $listing -> short_description = $short_description;
            if(!empty($description))
                $listing -> description = $description;
            if(!empty($about_us))
                $listing -> about_us = $about_us;
            if(!empty($price))
                $listing -> price = $price;
            if(!empty($location))
                $listing -> location = $location;
            if(!empty($longitude))
                $listing -> longitude = $longitude;
			if(!empty($latitude))
                $listing -> latitude = $latitude;
				
            if(!empty($category_id)) {
                $category = Engine_Api::_()->getItem('ynmultilisting_category', $category_id);
                if ($category) {
                    $listing->category_id = $category_id;
                    $listing->listingtype_id = $category->listingtype_id;
                }
                else {
                    echo Zend_Json::encode(array('status' => true, 'message' => ''));  
                    return;
                }
            }
            else {
                echo Zend_Json::encode(array('status' => true, 'message' => ''));  
                return;
            }
            
            $listing -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
            
            if(!empty($email)) {
                $tableUser = Engine_Api::_() -> getDbTable('users', 'user');
                $select = $tableUser -> select() -> where('email = ?', $email) -> limit(1);
                $user = $tableUser -> fetchRow($select);
                if($user){
                    $listing -> user_id = $user -> getIdentity();
                }
                else {
                    echo Zend_Json::encode(array('status' => true, 'message' => ''));  
                    return;
                }
            }
            else {
                echo Zend_Json::encode(array('status' => true, 'message' => ''));  
                return;
            }
            
            if ($auto_improve == '1') {
                $listing -> approved_status = 'approved';
                $listing -> approved_date = date("Y-m-d H:i:s");
            }
            else {
                $listing -> approved_status = 'pending';
            }
            $listing -> status = 'open';
			
			$listing -> package_id = $package_id;
			$type = ($package->valid_amount == 1) ? $type = 'day' : $type = 'days';
			$now =  date("Y-m-d H:i:s");
   			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($package->valid_amount." ".$type));
			$listing -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			
			$listing->theme = $package->themes[0];  
            $listing -> save();
            
            if ($listing->isOverMax()) {
                $listing->deleted = 1;
                echo Zend_Json::encode(array('status' => true, 'message' => ''));  
                return;
            }
            
            if(!empty($tag)) {
                $tags = preg_split('/[,]+/', $tag);
                $listing -> tags() -> addTagMaps($viewer, $tags);
            }
            
            //set authorization
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment', 'share', 'photo', 'discussion');
            if(Engine_Api::_()->hasItemType('video')) {
                array_push($auth_arr, 'video'); 
            }
            
            foreach ($auth_arr as $elem) {
                $auth_elem = 'auth_'.$elem;
                $auth_role = $auth_listing->$auth_elem;
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        echo Zend_Json::encode(array('status' => true, 'message' => '', 'id' => $listing->getIdentity()));  
        return;
    }
    
    public function fileHistoryAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer->getIdentity()) {
            echo Zend_Json::encode(array('status' => false, 'message' => $this->view->translate('You don\'t have permission to do this.')));  
            return;
        }
        
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => $this->view->translate('The request is invalid.')));  
            return;
        }

        $listings = json_decode($this->_getParam('listings'));
        $db = Engine_Api::_()->getDbtable('imports', 'ynmultilisting')->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('imports', 'ynmultilisting');
            $history = $table->createRow();
            $history -> file_name = $this->_getParam('filename');
            $history -> number_listings = count($listings);
            $history -> list_listings = $listings;
            $history -> creation_date = date('Y-m-d H:i:s');
            $history -> save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
        
        $db->commit();
        
        $auto_improve = $this->_getParam('approved');
        if ($auto_improve == '1') {
            foreach ($listings as $listing_id) {
                $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $listing_id);
                //send notification to follower
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $owner = $listing -> getOwner();
                // get follower
                $tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynmultilisting');
                $select = $tableFollow -> select() -> where('owner_id = ?', $owner -> getIdentity()) -> where('status = 1');
                $followers = $tableFollow -> fetchAll($select);
                foreach($followers as $row) {
                    $follower = Engine_Api::_()->getItem('user', $row -> user_id);
                    if ($follower)
                        $notifyApi -> addNotification($follower, $owner, $listing, 'ynmultilisting_listing_follow');
                }
                
                //send notifications end add activity on feed
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $notifyApi -> addNotification($owner, $viewer, $listing, 'ynmultilisting_listing_approve');
                
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($owner, $listing, 'ynmultilisting_listing_create');
                if($action) {
                    $activityApi->attachActivity($action, $listing);
                }
            }
        }
    }

	public function viewHistoryAction() {
		$table = Engine_Api::_()->getDbTable('imports', 'ynmultilisting');
		$page = $this -> _getParam('page', 1);
		$this->view->paginator = Zend_Paginator::factory($table->select()->order('creation_date DESC'));
        $this -> view -> paginator -> setItemCountPerPage(10);
        $this -> view -> paginator -> setCurrentPageNumber($page);
	}
	
	public function viewListingAction() {
		$import = Engine_Api::_()->getItem('ynmultilisting_import', $this->_getParam('id'));
		$this-> view -> list_listings = $import -> list_listings;
	}
	
	public function moduleAction() {
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> view -> form = $form = new Ynmultilisting_Form_Import_Module();
		
		$listingtypes = Engine_Api::_()->getDbTable('listingtypes', 'ynmultilisting')->getAvailableListingTypes();
		if (!count($listingtypes)) {
            $form->addError($this->view->translate('Don\'t have any listing types for importing listings.'));
            return;
		}
		
		$listingtypeArr = array();
		foreach ($listingtypes as $listingtype) {
			$listingtypeArr[$listingtype->getIdentity()] = $listingtype->getTitle();
		}
		$form->listingtype->addMultiOptions($listingtypeArr);
		$listingtype_id = $this->_getParam('listingtype', 0);
		if (!$listingtype_id) {
			$listingtype_id = key($listingtypeArr);
		}
		$form->listingtype->setValue($listingtype_id);
        
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting') -> getListingTypeCategories($listingtype_id);
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category_id->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$this->view->translate($category['title']));
            }
        }
        
        else {
           	$form->addError($this->view->translate('No categories available for import listings.'));
            return;
        }
		
        $allModules = Engine_Api::_()->getDbTable('modules', 'ynmultilisting')->getAvailableModules();
        $modules = array();
        foreach ($allModules as $module) {
            if (Engine_Api::_()->hasItemType($module->table_item)) {
                $form->getElement('module_id')->addMultiOption($module->getIdentity(), $module->getTitle());
                $modules[] = $module;
            }
        }
        if (!count($modules)) {
            $form->addError($this->view->translate('No modules available for import listings.'));
            return;
        }
		
		$form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          	'module' => 'ynmultilisting',
          	'controller' => 'import',
          	'action' => 'module-exec'
        ), 'admin_default', true));
        
    }

	public function moduleExecAction() {
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $module_id = $this->_getParam('module_id', 0);
        $this->view->module = $module = Engine_Api::_()->getItem('ynmultilisting_module', $module_id);
        if (!$module_id || !$module) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the module.');
            return;
        }
        $category_id = $this->_getParam('category_id', 0);
        $this->view->category = $category = Engine_Api::_()->getItem('ynmultilisting_category', $category_id);
        $listingtype = $category->getListingType();
        if (!$category_id || !$category || !$listingtype->hasCategory($category_id)) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the category.');
            return;
        }
        if (!Engine_Api::_()->hasItemType($module->table_item)) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the module.');
            return;
        }
		
		$all_owner = $this->_getParam('all_owner', 1);
		$owner_ids = $this->_getParam('owner_ids', '');
		
		
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
			$itemIds = $post['item_ids'];
			$listingIds = array();
			if (!empty($itemIds)) {
				$table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
				$itemTable = Engine_Api::_()->getItemTable($module->table_item);
				$primary = $itemTable->info(Zend_Db_Table_Abstract::PRIMARY);
			    $prop = array_shift($primary);		        
		        $ownerColumn = $module->owner_id_column;
				$itemSelect = $itemTable->select()->where("$prop IN (?)", $itemIds);
				$items = $itemTable->fetchAll($itemSelect);
		        $indexArr = array(
		            'title_column' => 'title',
		            'short_description_column' => 'short_description',
		            'description_column' => 'description',
		            'photo_id_column' => 'photo_id',
		            'about_us_column' => 'about_us',
		            'price_column' => 'price',
		            'currency_column' => 'currency',
		            'location_column' => 'location',
		            'long_column' => 'longitude',
		            'lat_column' => 'latitude',
		            'owner_id_column' => 'user_id'
		        );
		        $db = $table->getAdapter();
		        $db->beginTransaction();
		        try {
		            foreach ($items as $item) {
		            	$values = array(
	                    'listingtype_id' => $listingtype->getIdentity(),
	                    'category_id' => $category_id,
	                    'theme' => 'theme1',
	                    'status' => 'draft',
	                    'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'),
	                    'creation_date' => date('Y-m-d H:i:s'),
	                    'modified_date' => date('Y-m-d H:i:s'),
	                    'deleted' => 0
	                );
	                foreach ($indexArr as $key => $value) {
	                    if (!empty($module->$key)) {
	                        $itemKet = $module->$key;
	                        $values[$value] = $item->$itemKet;
	                    }
	                }
	                
	                $listing = $table->createRow();
	                $listing->setFromArray($values);
	                $listing->save();
					
					//add photo to profile album listing
					if ($listing->photo_id) {
						$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($listing->photo_id);				
						$listing->setPhoto($photo);
					}
					
					//set authorization
	                $auth = Engine_Api::_()->authorization()->context;
	                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
	                $auth_arr = array('view', 'comment', 'share', 'photo', 'discussion');
	                if(Engine_Api::_()->hasItemType('video')) {
	                    array_push($auth_arr, 'video'); 
	                }
	                
	                foreach ($auth_arr as $elem) {
	                    $auth_elem = 'auth_'.$elem;
	                    $auth_role = 'everyone';
	                    if ($auth_role) {
	                        $roleMax = array_search($auth_role, $roles);
	                        foreach ($roles as $i=>$role) {
	                           $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
	                        }
	                    }    
	                }
		            
					$listingIds[] = $listing->getIdentity();
						
						Engine_Api::_()->getDbTable('moduleimports', 'ynmultilisting')->importItem($module_id, $item->getIdentity(), $item->$ownerColumn, $listing->getIdentity());
		            }

					if (count($listingIds)) {
			            $historyTbl = Engine_Api::_()->getDbtable('imports', 'ynmultilisting');
			            $history = $historyTbl->createRow();
			            $history -> module_id = $module_id;
			            $history -> number_listings = count($listingIds);
			            $history -> list_listings = $listingIds;
			            $history -> creation_date = date('Y-m-d H:i:s');
			            $history -> save();
		            }
		        }
		        catch( Exception $e ) {
		            $db->rollBack();
		            throw $e;
		        }       
		        $db->commit();
			}
			$this->view->importMessage = $this->view->translate(array('%s listing has been imported', '%s listings has been imported', count($listingIds)), count($listingIds));
		}	
		
		$this->view->form = $form = new Ynmultilisting_Form_Import_ModuleSearch();
		$form->module_id->setValue($module_id);
		$form->category_id->setValue($category_id);
		$form->populate($this->_getAllParams());
        $values = $form->getValues();
		
		if ($all_owner) {
			$importedIds = Engine_Api::_()->getDbTable('moduleimports', 'ynmultilisting')->getImportedItemIdsOfModule($module_id);
		}
		else {
			$owner_ids = explode(',', $owner_ids);
			$importedIds = Engine_Api::_()->getDbTable('moduleimports', 'ynmultilisting')->getImportedItemIdsOfModule($module_id, $owner_ids);
		}
        $itemTable = Engine_Api::_()->getItemTable($module->table_item);
		$itemSelect = $itemTable->select();
		$primary = $itemTable->info(Zend_Db_Table_Abstract::PRIMARY);
	    $prop = array_shift($primary);
        $ownerColumn = $module->owner_id_column;
		$titleColumn = $module->title_column;
		if (!$all_owner) {
        	$itemSelect->where("$ownerColumn IN (?)", $owner_ids);
		}
		if (!empty($values['item_title'])) {
			$itemSelect->where("$titleColumn LIKE ?", "%".$values['item_title']."%");
		}
		if (!empty($values['imported'])) {
			$ids = (empty($importedIds)) ? array(0) : $importedIds;
			if ($values['imported'] == 'yes') {
				$itemSelect->where("$prop IN (?)", $ids);
			}
			else {
				$itemSelect->where("$prop NOT IN (?)", $ids);
			}
		}
		
		$page = $this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($itemSelect);
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($page);
		$this->view->paginator = $paginator;
		$this->view->importedIds = $importedIds;
    	$this->view->formValues = $values;
    }

	public function moduleExecOldAction() {
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> _helper -> layout -> setLayout('default-simple');
        $viewer = Engine_Api::_()->user()->getViewer();
        $module_id = $this->_getParam('module_id', 0);
        $this->view->module = $module = Engine_Api::_()->getItem('ynmultilisting_module', $module_id);
        if (!$module_id || !$module) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the module.');
            return;
        }
        $category_id = $this->_getParam('category_id', 0);
        $this->view->category = $category = Engine_Api::_()->getItem('ynmultilisting_category', $category_id);
        $listingtype = $category->getListingType();
        if (!$category_id || !$category || !$listingtype->hasCategory($category_id)) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the category.');
            return;
        }
        if (!Engine_Api::_()->hasItemType($module->table_item)) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the module.');
            return;
        }
		
		$owner_ids = $this->_getParam('owner_ids', '');
        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $listing_ids = $post['listing_id'];
            if (count($listing_ids)) {
                $where = $table->getAdapter()->quoteInto('listing_id IN (?)', $listing_ids);
                $data = array(
                    'deleted' => 0
                );
                $table->update($data, $where);
				
	            $historyTbl = Engine_Api::_()->getDbtable('imports', 'ynmultilisting');
	            $history = $historyTbl->createRow();
	            $history -> module_id = $module_id;
	            $history -> number_listings = count($listing_ids);
	            $history -> list_listings = $listing_ids;
	            $history -> creation_date = date('Y-m-d H:i:s');
	            $history -> save();
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array($this->view->translate(array('%s listing has been imported', '%s listings has been imported', count($listing_ids)), count($listing_ids)))
            ));    
        }
        
        $itemTable = Engine_Api::_()->getItemTable($module->table_item);
        $ownerColumn = $module->owner_id_column;
		$itemSelect = $itemTable->select();
		if ($owner_ids) {
			$owner_ids = explode(',', $owner_ids);
			$itemSelect->where("$ownerColumn IN (?)", $owner_ids);
		}
        $items = $itemTable->fetchAll($itemSelect);
        $indexArr = array(
            'title_column' => 'title',
            'short_description_column' => 'short_description',
            'description_column' => 'description',
            'photo_id_column' => 'photo_id',
            'about_us_column' => 'about_us',
            'price_column' => 'price',
            'currency_column' => 'currency',
            'location_column' => 'location',
            'long_column' => 'longitude',
            'lat_column' => 'latitude'
        );
        $listings = array();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            foreach ($items as $item) {
                $values = array(
                    'listingtype_id' => $listingtype->getIdentity(),
                    'category_id' => $category_id,
                    'user_id' => $viewer->getIdentity(),
                    'theme' => 'theme1',
                    'status' => 'draft',
                    'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'),
                    'creation_date' => date('Y-m-d H:i:s'),
                    'modified_date' => date('Y-m-d H:i:s'),
                    'deleted' => 1
                );
                foreach ($indexArr as $key => $value) {
                    if (!empty($module->$key)) {
                        $itemKet = $module->$key;
                        $values[$value] = $item->$itemKet;
                    }
                }
                
                $listing = $table->createRow();
                $listing->setFromArray($values);
                $listing->save();
				
				//add photo to profile album listing
				if ($listing->photo_id) {
					$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($listing->photo_id);				
					$listing->setPhoto($photo);
				}
				
				//set authorization
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $auth_arr = array('view', 'comment', 'share', 'photo', 'discussion');
                if(Engine_Api::_()->hasItemType('video')) {
                    array_push($auth_arr, 'video'); 
                }
                
                foreach ($auth_arr as $elem) {
                    $auth_elem = 'auth_'.$elem;
                    $auth_role = 'everyone';
                    if ($auth_role) {
                        $roleMax = array_search($auth_role, $roles);
                        foreach ($roles as $i=>$role) {
                           $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                        }
                    }    
                }
				
                $ele = array(
                    'listing' => $listing,
                    'item' => $item
                );
                $listings[] = $ele;
            }
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
        $db->commit();
        $this->view->listings = $listings;
    }

	public function suggestOwnerAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('user');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = Engine_Api::_()->getItemTable('user')->select()->where('search = ?', 1);
    
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $friend ){
            $data[] = array(
                'id' => $friend->getIdentity(),
                'label' => $friend->getTitle(), // We should recode this to use title instead of label
                'title' => $friend->getTitle(),
                'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                'url' => $friend->getHref(),
                'type' => 'user',
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
}