<?php
class Advgroup_EventController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($event_id = (int)$this -> _getParam('event_id')) && null !== ($event = Engine_Api::_() -> getItem('advgroup_event', $event_id)))
			{
				Engine_Api::_() -> core() -> setSubject($event);
			}
			else
			if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
	}

	public function listAction()
	{
		//Checking Ynevent Plugin - View privacy
		$event_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynevent');
		if (!$event_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_Event_Search;
		if (!$viewer || !$viewer -> getIdentity())
		{
			$form -> removeElement('view');
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid())
		{
			return;
		}
		$val = $this -> _getAllParams();
		// Populate form data
		if ($form -> isValid($val))
		{
			$this -> view -> formValues = $values = $form -> getValues();
		}
		else
		{
			$form -> populate($defaultValues);
			$this -> view -> formValues = $values = array();
			$this -> view -> message = "The search value is not valid !";
			return;
		}
		// Prepare data
		$this -> view -> formValues = $values = array_merge($form -> getValues(), $_GET);

		if ($viewer -> getIdentity() && @$values['view'] == 5)
		{
			$values['users'] = array();
			foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo)
			{
				$values['users'][] = $memberinfo -> user_id;
			}
		}
		if ($viewer -> getIdentity() && @$values['view'] == 4)
		{
			$followTable = Engine_Api::_() -> getDbtable('follow', 'ynevent');
			$values['events'] = array();
			foreach ($followTable->getFollowEvents($viewer->user_id) as $event)
			{
				$values['events'][] = $event -> resource_id;
			}

		}
		else
		{
			if ($viewer -> getIdentity() && @$values['view'] != null)
			{
				$memberTable = Engine_Api::_() -> getDbtable('membership', 'ynevent');
				$values['events'] = array();
				foreach ($memberTable->getMemberEvents($viewer->user_id, $values['view']) as $event)
				{
					$values['events'][] = $event -> resource_id;
				}
			}
		}

		$values['search'] = 1;
		if ($selected_day = $this -> _getParam('selected_day'))
		{
			$values['selected_day'] = $selected_day;
		}
		else
		{
			if ($filter == "past")
			{
				$values['past'] = 1;
			}
			else
			{
				$values['future'] = 1;
				$values['order'] = new Zend_Db_Expr("ABS(TIMESTAMPDIFF(SECOND,NOW(), starttime))");
				$values['direction'] = 'asc';
			}
		}
		// Check create video authorization
		$canCreate = $group -> authorization() -> isAllowed($viewer, 'event');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'event');

		if ($canCreate && $levelCreate)
		{
			$this -> view -> canCreate = true;
		}
		else
		{
			$this -> view -> canCreate = false;
		}

		//Prepare data filer
		$values['parent_type'] = 'group';
		$values['parent_id'] = $group -> getIdentity();
		$values['limit'] = 12;
		//Get data
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('event') -> getEventPaginator($values);
	}

	public function manageAction()
	{
		//Checking Ynevent Plugin - Viewer required -View privacy
		$event_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynevent');
		if (!$event_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid())
		{
			return;
		}

		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $formFilter = new Advgroup_Form_Event_Manage;

		// Check create video authorization
		$canCreate = $group -> authorization() -> isAllowed(null, 'event');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'event');

		if ($canCreate && $levelCreate)
		{
			$this -> view -> canCreate = true;
		}
		else
		{
			$this -> view -> canCreate = false;
		}

		$defaultValues = $formFilter -> getValues();

		// Populate form data
		if ($formFilter -> isValid($this -> _getAllParams()))
		{
			$this -> view -> formValues = $values = $formFilter -> getValues();
		}
		else
		{
			$formFilter -> populate($defaultValues);
			$this -> view -> formValues = $values = array();
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbtable('events', 'ynevent');
		$tableName = $table -> info('name');

		// Only mine
		if (@$values['view'] == 2)
		{
			$select = $table -> select() -> where('user_id = ?', $viewer -> getIdentity());
		}
		// All membership
		else
		{
			$membership = Engine_Api::_() -> getDbtable('membership', 'ynevent');
			$select = $membership -> getMembershipsOfSelect($viewer);
		}
		$select -> where("`{$tableName}`.parent_type = ?", 'group');
		$select -> where("`{$tableName}`.parent_id = ?", $group -> getIdentity());
		if (!empty($values['text']))
		{
			$select -> where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
		}
		$select -> order('creation_date DESC');
		$select -> group('repeat_group');

		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> text = $values['text'];

		$this -> view -> view = $values['view'];

		$paginator -> setItemCountPerPage(20);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
	}

}
?>
