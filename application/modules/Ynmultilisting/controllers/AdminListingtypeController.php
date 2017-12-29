<?php
class Ynmultilisting_AdminListingtypeController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_listingtypes');
    }
        
    public function indexAction()
    {
    	$typeTbl = Engine_Api::_()->getItemTable('ynmultilisting_listingtype');
    	$page = $this->_getParam('page', 1);
		$this -> view  -> types = $types = $typeTbl->getTypesPaginator(array(
			'visible' => 'all',
			'limit' => 15,
			'page' => $page
		));
    }

    public function showAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $id);
        if ($listingtype) {
            $listingtype->show = $value;
            $listingtype->save();
        }
    }

    public function createAction()
    {
    	// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Ynmultilisting_Form_Admin_Listingtype_Create();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		$data = $this -> getRequest() -> getPost();
		$values = array_merge($values, $data);
		$typeTbl = Engine_Api::_()->getItemTable('ynmultilisting_listingtype');
		$row = $typeTbl -> createRow();
		$row -> setFromArray($values);
        $select = $typeTbl -> select() -> order('order DESC') ->limit(1);
        $order = $select->query()->fetchObject()->order + 1;
        $row -> order = $order;
		
		$row -> save();
		//INSERT ROOT NODES
		$categoryTbl = Engine_Api::_()-> getItemTable('ynmultilisting_category');
		$root = $categoryTbl -> createRow();
		$root -> setFromArray(array(
			'listingtype_id' => $row -> getIdentity(),
			'pleft' => 1,
			'pright' => 2,
			'level' => 0,
			'title' => 'All Categories'
		));
		$root -> save();
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }
    
    public function editAction()
    {
    	// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id', 0);
		$row = $type = Engine_Api::_()->getItem('ynmultilisting_listingtype', $id);
		if (is_null($type))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$form = $this -> view -> form = new Ynmultilisting_Form_Admin_Listingtype_Edit(array(
			'listingtype' => $type
		));
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		$data = $this -> getRequest() -> getPost();
		$values = array_merge($values, $data);
		$row -> setFromArray($values);
		
		$row -> save();
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }
    
    public function deleteAction()
    {
    	// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id', 0);
		$row = $type = Engine_Api::_()->getItem('ynmultilisting_listingtype', $id);
		$categories = $type -> getCategories();

		if (is_null($type))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$form = $this -> view -> form = new Ynmultilisting_Form_Admin_Listingtype_Delete(array(
			'listingtype' => $type
		));
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		if ($values['listingtype_id'] > 0)
		{
            $categoryTbl = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting');
            $listingTbl = Engine_Api::_() -> getItemTable('ynmultilisting_listing');
            $newType = Engine_Api::_()->getItem('ynmultilisting_listingtype', $values['listingtype_id']);
            $parentNode = $topCategory = $newType -> getTopCategory();
            $newCategory = null;

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                foreach ($categories as $category)
                {
                    if ($category -> level == 0 || $category -> level > 3)
                    {
                        continue;
                    }
                    $data = $category -> toArray();
                    unset($data['category_id']);
                    $data['listingtype_id'] = intval($values['listingtype_id']);
                    if ($category -> level == 1)
                    {
                        $parentNode = $topCategory;
                        $newCategory = $categoryTbl -> addChild($parentNode, $data);
                        $listingTbl -> update(array(
                            'listingtype_id' => $newCategory -> listingtype_id,
                            'category_id' => $newCategory -> category_id,
                        ), array(
                            'category_id = ?' => $category -> category_id,
                        ));
                    }
                    if ($category -> level > 1)
                    {
                        if ($newCategory -> level == $category -> level - 1)
                        {
                            $parentNode = $newCategory;

                        }
                        $newCategory = $categoryTbl -> addChild($parentNode, $data);
                        $listingTbl -> update(array(
                            'listingtype_id' => $newCategory -> listingtype_id,
                            'category_id' => $newCategory -> category_id,
                        ), array(
                            'category_id = ?' => $category -> category_id,
                        ));
                    }
                }
                $row -> delete();
                $db->commit();
            }
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }
		} else {
			$db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
            	$listings = Engine_Api::_()->getItemTable('ynmultilisting_listing')->getListingTypeListings($row->getIdentity());
				foreach ($listings as $listing) {
					$listing->delete();
				}
				$categories = Engine_Api::_()->getItemTable('ynmultilisting_category')->getListingTypeCategories($row->getIdentity());
				foreach ($categories as $category) {
					$category->delete();
				}
				$row -> delete();
                $db->commit();
            }
            catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }
		}

		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }
    
	public function deleteSelectedAction()
    {
    	// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$ids = $this->_getParam('ids');
		if ($ids != '')	{
			$ids = explode(',', $ids);
			$form = $this -> view -> form = new Ynmultilisting_Form_Admin_Listingtype_Delete(array(
				'ids' => $ids
			));
			
			if (!$this -> getRequest() -> isPost())
			{
				return;
			}
			if (!$form -> isValid($this -> getRequest() -> getPost()))
			{
				return;
			}
			$values = $form -> getValues();
			$ids = $this->_getParam('ids');
				$ids = explode(',', $ids);
				if (count($ids))
				{
					foreach ($ids as $id)
					{
						$row = Engine_Api::_()->getItem('ynmultilisting_listingtype', $id);
						if ($values['listingtype_id'] > 0)
						{
							$row -> moveCategoryTo($values['listingtype_id']);
							$row -> moveListingTo($values['listingtype_id']);
						}
						else {
							$listings = Engine_Api::_()->getItemTable('ynmultilisting_listing')->getListingTypeListings($row->getIdentity());
							foreach ($listings as $listing) {
								$listing->delete();
							}
							$categories = Engine_Api::_()->getItemTable('ynmultilisting_category')->getListingTypeCategories($row->getIdentity());
							foreach ($categories as $category) {
								$category->delete();
							}
						}
						$row -> delete();		
					}
				}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
	}
    
    public function sortAction() 
    {
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $item) {
			if ($item != '')
			{
				$listingtype_id = substr($item, strrpos($item, '_') + 1);
				$listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
				$listingtype -> order = $i;
				$listingtype -> save();	
			}
		}
	}
    
    public function memberLevelPermissionAction() {
        $this->view->listingtype_id = $listingtype_id = $this -> _getParam('id', 0);
        $this->view->listingtype = $listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
        if (!$listingtype_id || !$listingtype) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not found the Listing Type!');
            return;
        }
        if (null !== ($id = $this->_getParam('level_id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } 
        else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if(!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;
    
        // Make form
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        
        $form->level_id->setValue($id);
		$form->setTitle($this->view->translate('Member Level Setttings - %s', $this->view->htmlLink($listingtype->getHref(), $listingtype->getTitle())));
 
        if ($level->type != 'public') {
            $elements = array();    
            if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                array_push($elements, 'first_amount', 'first_credit', 'credit', 'max_credit', 'period'); 
            }
            foreach ($elements as $element) {
                $form->removeElement($element);
            }
        }
        
        $Memberlevel = Engine_Api::_() -> getDbtable('memberlevelpermission', 'ynmultilisting');
        $flag = $Memberlevel -> select() -> where('listingtype_id = ?', $listingtype_id) -> where('level_id = ?', $id) -> where('type = ?', 'ynmultilisting_listing') -> limit(1) -> query() -> fetchColumn();
        if (!$flag) {
            $permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
            $form->populate($permissionsTable->getAllowed('ynmultilisting_listing', $id, array_keys($form->getValues())));
        	$numberFieldArr = Array('max_listing');
            foreach ($numberFieldArr as $numberField) {
                if ($permissionsTable->getAllowed('ynmultilisting_listing', $id, $numberField) == null) {
                    $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $id)
                    ->where('type = ?', 'ynmultilisting_listing')
                    ->where('name = ?', $numberField));
                    if ($row) {
                        $form->$numberField->setValue($row->value);
                    }
                }
            } 
		}
        else {
            $data = $Memberlevel->getAllowed('ynmultilisting_listing', $id, array_keys($form->getValues()), $listingtype_id);
            $auth_arr = array('auth_view', 'auth_comment', 'auth_share', 'auth_photo', 'auth_video', 'auth_discussion');
            foreach ($data as $key => $value) {
                if (in_array($key, $auth_arr)) {
                    $data[$key] = json_decode($value);
                }
            }
            $form->populate($data);
            
        }
        // Check post
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        // Check validitiy
        if(!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        
        $values = $form->getValues();
        
        foreach ($auth_arr as $check) {
            if(empty($values[$check])) {
                unset($values[$check]);
                $form->$check->setValue($data[$check]);
            }
        }
        
        $db = $Memberlevel->getAdapter();
        $db->beginTransaction();
        // Process
        
        try {
            $Memberlevel -> setAllowed('ynmultilisting_listing', $id, $values, $listingtype_id);
             // Commit
            $db->commit();
        }
    
        catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
        $form->addNotice('Your changes have been saved.'); 
    }

    public function manageMenuAction() {
    	$this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 
                'application/modules/Ynmultilisting/externals/scripts/collapsible.js');
        $listingTypeId = $this->_getParam('id', 0);
        $this->view->listingType = $listingType = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingTypeId);
        
        if (!$listingTypeId || !$listingType) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not found the Listing Type!');
            return;
        }
        
        $params = array();
        if($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
        }
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Listingtype_ManageMenu(array('listingtype' => $listingType, 'params' => $params));
        
        $promotion = $listingType->getPromotion();
        if ($promotion) {
            $promotion_arr = $promotion->toArray();
            unset($promotion_arr['text_color']);
            unset($promotion_arr['text_background_color']);
            $form->populate($promotion_arr);
        }
        
        if (!$this->getRequest()->isPost()){
            return;
        }
        $p_valid = $this->getRequest()->getPost();
        $error = false;
        if (empty($p_valid['top_category'])) {
            $form->addError($this->view->translate('Plase select at least 1 category for top categories'));
            $error = true;
        }
        if (empty($p_valid['more_category'])) {
            $form->addError($this->view->translate('Plase select at least 1 category for more categories'));
            $error = true;
        }
        $colorSettings = array(
            'text_color', 
            'text_background_color', 
        );
        $formValues  = $form->getValues();
        foreach ($colorSettings as $setting) {
            $p_valid[$setting] = $formValues[$setting];
        }
        if($form->isValid($p_valid)) {
            $p_post = $this->getRequest()->getPost();
            $values  = $form->getValues();
            Engine_Api::_()->getDbTable('categories', 'ynmultilisting')->updateListingTypeMenu($listingTypeId, $p_post['top_category'], $p_post['more_category']);
            if (!$promotion) {
                $table = Engine_Api::_()->getDbTable('promotions', 'ynmultilisting');
                $promotion = $table->createRow();
                $promotion->listingtype_id = $listingTypeId;
            }
            $promotion->setFromArray($p_post);
            $promotion->save();
            if(!empty($values['photo'])) {
                Engine_Api::_()->ynmultilisting()->setPhoto($promotion, $form->photo, $listingType);
            }
            $listingType->manage_menu = true;
            $listingType->save();
            $form->addNotice('Your changes have been saved.'); 
        }
    }

	public function getQuicklinkOptionsAction() {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $listingtype_id = $this -> _getParam('listingtype_id', 0);
		$loadnew = $this -> _getParam('load_new');
        if ($listingtype_id && $listingtype_id != 'all') {
            $listingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
			$quicklinks = $listingtype->getQuicklinks(array('show' => 1));
			$str = '';
			foreach ($quicklinks as $quicklink) {
				$id = $quicklink->getIdentity();
				$text = $quicklink->getTitle();	
				$str .= "<option selected value='{$id}'>{$text}</option>";
			}
            echo $str; exit;
        }
		else if ($listingtype_id && $listingtype_id == 'all') {
			$listingtypes = Engine_Api::_()->getDbTable('listingtypes', 'ynmultilisting')->getAvailableListingTypes();
			$str = '';
			foreach ($listingtypes as $listingtype) {
				$quicklinks = $listingtype->getQuicklinks(array('show' => 1));
				foreach ($quicklinks as $quicklink) {
					$id = $quicklink->getIdentity();
					$text = $quicklink->getTitle();	
					$str .= "<option selected value='{$id}'>{$text}</option>";
				}
			}
			echo $str; exit;
		}
		else echo ''; exit;
    }
}