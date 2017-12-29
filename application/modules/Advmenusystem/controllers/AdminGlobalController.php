<?php
class Advmenusystem_AdminGlobalController extends Core_Controller_Action_Admin
{

	public function init()
	{
		// Get list of menus
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advmenusystem_admin_main', array(), 'advmenusystem_admin_global_settings');
	}

	public function indexAction()
	{
		$contentTable = Engine_Api::_()->getDbtable('content', 'core');
		$select = $contentTable->select()->where("name = ?", "advmenusystem.advanced-main-menu");
		$menus = $contentTable->fetchAll($select);
		$this->view->form = $form = new Advmenusystem_Form_Admin_Widget_Main();
		if(count($menus) && $menus[0]->params && $menus[0]->params != '[]')
			$form->populate($menus[0]->params);
		$params = $this -> getRequest() -> getPost();
		if ($this -> getRequest() -> isPost() && $form -> isValid($params)){
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try {				
				foreach($menus as $menu){
					$menu->params = $form->getValues();
					$menu->save();
				}
				$db->commit();
				$form -> addNotice('Your changes have been saved.');
			} catch (Exception $e) {
				// throw $e;				
				$db->rollback();
				$form -> addError('Some errors happened.');
			}			
		}
	}
}
