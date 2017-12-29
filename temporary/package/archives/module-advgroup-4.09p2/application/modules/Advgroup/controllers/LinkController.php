<?php
class Advgroup_LinkController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
		{
			Engine_Api::_() -> core() -> setSubject($group);
		}
	}

	public function createAction()
	{
		//Check user and subject
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;

		$group = Engine_Api::_() -> core() -> getSubject('group');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
		{
			$this -> renderScript('/_error.tpl');
			return;
		}

		$this -> view -> form = $form = new Advgroup_Form_Link_Create();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;
		$values = $form -> getValues();
		$table = Engine_Api::_() -> getItemTable('advgroup_link');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$uri = $values['url'];
			if (strpos($uri, 'http') === false)
			{
				$uri = "http://" . $uri;
			}
			$link = $table -> createRow();
			$link -> group_id = $group -> group_id;
			$link -> owner_id = $group -> user_id;
			$link -> title = $values['title'];
			$link -> description = $values['description'];
			$link -> link_content = $uri;
			$link -> creation_date = date('Y-m-d H:i:s');
			$link -> save();

			// Process activity
			$body = "<a href='$link->link_content' target='_blank'>$link->title</a>";
			$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity(Engine_Api::_() -> user() -> getViewer(), $group, 'advgroup_link_new', $body);

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array(
			'id' => $group -> group_id,
			'action' => 'manage'
		), 'group_link', true);
	}

	public function manageAction()
	{
		if (Engine_Api::_() -> core() -> hasSubject())
			$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		else
			$this -> view -> group = $group = Engine_Api::_() -> getItem('group', $this -> _getParam('id'));
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
		{
			$this -> renderScript('/_error.tpl');
			return;
		}
		$table = Engine_Api::_() -> getItemTable('advgroup_link');
		$select = $table -> select() -> where('group_id = ?', $group -> group_id) -> order("creation_date DESC");
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage(20);
		$page = $this -> _getParam('page', 1);
		$paginator -> setCurrentPageNumber($page);
	}

	public function editAction()
	{
		$this -> view -> form = $form = new Advgroup_Form_Link_Edit();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!($id = $this -> _getParam('link_id')))
		{
			throw new Zend_Exception('No link id specified');
		}

		$table = Engine_Api::_() -> getItemTable('advgroup_link');
		$select = $table -> select() -> where("link_id = ?", $id);
		$link = $table -> fetchRow($select);
		if ($link -> owner_id != $viewer -> getIdentity())
		{
			$this -> renderScript('_error.tpl');
			return;
		}
		$form -> populate(array(
			'link_id' => $link -> link_id,
			'title' => $link -> title,
			'description' => $link -> description,
			'url' => $link -> link_content,
		));
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$uri = $values['url'];
				if (strpos($uri, 'http') === false)
				{
					$uri = "http://" . $uri;
				}
				$link -> title = $values['title'];
				$link -> description = $values['description'];
				$link -> link_content = $uri;
				$link -> creation_date = date('Y-m-d H:i:s');
				$link -> save();
				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
			$session = new Zend_Session_Namespace('mobile');
			if ($session -> mobile)
			{
				return $this -> _helper -> redirector -> gotoRoute(array(
					'id' => $link -> group_id,
					'action' => 'manage'
				), 'group_link', true);
			}
			else
			{
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh' => 10,
					'messages' => array('')
				));
			}
		}
		$this -> renderScript('/link/edit.tpl');
	}

	public function deleteAction()
	{
		$this -> view -> form = $form = new Advgroup_Form_Link_Delete();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$link = Engine_Api::_() -> getItem('advgroup_link', $values['link_id']);
				$link -> delete();
				$db -> commit();
			}
			catch(Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}

		if (!($id = $this -> _getParam('link_id')))
		{
			throw new Zend_Exception('No link id specified');
		}

		$table = Engine_Api::_() -> getItemTable('advgroup_link');
		$select = $table -> select() -> where("link_id = ?", $id);
		$link = $table -> fetchRow($select);
		if ($link -> owner_id != $viewer -> getIdentity())
		{
			$this -> renderScript('_error.tpl');
			return;
		}
		$form -> populate(array('link_id' => $link -> link_id));
		$this -> renderScript('/link/delete.tpl');
	}

}
