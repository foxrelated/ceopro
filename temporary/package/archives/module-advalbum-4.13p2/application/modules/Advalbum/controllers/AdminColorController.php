<?php
class Advalbum_AdminColorController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('advalbum_admin_main', array(), 'advalbum_admin_main_color');
		
		$this -> view -> form = $form = new Advalbum_Form_Admin_Color();
		if (!$this->getRequest()->isPost())
		{
			return;
		}
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
	    }
		
		$values = $this->_getAllParams();
		$colorTbl = Engine_Api::_()->getDbTable("colors", "advalbum");
		if (isset($values['clear']) && $values['clear'] != '')
		{
			$colorTbl->setDefault();
			Engine_Api::_()->getDbTable("settings", "core")->setSetting("advalbum_maxcolor", 1);
			$form->addNotice("You have set default value successfully.");
			return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
		}
		else if (isset($values['submit']) && $values['submit'] != '')
		{
			foreach ($values as $k => $v)
			{
				if (strpos($k, "__") !== false)
				{
					$args = explode("__", $k);
					if($args[0] == 'color')
					{
						$colorId = $args[1];
						$colorTbl -> update(
								array(
										'hex_value' => "#" . $v
								),
								array(
										'color_id = ?' => $colorId,
								)
						);
					}
				}
				else if ($k == 'advalbum_maxcolor')
				{
					Engine_Api::_()->getDbTable("settings", "core")->setSetting("advalbum_maxcolor", (int)$v);
				}
			}
			$form->addNotice("Your changes have been saved.");
			//return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
		}
	}
}