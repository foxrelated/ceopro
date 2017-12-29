<?php
class Ynmultilisting_AdminQuicklinkController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_listingtypes');
    }
        
    public function indexAction() {
        $listingTypeId = $this->_getParam('listingtype_id', 0);
        $this->view->listingType = $listingType = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingTypeId);
        
        if (!$listingTypeId || !$listingType) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not found the Listing Type!');
            return;
        }
        
        $page = $this->_getParam('page',1);
        $quicklinks = $listingType->getQuicklinks(array());
        $this->view->paginator = Zend_Paginator::factory($quicklinks);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
    
    public function createAction() {
        $listingTypeId = $this->_getParam('listingtype_id', 0);
        $this->view->listingType = $listingType = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingTypeId);
        
        if (!$listingTypeId || !$listingType) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not found the Listing Type!');
            return;
        }
        $supportedCurrencies = array();
        $gateways = array();
        $gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[$gateway -> gateway_id] = $gateway -> title;
            $gatewayObject = $gateway -> getGateway();
            $currencies = $gatewayObject -> getSupportedCurrencies();
            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[$gateway -> title] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            }
            else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
        $currencies = array_merge(array_combine($fullySupportedCurrencies,$fullySupportedCurrencies), array_combine($supportedCurrencies,$supportedCurrencies));
        
        $params = array();
        if($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
        }
        $this->view->params = $params;
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Quicklink_Create(array('currencies' => $currencies, 'params' => $params));
        
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting') -> getListingTypeCategories($listingTypeId);
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category_ids->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        }    
        
        $owners = array();
        if (isset($params['owner_ids'])) {
            $owner_ids = explode(',', $params['owner_ids']);
            foreach ($owner_ids as $id) {
                if (is_numeric($id)) {
                    $user = Engine_Api::_()->user()->getUser($id);
                    if ($user) $owners[] = $user;
                }
            }
        }
        $this->view->owners = $owners;
        
        $listings = array();
        if (isset($params['listing_ids'])) {
            $listing_ids = explode(',', $params['listing_ids']);
            foreach ($listing_ids as $id) {
                if (is_numeric($id)) {
                    $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                    if ($listing) $listings[] = $listing;
                }
            }
        }
        $this->view->listings = $listings;
        
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $this->getRequest()->getPost();
        if (isset($values['price-from'])) {
            foreach ($values['price-from'] as $from) {
                if (!filter_var($from, FILTER_VALIDATE_INT) && !empty($from)) {
                    $form->addError($this->view->translate('Price from must be an integer!'));
                    return;
                }
            }
        }
        
        if (isset($values['price-to'])) {
            foreach ($values['price-to'] as $to) {
                if (!filter_var($to, FILTER_VALIDATE_INT) && !empty($to)) {
                    $form->addError($this->view->translate('Price to must be an integer!'));
                    return;
                }
            }
        }
        
        $values['listingtype_id'] = $listingTypeId;
		
		if (isset($values['expire_from']) && $values['expire_from']) {
            $expire_from = new DateTime($values['expire_from']);
            $values['expire_from'] = $expire_from->format('Y-m-d');
        }
		else {
			$values['expire_from'] = null;
		}
		
        if (isset($values['expire_to']) && $values['expire_to']) {
            $expire_to = new DateTime($values['expire_to']);
            $values['expire_to'] = $expire_to->format('Y-m-d');
        }
		else {
			$values['expire_to'] = null;
		}
		
		if ($values['radius'] == '') {
			$values['radius'] = null;
		}
		
        $prices = array();
        if (isset($values['price-from'])) {
            foreach ($values['price-from'] as $key => $value) {
                $price = array();
                if ($value) $price['from'] = $value;
                if ($values['price-to'][$key]) $price['to'] = $values['price-to'][$key];
                $price['currency'] = $values['price-currency'][$key];
                $prices[] = serialize($price);
            }
        }
        
        $values['price'] = $prices;
        $table = Engine_Api::_()->getDbtable('quicklinks', 'ynmultilisting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $quicklink = $table->createRow();
            $quicklink->setFromArray($values);
            $quicklink->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'quicklink', 'action'=>'index', 'listingtype_id'=>$listingTypeId), 'admin_default', TRUE);
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
    }
    
    public function editAction() {
        $id = $this->_getParam('id', 0);
        $quicklink = Engine_Api::_()->getItem('ynmultilisting_quicklink', $id);
        if (!$id || !$quicklink) {
            $this->view->error = true;
            $this->view->message = $this->view->translate('Can not found the Quick Link');
        }
        $this->view->listingType = $listingType = $quicklink->getListingType();
        $supportedCurrencies = array();
        $gateways = array();
        $gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[$gateway -> gateway_id] = $gateway -> title;
            $gatewayObject = $gateway -> getGateway();
            $currencies = $gatewayObject -> getSupportedCurrencies();
            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[$gateway -> title] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            }
            else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
        $currencies = array_merge(array_combine($fullySupportedCurrencies,$fullySupportedCurrencies), array_combine($supportedCurrencies,$supportedCurrencies));
        
        $params = array();
        if($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
        }
        $this->view->params = $params;
        
        $this->view->form = $form = new Ynmultilisting_Form_Admin_Quicklink_Edit(array('currencies'=>$currencies,'quicklink'=>$quicklink,'params'=>$params));
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting') -> getListingTypeCategories($listingType->getIdentity());
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category_ids->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        } 
            
        //populate
        $form->populate($quicklink->toArray());
		if ($quicklink->expire_from) {
            $expire_from = new DateTime($quicklink->expire_from);
            $form->expire_from->setValue($expire_from->format('m/d/Y'));
        }
        if ($quicklink->expire_to) {
            $expire_to = new DateTime($quicklink->expire_to);
            $form->expire_to->setValue($expire_to->format('m/d/Y'));
        }
        $owners = array();
        $owner_ids = (isset($params['owner_ids'])) ? $params['owner_ids'] : $quicklink->owner_ids;
        $owner_ids = explode(',', $owner_ids);
        foreach ($owner_ids as $id) {
            if (is_numeric($id)) {
                $user = Engine_Api::_()->user()->getUser($id);
                if ($user) $owners[] = $user;
            }
        }
        $this->view->owners = $owners;
        
        $listings = array();
        $listing_ids = (isset($params['listing_ids'])) ? $params['listing_ids'] : $quicklink->listing_ids;
        $listing_ids = explode(',', $listing_ids);
        foreach ($listing_ids as $id) {
            if (is_numeric($id)) {
                $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                if ($listing) $listings[] = $listing;
            }
        }
        $this->view->listings = $listings;
        
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $this->getRequest()->getPost();
        if (isset($values['price-from'])) {
            foreach ($values['price-from'] as $from) {
                if (!filter_var($from, FILTER_VALIDATE_INT) && !empty($from)) {
                    $form->addError($this->view->translate('Price from must be an integer!'));
                    return;
                }
            }
        }
        
        if (isset($values['price-to'])) {
            foreach ($values['price-to'] as $to) {
                if (!filter_var($to, FILTER_VALIDATE_INT) && !empty($to)) {
                    $form->addError($this->view->translate('Price to must be an integer!'));
                    return;
                }
            }
        }
        
		if (isset($values['expire_from']) && $values['expire_from']) {
            $expire_from = new DateTime($values['expire_from']);
            $values['expire_from'] = $expire_from->format('Y-m-d');
        }
		else {
			$values['expire_from'] = null;
		}
		
        if (isset($values['expire_to']) && $values['expire_to']) {
            $expire_to = new DateTime($values['expire_to']);
            $values['expire_to'] = $expire_to->format('Y-m-d');
        }
		else {
			$values['expire_to'] = null;
		}
		
		if ($values['radius'] == '') {
			$values['radius'] = null;
		}
		
        $prices = array();
        if (isset($values['price-from'])) {
            foreach ($values['price-from'] as $key => $value) {
                $price = array();
                if ($value) $price['from'] = $value;
                if ($values['price-to'][$key]) $price['to'] = $values['price-to'][$key];
                $price['currency'] = $values['price-currency'][$key];
                $prices[] = serialize($price);
            }
        }
        
        $values['price'] = $prices;
        $table = Engine_Api::_()->getDbtable('quicklinks', 'ynmultilisting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $quicklink->setFromArray($values);
            $quicklink->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'quicklink', 'action'=>'index', 'listingtype_id'=>$listingType->getIdentity()), 'admin_default', TRUE);
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }              
    }
    
    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->quicklink_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $quicklink = Engine_Api::_()->getItem('ynmultilisting_quicklink', $id);
                $quicklink->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This Quick Link has been deleted.')
            ));
        }
    }
    
    public function multideleteAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $this -> view -> listingtype_id = $listingtype_id = $this -> _getParam('listingtype_id', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id) {
                $quicklink = Engine_Api::_()->getItem('ynmultilisting_quicklink', $id);
                if ($quicklink) {
                    $quicklink->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmultilisting','controller'=>'quicklink', 'action'=>'index', 'listingtype_id'=>$listingtype_id), 'admin_default', TRUE);
        }
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

    public function suggestListingAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        
        $listingTypeId = $this->_getParam('listingtype_id', 0);
        $this->view->listingType = $listingType = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingTypeId);
        
        if (!$listingTypeId || !$listingType) {
            return false;
        }
        
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        
        $params = array();
        if( null !== $text ) {
            $params['title'] = $text;
        }
        $params['limit'] = 10;
        $params['publish'] = true;
        $listings  = $listingType->getAllListings($params);
    
        // Retv data
        $data = array();
        foreach( $listings as $listing ){
            $owner = $listing->getOwner();
            $category = $listing->getCategory();
            $data[] = array(
                'id' => $listing->getIdentity(),
                'label' => $listing->getTitle(), // We should recode this to use title instead of label
                'title' => $listing->getTitle(),
                'photo' => $this->view->itemPhoto($listing, 'thumb.icon'),
                'url' => $listing->getHref(),
                'type' => $listing->getType(),
                'owner' => $this->view->translate('by %s', $this->view->htmlLink($owner->getHref(), $owner->getTitle())),
                'category' => $this->view->translate('Category: %s', $category->getTitle())
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

	public function showAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$id = $this -> _getParam('id');
		$value = $this -> _getParam('value');
        $quicklink = Engine_Api::_()->getItem('ynmultilisting_quicklink', $id);
        if (!$quicklink) {
            echo Zend_Json::encode(array('error' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not find the quick link.")));
            exit ;
        }
		$quicklink->show = $value;
		$quicklink->save();
		echo Zend_Json::encode(array());
        exit ;
	}
}