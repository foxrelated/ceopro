<?php
class Ynmobileview_AdminMenusController extends Core_Controller_Action_Admin
{

	protected $_menus;
	protected $_enabledModuleNames;

	public function init()
	{
		$this -> _enabledModuleNames = Engine_Api::_() -> getDbtable('modules', 'core') -> getEnabledModuleNames();
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmobileview_admin_main', array(), 'ynmobileview_admin_main_menus');
	}

	public function indexAction()
	{
		// Check and update menu items
		$menuItemsTable = Engine_Api::_() -> getDbtable('menuItems', 'core');
		$mb_menuItemsTable = Engine_Api::_() -> getDbtable('menuItems', 'ynmobileview');
		$menuItemsSelect = $menuItemsTable -> select() -> where('menu = ?', 'core_main');
		if (!empty($this -> _enabledModuleNames))
		{
			$menuItemsSelect -> where('module IN(?)', $this -> _enabledModuleNames);
		}
		$menuItems = $menuItemsTable -> fetchAll($menuItemsSelect);
		foreach ($menuItems as $item)
		{
			$mb_menuItemsTable -> checkAndAdd($item);
		}
		// Get mobile menu items

		$mb_menuItemsSelect = $mb_menuItemsTable -> select() -> order('order');
		if (!empty($this -> _enabledModuleNames))
		{
			$mb_menuItemsSelect -> where('module IN(?)', $this -> _enabledModuleNames);
		}
		$this -> view -> menuItems = $mb_menuItemsTable -> fetchAll($mb_menuItemsSelect);
	}

	public function createAction()
	{
	}

	public function editAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'ynmobileview');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name);
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemEdit();
		$form -> removeElement('icon');
    // Make safe
    $menuItemData = $menuItem->toArray();
    if( isset($menuItemData['params']) && is_array($menuItemData['params']) ) {
      $menuItemData = array_merge($menuItemData, $menuItemData['params']);
    }
    if( !$menuItem->custom ) {
      $form->removeElement('uri');
    }
    unset($menuItemData['params']);

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      $form->populate($menuItemData);
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();

    $menuItem->label = $values['label'];
    $menuItem->enabled = !empty($values['enabled']);
    unset($values['label']);
    unset($values['enabled']);

    if( $menuItem->custom ) {
      $menuItem->params = $values;
    } 
    if( !empty($values['target']) ) {
      $menuItem->params = array_merge($menuItem->params, array('target' => $values['target']));
    } else if( isset($menuItem->params['target']) ){
      // Remove the target
      $tempParams = array();
      foreach( $menuItem->params as $key => $item ){
        if( $key != 'target' ){
          $tempParams[$key] = $item;
        }
      }
      $menuItem->params = $tempParams; 
    }
    $menuItem->save();
    
    $this->view->status = true;
    $this->view->form = null;
  }

  public function deleteAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'ynmobileview');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name)
      ->order('order ASC');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem || !$menuItem->custom ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemDelete();
    
    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $menuItem->delete();

    $this->view->form = null;
    $this->view->status = true;
  }

	public function orderAction()
  {
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $table = Engine_Api::_()->getDbtable('menuItems', 'ynmobileview');
    $menuitems = $table->fetchAll($table->select());
    foreach( $menuitems as $menuitem ) 
    {
      $order = $this->getRequest()->getParam('admin_menus_item_'.$menuitem->name);
      if( !$order ){
        $order = 999;
      }
      $menuitem->order = $order;
      $menuitem->save();
    }
    return;
  }

}
