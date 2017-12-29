<?php

class Advgroup_ActivityController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
		{
			Engine_Api::_() -> core() -> setSubject($group);
		}
	}

	public function activityAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;

		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();

		if (!$group -> isOwner($viewer) && !$viewer -> isAdmin())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		// Get available types
		$actionType_table = Engine_Api::_() -> getDbTable('actionTypes', 'activity');
		$select = $actionType_table -> select() -> where('enabled = ?', 1);
		$session = new Zend_Session_Namespace('mobile');
		$available_types = Engine_Api::_() -> getApi('activity', 'advgroup') -> getActionTypesAssoc();
		if (count($available_types) == 0)
		{
			if ($session -> mobile)
			{
				$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => $callbackUrl,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no enabled group activities.'))
				));
			}
			else
			{
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no enabled group activities.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}

		$group_id = $this -> _getParam('group_id');
		if (!$group_id)
		{
			if ($session -> mobile)
			{
				$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => $callbackUrl,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no group item on this page.'))
				));
			}
			else
			{
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no group item on this page.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}

		$published_types = Engine_Api::_() -> getApi('activity', 'advgroup') -> getGroupActionTypes($group_id);

		// Prepare form
		$this -> view -> form = $form = new Advgroup_Form_Activity();
		$form -> activities -> addMultiOptions($available_types);
		$form -> activities -> setValue($published_types);

		// throw notice if count = 0
		if (count($available_types) == 0)
		{
			if ($session -> mobile)
			{
				$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => $callbackUrl,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no enabled group activities.'))
				));
			}
			else
			{
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no enabled group activities.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}

		$group_id = $this -> _getParam('group_id');
		if (!$group_id)
		{
			if ($session -> mobile)
			{
				$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => $callbackUrl,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no group item on this page.'))
				));
			}
			else
			{
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no group item on this page.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}

		// Not posting
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$table = Engine_Api::_() -> getDbTable('publicActivities', 'advgroup');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$selected_types = $form -> getValue('activities');
			if (!empty($select_types))
			{
				$table -> deleteGroupActionTypes($group_id);
			}
			else
			{
				$table -> deleteGroupActionTypes($group_id);
				$table -> updateGroupActionTypes($group_id, $selected_types);
			}
			$db -> commit();
		}
		catch(Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		if ($session -> mobile)
		{
			$callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRedirect' => $callbackUrl,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Activities updated.'))
			));
		}
		else
		{

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Activities updated.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function viewmoreAction()
	{
		// Get some options
		$params = array();
		$first_id = null;
		$next_id = null;
		$action_id = null;
		$end_of_feed = false;
		$limit = $this -> _getParam('itemCountPerPage', 10);

		if ($limit > 50)
		{
			$this -> view -> length = $limit = 50;
		}

		// Load configuration options for getting activity actions here
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$params['order'] = 'date';
		$params['limit'] = $limit;
		$params['minId'] = $request -> getParam('minid', null);
		$params['maxId'] = $request -> getParam('maxid', null);
		$params['action_types'] = null;
		$this -> view -> feed_only = $feed_only = $request -> getParam('feed_only', false);

		$actions = Engine_Api::_() -> getApi('activity', 'advgroup') -> getActionsByObject($params);

		// Are we at the end?
		if (count($actions) < $limit || count($actions) <= 0)
		{
			$end_of_feed = true;
		}

		if (count($actions) > 0)
		{
			foreach ($actions as $action)
			{
				// get next id
				if (null === $next_id || $action -> action_id <= $next_id)
				{
					$next_id = $action -> action_id - 1;
				}
				// get first id
				if (null === $first_id || $action -> action_id > $first_id)
				{
					$first_id = $action -> action_id;
				}

				// skip disabled actions
				if (!$action -> getTypeInfo() || !$action -> getTypeInfo() -> enabled)
					continue;

				// skip items with missing items
				if (!$action -> getSubject() || !$action -> getSubject() -> getIdentity())
					continue;
				if (!$action -> getObject() || !$action -> getObject() -> getIdentity())
					continue;
			}
		}

		$widget_height = $this -> _getParam('widget_height', '');
		if (!empty($widget_height))
		{
			$this -> view -> widget_height = $widget_height;
		}
		else
		{
			$this -> view -> widget_height = 0;
		}
		$this -> view -> group_actions = $actions;
		$this -> view -> activity_count = count($actions);
		$this -> view -> next_id = $next_id;
		$this -> view -> first_id = $first_id;
		$this -> view -> end_of_feed = $end_of_feed;
	}

}
