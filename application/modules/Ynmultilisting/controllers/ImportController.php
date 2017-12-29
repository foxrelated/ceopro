<?php

class Ynmultilisting_ImportController extends Core_Controller_Action_Standard {
    //HOANGND action for import listing
    public function fileAction() {
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> _helper -> content -> setEnabled();
        $viewer = Engine_Api::_() -> user() -> getViewer();
		$currentListingType = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        $this -> view -> form = $form = new Ynmultilisting_Form_Import_File(array('listingType' => $currentListingType));
        $form->removeElement('approved');
		$form->removeElement('package_id');
        //get max import listings settings
        $this->view->max_import = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmultilisting_max_import', 100);
        if(!Engine_Api::_()->hasItemType('video')) {
            $form -> removeElement('auth_video');
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
        if(isset($data[6])) {
            $location = strip_tags($data[6]);
        }
		if(isset($data[7])) {
            $longitude = strip_tags($data[7]);
        }
		if(isset($data[8])) {
            $latitude = strip_tags($data[8]);
        }
        if(isset($data[9]))
            $category_id = strip_tags($data[9]);
        if(empty($title) || empty($short_description)){
            echo Zend_Json::encode(array('status' => true, 'message' => $this->view->translate('Title or Short Description is empty.')));  
            return;
        }
        // Check max of listings can be add.
        $table = Engine_Api::_() -> getDbtable('listings', 'ynmultilisting');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity())->where('deleted = ?', '0')->where('listingtype_id = ?', Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId());
        $count_listings = count($table->fetchAll($select));
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        $max_listings_auth = $listingtype->getPermission(null, 'ynmultilisting_listing', 'max_listing');
        if ($max_listings_auth > 0 && $count_listings > $max_listings_auth) {
            echo Zend_Json::encode(array('status' => false, 'message' => $this->view->translate('Your listings is maximum.')));  
            return;
        }
        else {
            $db = $table->getAdapter();
            $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
            $db->beginTransaction();
            try {
                $listing = $table->createRow();
                $listing -> listingtype_id = $listingtype->getIdentity();
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
                
                $listing->theme = 'theme1';
                $categories = $listingtype -> getAllCategories();
                if ($categories) {
                    if(!empty($category_id)) {
                        $category = Engine_Api::_()->getItem('ynmultilisting_category', $category_id);
                        if ($category && $listingtype->hasCategory($category_id)) {
                            $listing->category_id = $category_id;
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
                }
                else {
                    echo Zend_Json::encode(array('status' => true, 'message' => ''));  
                    return;       
                }
                
                $listing -> user_id = $viewer -> getIdentity();
                $listing -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
                $listing -> status = 'draft';
                $listing -> save();
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
    }

    public function fileRollbackAction() {
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
        foreach($listings as $listing_id) {
            $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $listing_id);
            if (!$listing || !$listing->isAllowed('delete')) {
                continue;
            }
            else {
                $listing->deleted = 1;
                $listing->save();
            }
        }
        echo Zend_Json::encode(array('status' => true, 'message' => ''));  
            return;
    }

    public function moduleAction() {
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> _helper -> content -> setEnabled();
        $this -> view -> form = $form = new Ynmultilisting_Form_Import_Module();
		$form->removeElement('listingtype');
		$form->removeElement('all_owner');
		$form->removeElement('owners');
		$form->removeElement('owner_ids');
        
        $allModules = Engine_Api::_()->getDbTable('modules', 'ynmultilisting')->getAvailableModules();
        $modules = array();
        foreach ($allModules as $module) {
            if (Engine_Api::_()->hasItemType($module->table_item)) {
                $form->getElement('module_id')->addMultiOption($module->getIdentity(), $module->getTitle());
                $modules[] = $module;
            }
        }
        if (!count($modules)) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not import from module now. Please contact with the administrator for more information.');
            return;
        }
        
		$form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          	'action' => 'module-exec'
        ), 'ynmultilisting_import', true));
		
        $listingTypeId = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting') -> getListingTypeCategories($listingTypeId);
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category_id->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$this->view->translate($category['title']));
            }
        }
        
        else {
            $this->view->error = true;
            $this->view->message = $this->view->translate('No categories available now for import listings. Please contact to the administrator for more imformation');
            return;
        }
    }
    
    public function moduleExecAction() {
        if (!$this -> _helper -> requireUser -> isValid())
            return;
		$this -> _helper -> content -> setEnabled();
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
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
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
		        $itemSelect = $itemTable->select()->where("$ownerColumn = ?", $viewer->getIdentity())->where("$prop IN (?)", $itemIds);
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
						
						Engine_Api::_()->getDbTable('moduleimports', 'ynmultilisting')->importItem($module_id, $item->getIdentity(), $viewer->getIdentity(), $listing->getIdentity());
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
		$importedIds = Engine_Api::_()->getDbTable('moduleimports', 'ynmultilisting')->getImportedItemIdsOfModule($module_id, $viewer->getIdentity());
		
        $itemTable = Engine_Api::_()->getItemTable($module->table_item);
		$primary = $itemTable->info(Zend_Db_Table_Abstract::PRIMARY);
	    $prop = array_shift($primary);
        $ownerColumn = $module->owner_id_column;
		$titleColumn = $module->title_column;
        $itemSelect = $itemTable->select()->where("$ownerColumn = ?", $viewer->getIdentity());
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
	
	public function viewImportedListingsAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$module_id = $this->_getParam('module_id', 0);
        $this->view->module = $module = Engine_Api::_()->getItem('ynmultilisting_module', $module_id);
        if (!$module_id || !$module) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the module.');
            return;
        }
		
		if (!Engine_Api::_()->hasItemType($module->table_item)) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the module.');
            return;
        }
		
		$item_id = $this->_getParam('item_id', 0);
		$this->view->item = $item = Engine_Api::_()->getItem($module->table_item, $item_id);
		if (!$item) {
			$this->view->error = true;
            $this->view->message = $this->view->translate('Can not find the imported item.');
            return;
		}
		
		$listing_ids = Engine_Api::_()->getDbTable('moduleimports', 'ynmultilisting')->getListingIdsOfModule($module_id, $item_id);
		$table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
		$listings = array();
		if (!empty($listing_ids)) {
			$select = $table->select()->where('listing_id IN (?)', $listing_ids)->where('deleted = ?', '0');
			$listings = $table->fetchAll($select);
		}
		$this->view->listings = $listings;
	}
}
