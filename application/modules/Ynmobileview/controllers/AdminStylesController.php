<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

class Ynmobileview_AdminStylesController extends Core_Controller_Action_Admin
{
	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmobileview_admin_main', array(), 'ynmobileview_admin_main_styles');
	}

	public function indexAction()
	{
		$stylesTable = Engine_Api::_() -> getDbTable('styles', 'ynmobileview');
		$select = $stylesTable -> select() -> order('title');
		$this -> view -> styles = $stylesTable -> fetchAll($select);
	}

	public function addAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$form = $this -> view -> form = new Ynmobileview_Form_Admin_Style();
		$form -> setAction($this -> view -> url(array()));

		// Check post
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$values = $form -> getValues();

		$styleTable = Engine_Api::_() -> getDbTable('styles', 'ynmobileview');
		$db = $styleTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$id = $styleTable -> insert(array('title' => $values['title']));

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$callbackUrl = $this->view->url(array(
			'module' => 'ynmobileview',
			'controller' => 'styles',
			'action' => 'edit',
			'id' => $id
		), null, true);
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRedirect' => $callbackUrl,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate("Mobile Custom Style successfully added."))
		));
	}

	public function deleteAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$style_id = $this -> _getParam('id');
		$this -> view -> $style_id = $style_id;
		$stylesTable = Engine_Api::_() -> getDbtable('styles', 'ynmobileview');
		$style = $stylesTable -> find($style_id) -> current();
		if (!$style)
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}
		else
		{
			$style_id = $style -> getIdentity();
		}

		if (!$this -> getRequest() -> isPost())
		{
			// Output
			$this -> renderScript('admin-styles/delete.tpl');
			return;
		}

		// Process
		$db = $stylesTable -> getAdapter();
		$db -> beginTransaction();

		try
		{

			$style -> delete();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh' => 10,
			'messages' => array('')
		));
	}

	public function editAction()
	{
		// In smoothbox
		$style_id = $this -> _getParam('id');
		$this -> view -> style_id = $style_id;
		$stylesTable = Engine_Api::_() -> getDbtable('styles', 'ynmobileview');
		$style = $stylesTable -> find($style_id) -> current();

		if (!$style)
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}
		else
		{
			$style_id = $style -> getIdentity();
		}

		$form = $this -> view -> form = new Ynmobileview_Form_Admin_EditStyle();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));

		$values = array();
		if ($style -> css_obj)
		{
			$values = Zend_Json::decode($style -> css_obj);
		}
		$values['title'] = $style -> title;

		$form -> populate($values);
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$values = $form -> getValues();

		$db = $stylesTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$style -> title = $values['title'];
			$style -> css_obj = Zend_Json::encode($values);
			$arr_css = array();
			foreach ($values as $key => $value)
			{
				if ($key != 'title')
				{
					if ($value != 'transparent')
					{
						$arr_css[$key] = "#" . $value;
					}
					else
					{
						$arr_css[$key] = $value;
					}
				}
			}
			$str = $this -> view -> partial("_css.tpl");
			$arr_keys = array_keys($arr_css);
			$arr_values = array_values($arr_css);
			$arr_keys = array_map(array(
				$this,
				'map'
			), $arr_keys);
			$str = str_replace($arr_keys, $arr_values, $str);
			$style -> css = $str;

			$style -> save();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array(
			'module' => 'ynmobileview',
			'controller' => 'styles',
			'action' => 'index'
		), null, true);
	}

	static public function map($a)
	{
		return "[{$a}]";
	}

	public function makeDefaultAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$style_id = $this -> _getParam('id');
		$stylesTable = Engine_Api::_() -> getDbtable('styles', 'ynmobileview');
		$style = $stylesTable -> find($style_id) -> current();

		if (!$style)
		{
			return;
		}
		else
		{
			$style_id = $style -> getIdentity();
		}
		// Process
		$db = $stylesTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$styles = $stylesTable -> fetchAll();
			foreach ($styles as $item)
			{
				$item -> active = 0;
				$item -> save();
			}

			$style -> active = 1;
			$style -> save();

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array(
			'module' => 'ynmobileview',
			'controller' => 'styles',
			'action' => 'index'
		), null, true);
	}

	public function removeDefaultAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$style_id = $this -> _getParam('id');
		$stylesTable = Engine_Api::_() -> getDbtable('styles', 'ynmobileview');
		$style = $stylesTable -> find($style_id) -> current();

		if (!$style)
		{
			return;
		}
		else
		{
			$style_id = $style -> getIdentity();
		}
		// Process
		$db = $stylesTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$style -> active = 0;
			$style -> save();

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array(
			'module' => 'ynmobileview',
			'controller' => 'styles',
			'action' => 'index'
		), null, true);
	}

}
