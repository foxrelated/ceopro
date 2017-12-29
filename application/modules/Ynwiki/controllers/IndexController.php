<?php
class Ynwiki_IndexController extends Core_Controller_Action_Standard
{
	protected $_paginate_params = array();
	public function init()
	{
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> _paginate_params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynwiki.page', 10);
		$this -> _paginate_params['sort'] = $this -> getRequest() -> getParam('sort', 'recent');
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
		$this -> _paginate_params['search'] = $this -> getRequest() -> getParam('search', '');
	}

	public function checkparentallow($page, $viewer, $action)
	{
		$i = 1;
		while ($page -> parent_page_id != 0 && $i <= 20)
		{
			if (!$this -> _helper -> requireAuth() -> setAuthParams($page -> getParentPage(), $viewer, $action) -> isValid())
			{
				return false;
			}
			$page = $page -> getParentPage();
			$i++;
		}
		return true;
	}

	public function browseAction()
	{
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function listingAction()
	{
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function createAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$parentPage_id = 0;
		$parentPage = null;

		if ($this -> _getParam('fromPageId'))
		{
			$parentPage_id = $this -> _getParam('fromPageId');
			$parentPage = Engine_Api::_() -> getItem('ynwiki_page', $parentPage_id);

			//Check all permission of parent
			if ($this -> checkparentallow($parentPage, $viewer, 'view') == false)
			{
				return;
			}

			if (!$this -> _helper -> requireAuth() -> setAuthParams($parentPage, $viewer, 'edit') -> isValid())
				return;

			if (!$this -> _helper -> requireAuth() -> setAuthParams('ynwiki_page', null, 'create') -> isValid())
				return;
		}
		else
		{
			if (!$this -> _helper -> requireAuth() -> setAuthParams('ynwiki_page', null, 'createspase') -> isValid())
				return;
		}
		$this -> view -> form = $form = new Ynwiki_Form_Create();
		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		// Set up data needed to check quota
		$parent_type = $this -> _getParam('parent_type');
		$parent_id = $this -> _getParam('parent_id', $this -> _getParam('subject_id'));

		if ($parent_type && Engine_Api::_() -> hasItemType($parent_type))
		{
			$this -> view -> item = $item = Engine_Api::_() -> getItem($parent_type, $parent_id);
		}

		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create Wiki page
			$values = array_merge($form -> getValues(), array(
				'user_id' => $viewer -> getIdentity(),
				'creator_id' => $viewer -> getIdentity(),
				'owner_type' => $viewer -> getType(),
			));
			if (Engine_Api::_() -> ynwiki() -> checkTitle($values['title'], $parentPage_id))
			{
				$form -> getElement('title') -> addError('The title have existed!');
				return;
			}

			if ($parent_type)
			{
				$values['parent_type'] = $parent_type;
			}

			if ($parent_id)
			{
				$values['parent_id'] = $parent_id;
			}

			if ($parentPage -> parent_type == 'group')
			{
				$values['parent_type'] = $parentPage -> parent_type;
				$values['parent_id'] = $parentPage -> parent_id;
			}

			$page = $table -> createRow();
			$page -> setFromArray($values);
			$page -> save();

			$officerList = $page -> getOfficerList();

			// Add owner as member
			$page -> membership() -> addMember($viewer) -> setUserApproved($viewer) -> setResourceApproved($viewer);

			if ($parentPage)
			{
				$page -> parent_id = $parentPage -> parent_id;
				//insert all members into new wiki
				$members = $parentPage -> membership() -> getMembers($parentPage);

				$officerListParent = $parentPage -> getOfficerList();
				foreach ($members as $member)
				{
					if ($member -> getIdentity() != $viewer -> getIdentity())
					{
						if ($page -> membership() -> isMember($member))
						{
							$page -> membership() -> setUserApproved($member);
						}
						else
						{
							$page -> membership() -> addMember($member) -> setUserApproved($member);
						}
						if ($officerListParent -> has($member))
							$officerList -> add($member);
					}
				}
			}

			$page -> save();
			if ($page -> parent_type == null)
				$page -> parent_type = 'wiki';
			if ($page -> parent_id == null)
				$page -> parent_id = $page -> page_id;

			// Set photo
			if (!empty($values['thumbnail']))
			{
				$page -> setPhoto($form -> thumbnail);
			}

			$revision_table = Engine_Api::_() -> getItemTable('ynwiki_revision');
			$revision = $revision_table -> createRow();
			$revision -> setFromArray($values);
			$revision -> page_id = $page -> page_id;
			$revision -> creation_date = date('Y-m-d H:i:s');
			$revision -> save();

			$page -> revision_id = $revision -> revision_id;
			$page -> save();

			// Authorization set up
			$auth = Engine_Api::_() -> authorization() -> context;
			//$params['parent_type']

			$model = new Ynwiki_Model_Page( array());

			if ($parent_type == 'group' || $page -> parent_type == 'group')
			{
				$roles = $model -> role_group;
				$values['auth_view'] = 'parent_member';
			}
			else
			{
				$roles = $model -> role_wiki;
				$values['auth_view'] = 'everyone';
			}

			$values['auth_comment'] = 'everyone';
			$values['auth_restrict'] = 'ynwiki_list';
			$values['auth_edit'] = 'ynwiki_list';
			$values['auth_delete'] = 'ynwiki_list';

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			$restrictMax = array_search($values['auth_restrict'], $roles);
			$editMax = array_search($values['auth_edit'], $roles);
			$deleteMax = array_search($values['auth_delete'], $roles);

			foreach ($roles as $i => $role)
			{
				if ($role === 'ynwiki_list')
				{
					$role = $officerList;
				}
				$auth -> setAllowed($page, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($page, $role, 'comment', ($i <= $commentMax));
				$auth -> setAllowed($page, $role, 'restrict', ($i <= $restrictMax));
				$auth -> setAllowed($page, $role, 'edit', ($i <= $editMax));
				$auth -> setAllowed($page, $role, 'delete', ($i <= $deleteMax));
			}

			if (!$officerList -> has($viewer))
				$officerList -> add($viewer);

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$page -> tags() -> addTagMaps($viewer, $tags);

			if ($parent_type == 'group')
			{
				$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $item, 'advgroup_wiki_create');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $page);
				}
			}
			else
			{
				$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $page, 'ynwiki_new');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $page);
				}
			}

			// Commit
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		// Redirect
		if ($parent_type && $parent_id)
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'set-permission',
				'pageId' => $page -> page_id,
				'parent_type' => $parent_type,
				'parent_id' => $parent_id
			), 'ynwiki_general', true);
		}
		else
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'set-permission',
				'pageId' => $page -> page_id
			), 'ynwiki_general', true);
		}

	}

	public function joinAction()
	{
		// Check auth
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));

		if ($page)
		{
			$all_page = $page -> getAllPage2();
			if ($page -> parent_type == 'group')
			{
				$this -> view -> type = 'group';
				$group = Engine_Api::_() -> getItem('group', $page -> parent_id);
				if ($group)
				{
					Engine_Api::_() -> core() -> setSubject($group);
				}
				$subject_group = Engine_Api::_() -> core() -> getSubject();
				$check_approve = $subject_group -> membership() -> getMemberInfo($viewer);
				if ($subject_group -> membership() -> isMember($viewer) && $check_approve -> resource_approved == 1)
					$this -> view -> type = 'group_member';
				$this -> view -> group = $group;
				Engine_Api::_() -> core() -> clearSubject();
			}
			Engine_Api::_() -> core() -> setSubject($page);
		}
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$params = array();
		$subject = Engine_Api::_() -> core() -> getSubject();

		// Make form
		$this -> view -> form = $form = new Ynwiki_Form_Member_Join();
		// If member is already part of the group
		if ($subject -> membership() -> isMember($viewer, true))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();
			try
			{
				// Set the request as handled
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'advgroup_invite');
				if ($notification)
				{
					$notification -> mitigated = true;
					$notification -> save();
				}
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You are already a member of this wiki space.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();
			try
			{
				if (count($all_page) > 1)
				{
					foreach ($all_page as $item)
					{
						if ($item -> membership() -> isMember($viewer))
						{
							$item -> membership() -> setUserApproved($viewer);
						}
						else
						{
							$item -> membership() -> addMember($viewer) -> setUserApproved($viewer);
						}
					}
				}
				elseif (count($all_page) == 1)
				{
					if ($subject -> membership() -> isMember($viewer))
					{
						$subject -> membership() -> setUserApproved($viewer);
					}
					else
					{
						$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
					}
				}

				// Set the request as handled
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'ynwiki_invite');
				if ($notification)
				{
					$notification -> mitigated = true;
					$notification -> save();
				}

				// Add activity
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activityApi -> addActivity($viewer, $subject, 'ynwiki_join');

				$db -> commit();

			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You are now a member of this wiki space.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function removeAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if ($page)
		{
			$all_page = $page -> getAllPage2();

			Engine_Api::_() -> core() -> setSubject($page);
		}
		$this -> view -> form = $form = new Ynwiki_Form_Member_Remove();

		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		// Get user
		if (0 === ($user_id = (int)$this -> _getParam('user_id')) || null === ($user = Engine_Api::_() -> getItem('user', $user_id)))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$subject = Engine_Api::_() -> core() -> getSubject();

		if (!$subject -> membership() -> isMember($user))
		{
			throw new Group_Model_Exception('Cannot remove a non-member');
		}

		// Make form
		$this -> view -> form = $form = new Ynwiki_Form_Member_Remove();
		{
			// Process form
			if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
			{
				$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
				$db -> beginTransaction();

				try
				{
					foreach ($all_page as $item)
					{
						$list = $item -> getOfficerList();
						// remove from officer list
						$list -> remove($user);

						$item -> membership() -> removeMember($user);
					}

					$db -> commit();
				}
				catch( Exception $e )
				{
					$db -> rollBack();
					throw $e;
				}
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('This member removed.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}

	}

	public function leaveAction()
	{

		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if ($page)
		{
			$all_page = $page -> getAllPage2();
			Engine_Api::_() -> core() -> setSubject($page);
		}

		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> isOwner($viewer))
			return;

		// Make form
		$this -> view -> form = $form = new Ynwiki_Form_Member_Leave();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{

				foreach ($all_page as $item)
				{
					$list = $item -> getOfficerList();
					// remove from officer list
					$list -> remove($viewer);

					$item -> membership() -> removeMember($viewer);
				}
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have successfully left this wiki space.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}

	}

	public function editAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));

		//Check all permission of parent
		if ($this -> checkparentallow($page, $viewer, 'view') == false)
		{
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'edit') -> isValid())
			return;

		if (!$page)
		{
			return $this -> _helper -> requireAuth -> forward();

		}

		//check permission to edit this page
		/*if(!$page->checkToEdit())
		 {   echo "2";
		 return $this->_helper->requireAuth->forward();
		 }*/

		$this -> view -> form = $form = new Ynwiki_Form_Edit();

		// Populate form
		$form -> populate($page -> toArray());

		$tagStr = '';
		foreach ($page->tags()->getTagMaps() as $tagMap)
		{
			$tag = $tagMap -> getTag();
			if (!isset($tag -> text))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag -> text;
		}
		$form -> populate(array('tags' => $tagStr, ));
		$this -> view -> tagNamePrepared = $tagStr;

		// hide status change if it has been already published
		if ($page -> draft == "0")
		{
			$form -> removeElement('draft');
		}
		$form -> removeElement('parent_page_id');
		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			if ($values['title'] != $page -> title)
			{
				if (Engine_Api::_() -> ynwiki() -> checkTitle($values['title'], $page -> parent_page_id))
				{
					$form -> getElement('title') -> addError('The title have existed!');
					return;
				}
			}

			$page -> setFromArray($values);
			//$page->user_id = $viewer->user_id;
			$page -> modified_date = date('Y-m-d H:i:s');

			$revision_table = Engine_Api::_() -> getItemTable('ynwiki_revision');
			$revision = $revision_table -> createRow();
			$revision -> setFromArray($values);
			$revision -> user_id = $viewer -> user_id;
			$revision -> creation_date = date('Y-m-d H:i:s');
			$revision -> page_id = $page -> page_id;
			$revision -> save();

			$page -> revision_id = $revision -> revision_id;
			$page -> save();

			// Set photo
			if (!empty($values['thumbnail']))
			{
				$page -> setPhoto($form -> thumbnail);
			}

			Engine_Api::_() -> ynwiki() -> saveEdit($page, $viewer -> getIdentity());
			// handle tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$page -> tags() -> setTagMaps($viewer, $tags);
			if ($page -> parent_type == 'group')
			{
				$group = Engine_Api::_() -> getItem($page -> parent_type, $page -> parent_id);
				$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $group, 'advgroup_wiki_update');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $page);
				}
			}
			else
			{
				$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $page, 'ynwiki_update');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $page);
				}
			}
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$userFollows = $page -> getUserFollows();
			foreach ($userFollows as $follow)
			{
				if ($follow -> user_id != $viewer -> getIdentity())
				{
					$userFollow = Engine_Api::_() -> getItem('user', $follow -> user_id);
					$notificationSettingsTable = Engine_Api::_() -> getDbtable('notificationSettings', 'activity');

					if ($notificationSettingsTable -> checkEnabledNotification($userFollow, 'ynwiki_update') && !empty($userFollow -> email))
					{
						$notifyApi -> addNotification($userFollow, $viewer, $page, 'ynwiki_update', array('label' => $page -> title));
					}

				}
			}
			//}

			// Commit
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'pageId' => $page -> page_id,
			'slug' => $page -> getSlug()
		), 'ynwiki_general', true);
	}

	public function deleteAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();

		$parent_id = $page -> parent_page_id;
		$parent = Engine_Api::_() -> getItem('ynwiki_page', $parent_id);
		$parent_type = $page -> parent_type;
		$parentid = $page -> parent_id;

		//Check all permission of parent
		if ($this -> checkparentallow($page, $viewer, 'view') == false)
		{
			return;
		}
		//if($this->checkparentallow($page, $viewer, 'delete')==false){return;}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'delete') -> isValid())
			return;

		$brr = array();
		$arr = $page -> getChildOfPage($data, $page -> page_id, $brr);

		$tmp = array();
		$tmp[] = $page -> page_id;
		foreach ($arr AS $obj)
			$tmp[] = $obj -> page_id;
		$listid = implode(',', $tmp);

		Engine_Api::_() -> ynwiki() -> trash($listid);
		$page -> recycle();

		//$page->reDelete();
		// Redirect

		if ($parent_type == "group")
		{
			$this -> _helper -> redirector -> gotoRoute(array('id' => $parentid), 'group_profile', true);
		}
		else
		if ($parent)
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'view',
				'pageId' => $parent -> page_id,
				'slug' => $parent -> getSlug()
			), 'ynwiki_general', true);
		else
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'browse'), 'ynwiki_general', true);
	}

	public function getLabel($k)
	{
		$roles = array(
			'everyone' => 'Everyone',
			'registered' => 'All Registered Members',
			'parent_member' => 'Group Members (group wikis only)',
			'member' => 'Wiki members',
			'ynwiki_list' => 'Owner and Officer'
		);

		foreach ($roles AS $key => $value)
			if ($key == $k)
				return $value;
	}

	public function setPermissionAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'restrict') -> isValid())
			return;

		$params = array();
		$params['parent_type'] = $this -> _getParam('parent_type', $page -> parent_type);
		$params['parent_id'] = $this -> _getParam('parent_id', $page -> parent_id);

		$this -> view -> form = $form = new Ynwiki_Form_Permission($params);
		$form -> populate($page -> toArray());

		$auth = Engine_Api::_() -> authorization() -> context;
		//get role in model page
		$model = new Ynwiki_Model_Page( array());
		if ($params['parent_type'] == 'group' || $page -> parent_type == 'group')
		{
			// $roles = array('owner', 'parent_member','parent_member_member', 'registered', 'everyone');
			$roles = $model -> role_group;
		}
		else
		{
			$roles = $model -> role_wiki;
		}

		if ($page -> parent_page_id != 0)
			$form -> getElement('text_view') -> setContent('Inherited permission from a parent page : <b>' . $this -> getLabel($page -> checkToView($page -> parent_type)) . '</b>. <br/> The effective view permission for this page will be at least as restrictive as the inherited permission. ');

		foreach ($roles as $role)
		{
			if ($role == 'ynwiki_list')
			{
				$role = $page -> getOfficerList();
			}
			if ($auth -> isAllowed($page, $role, 'view'))
			{
				$form -> auth_view -> setValue($role);
			}

			if ($auth -> isAllowed($page, $role, 'edit'))
			{
				$form -> auth_edit -> setValue($role);
			}
			if ($auth -> isAllowed($page, $role, 'delete'))
			{
				$form -> auth_delete -> setValue($role);
			}
			if ($auth -> isAllowed($page, $role, 'restrict'))
			{
				$form -> auth_restrict -> setValue($role);
			}
			if ($auth -> isAllowed($page, $role, 'comment'))
			{
				$form -> auth_comment -> setValue($role);
			}
		}

		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			//$page->user_id = $viewer->user_id;
			$page -> save();
			// Create Wiki page
			$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(), ));
			// Set privacy

			if (empty($values['auth_restrict']))
			{
				$values['auth_restrict'] = array("owner");
			}
			if (empty($values['auth_edit']))
			{
				$values['auth_edit'] = array("owner");
			}
			if (empty($values['auth_view']))
			{
				$values['auth_view'] = array("everyone");
			}
			if (empty($values['auth_delete']))
			{
				$values['auth_delete'] = array("owner");
			}
			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = array("everyone");
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			$editMax = array_search($values['auth_edit'], $roles);
			$deleteMax = array_search($values['auth_delete'], $roles);
			$restrictMax = array_search($values['auth_restrict'], $roles);

			foreach ($roles as $i => $role)
			{
				if ($role === 'ynwiki_list')
				{
					$role = $page -> getOfficerList();
				}
				$auth -> setAllowed($page, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($page, $role, 'edit', ($i <= $editMax));
				$auth -> setAllowed($page, $role, 'delete', ($i <= $deleteMax));
				$auth -> setAllowed($page, $role, 'restrict', ($i <= $restrictMax));
				$auth -> setAllowed($page, $role, 'comment', ($i <= $commentMax));
			}
			//$include_restricts = $values['include_restricts'];
			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($page) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			// Commit
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'pageId' => $page -> page_id,
			'slug' => $page -> getSlug()
		), 'ynwiki_general', true);
	}

	public function demoteAction()
	{
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if ($page)
		{
			$all_page = $page -> getAllPage2();
			Engine_Api::_() -> core() -> setSubject($page);
		}

		// Get user
		if (0 === ($user_id = (int)$this -> _getParam('user_id')) || null === ($user = Engine_Api::_() -> getItem('user', $user_id)))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$page = Engine_Api::_() -> core() -> getSubject();
		$list = $page -> getOfficerList();

		if (!$page -> membership() -> isMember($user))
		{
			throw new Group_Model_Exception('Cannot remove a non-member as an officer');
		}

		$this -> view -> form = $form = new Ynwiki_Form_Member_Demote();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$table = $list -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			foreach ($all_page as $item)
			{
				$lst = $item -> getOfficerList();
				$lst -> remove($user);
			}

			//s$list->remove($user);

			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Demoted')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}

	public function promoteAction()
	{

		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if ($page)
		{
			$all_page = $page -> getAllPage2();
			Engine_Api::_() -> core() -> setSubject($page);
		}

		// Get user
		if (0 === ($user_id = (int)$this -> _getParam('user_id')) || null === ($user = Engine_Api::_() -> getItem('user', $user_id)))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$page = Engine_Api::_() -> core() -> getSubject();
		$list = $page -> getOfficerList();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$page -> membership() -> isMember($user))
		{
			throw new Group_Model_Exception('Cannot add a non-member as an officer');
		}

		$this -> view -> form = $form = new Ynwiki_Form_Member_Promote();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$table = $list -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{

			foreach ($all_page as $item)
			{
				$lst = $item -> getOfficerList();
				$lst -> add($user);
			}
			//$list->add($user);

			// Add notification
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($user, $viewer, $page, 'ynwiki_promote');

			// Add activity
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($user, $page, 'ynwiki_promote');

			//crate db allow for officer
			$pageId = $this -> _getParam('pageId');
			$page = Engine_Api::_() -> getItem('ynwiki_page', $pageId);
			$action = 'ynwiki_list';
			$key = 'restrict';
			$table = Engine_Api::_() -> getItemTable('ynwiki_list');
			$list = $table -> getValueByName($pageId, $action);
			if (count($list) > 0)
			{
				$allowTable = Engine_Api::_() -> getDbtable('allow', 'authorization');

				$allowTable -> setAllowed($page, $list, $key, true);
			}

			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Member Promoted')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}

	public function viewAction()
	{
		// Check permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));

		// $all_page = $page->getAllPage2($page->parent_id);

		if ($page)
		{
			Engine_Api::_() -> core() -> setSubject($page);
		}

		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}

		if ($this -> checkparentallow($page, $viewer, 'view') == false)
		{
			return;
		}

		if (!$page || !$page -> getIdentity())
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		// Prepare data
		$pageTable = Engine_Api::_() -> getDbtable('pages', 'ynwiki');

		$this -> view -> page = $page;
		$this -> view -> owner = $owner = $page -> getOwner();
		$this -> view -> viewer = $viewer;
		if (!$viewer -> getIdentity())
		{
			$this -> view -> can_rate = $can_rate = 0;
		}
		else
		{
			$this -> view -> can_rate = $can_rate = Engine_Api::_() -> ynwiki() -> canRate($page, $viewer -> getIdentity());
		}
		/*
		 if($page->isOwner($viewer) && Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.rate', 0) == 0)
		 {
		 $this->view->can_rate = $can_rate = 0;
		 }*/
		if (!$page -> isOwner($viewer))
		{
			$pageTable -> update(array('view_count' => new Zend_Db_Expr('view_count + 1'), ), array('page_id = ?' => $page -> getIdentity(), ));
		}
		//Get tags
		$t_table = Engine_Api::_() -> getDbtable('tags', 'core');
		$tm_table = Engine_Api::_() -> getDbtable('tagMaps', 'core');
		$p_table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$tName = $t_table -> info('name');
		$tmName = $tm_table -> info('name');
		$pName = $p_table -> info('name');

		$filter_select = $tm_table -> select() -> from($tmName, "$tmName.*") -> setIntegrityCheck(false) -> joinLeft($pName, "$pName.page_id = $tmName.resource_id", '')
		// -> where("$pName.draft = 0")
		-> where("$pName.page_id = ?", $page -> getIdentity());

		$select = $t_table -> select() -> from($tName, array(
			"$tName.*",
			"Count($tName.tag_id) as count"
		));
		$select -> joinLeft($filter_select, "t.tag_id = $tName.tag_id", '');
		$select -> order("$tName.text");
		$select -> group("$tName.text");
		$select -> where("t.resource_type = ?", "ynwiki_page");
		$this -> view -> tags = $tags = $t_table -> fetchAll($select);
		if ($viewer -> getIdentity() > 0)
			Engine_Api::_() -> ynwiki() -> saveView($page, $viewer -> getIdentity());

		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function previewRevisionAction()
	{
		// Check permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$revision = Engine_Api::_() -> getItem('ynwiki_revision', $this -> _getParam('revisionId'));
		$page = Engine_Api::_() -> getItem('ynwiki_page', $revision -> page_id);
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		if (!$page || !$page -> getIdentity() || ($page -> draft && !$page -> isOwner($viewer)))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		// Prepare data
		$pageTable = Engine_Api::_() -> getDbtable('pages', 'ynwiki');

		$this -> view -> revision = $revision;
		$this -> view -> page = $page;
		$this -> view -> owner = $owner = $page -> getOwner();
		$this -> view -> viewer = $viewer;
	}

	public function restoreRevisionAction()
	{
		// Check permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$revision = Engine_Api::_() -> getItem('ynwiki_revision', $this -> _getParam('revisionId'));
		$page = Engine_Api::_() -> getItem('ynwiki_page', $revision -> page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'edit') -> isValid())
		{
			return;
		}

		//$page->title = $revision->title;
		$page -> body = $revision -> body;
		$page -> revision_id = $revision -> revision_id;
		$page -> modified_date = date('Y-m-d H:i:s');
		$page -> save();

		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'pageId' => $page -> page_id,
			'slug' => $page -> getSlug()
		), 'ynwiki_general', true);
	}

	public function rateAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$page_id = (int)$this -> _getParam('pageId');
		$rates = (int)$this -> _getParam('rates');

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($rates == 0 || $page_id == 0)
		{
			return;
		}
		// Check page exist
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		$can_rate = Engine_Api::_() -> ynwiki() -> canRate($page, $viewer -> getIdentity());
		// Check user rated
		if (!$can_rate)
		{
			return;
		}
		$rateTable = Engine_Api::_() -> getDbtable('rates', 'ynwiki');
		$db = $rateTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$rate = $rateTable -> createRow();
			$rate -> user_id = $viewer -> getIdentity();
			$rate -> page_id = $page_id;
			$rate -> rate_number = $rates;
			$rate -> save();
			$rates = Engine_Api::_() -> ynwiki() -> getAVGrate($page_id);
			$page -> rate_ave = $rates;
			$page -> rate_count = $page -> rate_count + 1;
			$page -> save();
			// Commit
			$db -> commit();
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Rate successfully.'))
			));
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

	}

	public function historyAction()
	{
		$page_id = (int)$this -> _getParam('pageId');
		// Check page exist
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();

		Engine_Api::_() -> core() -> setSubject($page);

		$this -> view -> page = $page;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			$this -> view -> can_rate = $can_rate = 0;
		}
		else
		{
			$this -> view -> can_rate = $can_rate = Engine_Api::_() -> ynwiki() -> canRate($page, $viewer -> getIdentity());
		}
		$this -> view -> paginator = $paginator = $page -> getRevisions();
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage(10000000);
	}

	public function downloadAction()
	{
		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$page_id = (int)$this -> _getParam('pageId');
			$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
			if (!$page || (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynwiki.download', 0) == 0 && !$viewer -> getIdentity()))
				return $this -> _helper -> requireAuth -> forward();
			else
			{
				$this -> _helper -> layout -> disableLayout();
				$this -> _helper -> viewRenderer -> setNoRender(TRUE);
			}
			$pdf = new HTML2PDF('P', 'A4', 'fr');

			$pdf -> pdf -> SetDisplayMode('real');
			$childs = $this -> view -> partial('_childs_pdf.tpl', array('page' => $page));

			//Add rule for html2pdf
			$content = preg_replace("/\<colgroup.*?\<\/colgroup\>/", '', $page -> body);
            $content = preg_replace("/\<iframe.*?\<\/iframe\>/", '', $content);
			$pdf -> WriteHTML('<page style="font-family: freeserif"><br />' . '<h4>' . nl2br($page -> title) . '</h4>' . nl2br($content) . $childs . '</page>');

			$name = "page_" . $page -> page_id . ".pdf";
			$pdf -> Output($name);

		}
		catch(Exception $e)
		{
			$translate = Zend_Registry::get('Zend_Translate');
			$this -> view -> error_msg = $translate -> translate("Can not convert this page to PDF!");
			$this -> _helper -> layout -> enableLayout();
			$this -> _helper -> viewRenderer -> setNoRender(FALSE);
			return;

		}
	}

	public function printViewAction()
	{
		$page_id = (int)$this -> _getParam('pageId');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page || (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynwiki.print', 0) == 0 && !$viewer -> getIdentity()))
			return $this -> _helper -> requireAuth -> forward();
		else
			$this -> _helper -> layout -> disableLayout();
		$this -> view -> page = $page;
	}

	public function followAction()
	{

		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page_id = (int)$this -> _getParam('pageId');
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('pages', 'ynwiki') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if ($page)
			{
				if ($page -> checkFollow())
				{
					$follow_table = Engine_Api::_() -> getItemTable('ynwiki_follow');
					$follow = $follow_table -> createRow();
					$follow -> page_id = $page -> page_id;
					$follow -> user_id = $viewer -> getIdentity();
					$follow -> save();
					$page -> follow_count = $page -> follow_count + 1;
					$page -> save();
					$db -> commit();
					echo Zend_Json::encode(array('success' => 1));
				}
			}
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function unFollowAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page_id = (int)$this -> _getParam('pageId');
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('pages', 'ynwiki') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if ($page)
			{
				if (!$page -> checkFollow())
				{
					$follow = Engine_Api::_() -> ynwiki() -> getFollow($viewer -> getIdentity(), $page_id);
					$follow -> delete();
					$page -> follow_count = $page -> follow_count - 1;
					$page -> save();
					$db -> commit();
				}
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array($this -> view -> translate('Unfollow successfully.'))
				));
			}
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function unFollowAjaxAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page_id = (int)$this -> _getParam('pageId');
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('pages', 'ynwiki') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if ($page)
			{
				if (!$page -> checkFollow())
				{
					$follow = Engine_Api::_() -> ynwiki() -> getFollow($viewer -> getIdentity(), $page_id);
					$follow -> delete();
					$page -> follow_count = $page -> follow_count - 1;
					$page -> save();
					$db -> commit();
				}
				echo Zend_Json::encode(array('success' => 1));
			}
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function favouriteAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page_id = (int)$this -> _getParam('pageId');
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('pages', 'ynwiki') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if ($page)
			{
				if ($page -> checkFavourite())
				{
					$favourite_table = Engine_Api::_() -> getItemTable('ynwiki_favourite');
					$favourite = $favourite_table -> createRow();
					$favourite -> page_id = $page -> page_id;
					$favourite -> user_id = $viewer -> getIdentity();
					$favourite -> save();
					$page -> favourite_count = $page -> favourite_count + 1;
					$page -> save();
					$db -> commit();
				}
				echo Zend_Json::encode(array('success' => 1));
			}
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function unFavouriteAjaxAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page_id = (int)$this -> _getParam('pageId');
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('pages', 'ynwiki') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if ($page)
			{
				if (!$page -> checkFavourite())
				{
					$favourite = Engine_Api::_() -> ynwiki() -> getFavourite($viewer -> getIdentity(), $page_id);
					$favourite -> delete();
					$page -> favourite_count = $page -> favourite_count - 1;
					$page -> save();
					$db -> commit();
				}
				echo Zend_Json::encode(array('success' => 1));
			}
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function unFavouriteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page_id = (int)$this -> _getParam('pageId');
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('pages', 'ynwiki') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if ($page)
			{
				if (!$page -> checkFavourite())
				{
					$favourite = Engine_Api::_() -> ynwiki() -> getFavourite($viewer -> getIdentity(), $page_id);
					$favourite -> delete();
					$page -> favourite_count = $page -> favourite_count - 1;
					$page -> save();
					$db -> commit();
				}
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array($this -> view -> translate('Unfavourite successfully.'))
				));
			}
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function manageFollowAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $form = new Ynwiki_Form_Search();
		// Process form
		$form -> isValid($this -> _getAllParams());
		$values = $form -> getValues();
		$this -> view -> formValues = array_filter($values);
		$values['user_id'] = $viewer -> getIdentity();
		$values['follow'] = true;
		$this -> view -> pages = $paginator = Engine_Api::_() -> ynwiki() -> getPagesPaginator($values);
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> ynwiki_page;
		$paginator -> setItemCountPerPage($items_per_page);
		$paginator -> setCurrentPageNumber($values['page']);

		$t_table = Engine_Api::_() -> getDbtable('tags', 'core');
		$tm_table = Engine_Api::_() -> getDbtable('tagMaps', 'core');
		$p_table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$tName = $t_table -> info('name');
		$tmName = $tm_table -> info('name');
		$pName = $p_table -> info('name');

		$filter_select = $tm_table -> select() -> from($tmName, "$tmName.*") -> setIntegrityCheck(false) -> joinLeft($pName, "$pName.page_id = $tmName.resource_id", '') -> where("$pName.draft = 0");

		$select = $t_table -> select() -> from($tName, array(
			"$tName.*",
			"Count($tName.tag_id) as count"
		));
		$select -> joinLeft($filter_select, "t.tag_id = $tName.tag_id", '');
		$select -> order("$tName.text");
		$select -> group("$tName.text");
		$select -> where("t.resource_type = ?", "ynwiki_page");
		$this -> view -> tags = $t_table -> fetchAll($select);

	}

	public function manageFavouriteAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		//if( !$this->_helper->requireAuth()->setAuthParams('ynwiki_page', null, 'follow')->isValid()) return;
		$this -> view -> form = $form = new Ynwiki_Form_Search();
		// Process form
		$form -> isValid($this -> _getAllParams());
		$values = $form -> getValues();
		$this -> view -> formValues = array_filter($values);
		$values['user_id'] = $viewer -> getIdentity();
		$values['favourite'] = true;
		$this -> view -> pages = $paginator = Engine_Api::_() -> ynwiki() -> getPagesPaginator($values);
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> ynwiki_page;
		$paginator -> setItemCountPerPage($items_per_page);
		$paginator -> setCurrentPageNumber($values['page']);

		$t_table = Engine_Api::_() -> getDbtable('tags', 'core');
		$tm_table = Engine_Api::_() -> getDbtable('tagMaps', 'core');
		$p_table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$tName = $t_table -> info('name');
		$tmName = $tm_table -> info('name');
		$pName = $p_table -> info('name');

		$filter_select = $tm_table -> select() -> from($tmName, "$tmName.*") -> setIntegrityCheck(false) -> joinLeft($pName, "$pName.page_id = $tmName.resource_id", '') -> where("$pName.draft = 0");

		$select = $t_table -> select() -> from($tName, array(
			"$tName.*",
			"Count($tName.tag_id) as count"
		));
		$select -> joinLeft($filter_select, "t.tag_id = $tName.tag_id", '');
		$select -> order("$tName.text");
		$select -> group("$tName.text");
		$select -> where("t.resource_type = ?", "ynwiki_page");
		$this -> view -> tags = $t_table -> fetchAll($select);
	}

	public function moreSpaceAction()
	{
		$this -> view -> form = $form = new Ynwiki_Form_Search();
		// Process form
		$form -> isValid($this -> _getAllParams());
		$values = $form -> getValues();
		$this -> view -> formValues = array_filter($values);
		$values['space'] = true;
		$this -> view -> pages = $paginator = Engine_Api::_() -> ynwiki() -> getPagesPaginator($values);
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> ynwiki_page;
		$paginator -> setItemCountPerPage($items_per_page);
		$paginator -> setCurrentPageNumber($values['page']);

		$t_table = Engine_Api::_() -> getDbtable('tags', 'core');
		$tm_table = Engine_Api::_() -> getDbtable('tagMaps', 'core');
		$p_table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$tName = $t_table -> info('name');
		$tmName = $tm_table -> info('name');
		$pName = $p_table -> info('name');

		$filter_select = $tm_table -> select() -> from($tmName, "$tmName.*") -> setIntegrityCheck(false) -> joinLeft($pName, "$pName.page_id = $tmName.resource_id", '') -> where("$pName.draft = 0");

		$select = $t_table -> select() -> from($tName, array(
			"$tName.*",
			"Count($tName.tag_id) as count"
		));
		$select -> joinLeft($filter_select, "t.tag_id = $tName.tag_id", '');
		$select -> order("$tName.text");
		$select -> group("$tName.text");
		$select -> where("t.resource_type = ?", "ynwiki_page");
		$this -> view -> tags = $t_table -> fetchAll($select);
	}

	public function moveLocationAction()
	{
		// Check permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> page_id = $page_id = $this -> _getParam('pageId');
		$page = Engine_Api::_() -> getItem('ynwiki_page', $page_id);
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'edit') -> isValid())
		{
			return;
		}
		$this -> view -> page = $page;
		if (!$page)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> form = $form = new Ynwiki_Form_Location();

		if (!$this -> getRequest() -> getPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		//Process
		$values = $form -> getValues();
		$db = Engine_Api::_() -> getDbtable('pages', 'ynwiki') -> getAdapter();
		$db -> beginTransaction();
		$page_parent = Engine_Api::_() -> getItem('ynwiki_page', $values['toValues']);
		if ($page_parent -> page_id == $page -> parent_page_id)
		{
			$form -> getElement('to') -> addError('Not change, please select other page');
			$form -> getElement('toValues') -> setValue("");
			return;
		}
		$check_move = $page_parent -> authorization() -> isAllowed($viewer, 'edit');
		if ($page_parent -> checkParent($page -> page_id) || !$check_move)
		{
			$form -> getElement('to') -> addError('You can not move to that page');
			$form -> getElement('toValues') -> setValue("");
			return;
		}
		try
		{
			$page -> parent_page_id = $page_parent -> page_id;
			$increLevel = $page_parent -> level - $page -> level + 1;
			$page -> moveChilds($increLevel);
			$page -> save();

			if ($page -> parent_type == 'group')
			{
				$group = Engine_Api::_() -> getItem($page -> parent_type, $page -> parent_id);
				$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $group, 'advgroup_wiki_move');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $page);
				}
			}
			else
			{
				$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $page, 'ynwiki_move');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $page);
				}
			}
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$userFollows = $page -> getUserFollows();
			foreach ($userFollows as $follow)
			{
				if ($follow -> user_id != $viewer -> getIdentity())
				{
					$userFollow = Engine_Api::_() -> getItem('user', $follow -> user_id);
					$notificationSettingsTable = Engine_Api::_() -> getDbtable('notificationSettings', 'activity');

					if ($notificationSettingsTable -> checkEnabledNotification($userFollow, 'ynwiki_move') && !empty($userFollow -> email))
					{
						$notifyApi -> addNotification($userFollow, $viewer, $page, 'ynwiki_move', array('label' => $page -> title));
					}
				}
			}
			$db -> commit();
		}
		catch(Exception $e)
		{
			$db -> rollback();
			throw $e;
		}
		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'pageId' => $page -> page_id,
			'slug' => $page -> getSlug()
		), 'ynwiki_general', true);
	}

	public function attachAction()
	{
		// Check permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));

		if ($page)
		{
			Engine_Api::_() -> core() -> setSubject($page);
		}

		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'edit') -> isValid())
		{
			return;
		}

		$this -> view -> form = $form = new Ynwiki_Form_Attach();
		if (!$viewer -> getIdentity())
		{
			$this -> view -> can_rate = $can_rate = 0;
		}
		else
		{
			$this -> view -> can_rate = $can_rate = Engine_Api::_() -> ynwiki() -> canRate($page, $viewer -> getIdentity());
		}
		$this -> view -> page = $page;
		$this -> view -> viewer = $viewer;
		if (!$this -> getRequest() -> getPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		//Process
		//determine filename and extension
		$info = pathinfo($form -> attach -> getFileName(null, false));
		$filename = $info['filename'];
		$ext = $info['extension'] ? "." . $info['extension'] : "";
		//filter for renaming.. prepend with current time
		$form -> attach -> addFilter(new Zend_Filter_File_Rename( array(
			"target" => time() . $filename . $ext,
			"overwrite" => true
		)));
		$form -> getValue('attach');
		$values = $form -> getValues();
		if (!empty($values['attach']))
		{
			$name = time() . $filename . $ext;
			$title = $filename . $ext;
			$page -> saveAttach($name, $title);
		}

		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'pageId' => $page -> page_id,
			'slug' => $page -> getSlug()
		), 'ynwiki_general', true);
	}

	public function suggestAction()
	{
		$data = array();
		$table = Engine_Api::_() -> getItemTable('ynwiki_page');
		$select = $table -> select();

		if (0 < ($limit = (int)$this -> _getParam('limit', 10)))
		{
			$select -> limit($limit);
		}
		$page_id = $this -> _getParam('pageId');
		if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value'))))
		{
			$select -> where('title LIKE ?', '%' . $text . '%');
		}

		foreach ($select->getTable()->fetchAll($select) as $page)
		{
			$label = $page -> title;
			if ($page -> parent_page_id > 0)
			{
				$parent_page = Engine_Api::_() -> getItem('ynwiki_page', $page -> parent_page_id);
				if ($parent_page)
					$label = "(" . $parent_page -> title . ") " . $page -> title;
			}
			$data[] = array(
				'type' => 'ynwiki_page',
				'id' => $page -> getIdentity(),
				'label' => $label,
				'photo' => $this -> view -> itemPhoto($page -> getOwner(), 'thumb.icon'),
				'url' => $page -> getHref(),
			);
		}
		return $this -> _helper -> json($data);
	}

	public function suggestUserAction()
	{
		$data = array();
		$table = Engine_Api::_() -> getItemTable('user');
		$select = $table -> select();

		if (0 < ($limit = (int)$this -> _getParam('limit', 10)))
		{
			$select -> limit($limit);
		}
		if (null !== ($text = $this -> _getParam('search', $this -> _getParam('text'))))
		{
			$select -> where('displayname LIKE ?', '%' . $text . '%');
		}

		foreach ($select->getTable()->fetchAll($select) as $user)
		{
			$data[] = array(
				'type' => 'user',
				'id' => $user -> getIdentity(),
				'label' => $user -> displayname,
				'photo' => $this -> view -> itemPhoto($user, 'thumb.icon'),
				'url' => $user -> getHref(),
			);
		}
		return $this -> _helper -> json($data);
	}

	public function deleteAttachAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$attach = Engine_Api::_() -> getItem('ynwiki_attachment', $this -> _getParam('attachId'));
		if (!$attach)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$page = Engine_Api::_() -> getItem('ynwiki_page', $attach -> page_id);
		if (!$this -> _helper -> requireAuth() -> setAuthParams($page, $viewer, 'edit') -> isValid())
		{
			return;
		}
		if (!$page)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$file = Engine_Api::_() -> getItem('storage_file', $attach -> file_id);
		$file -> delete();
		$attach -> delete();
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Delete attach successfully.'))
		));
	}

	public function compareVersionsAction()
	{
		$post = $_POST;
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if (!$page)
			return $this -> _helper -> requireAuth -> forward();
		$this -> view -> page = $page;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			$this -> view -> can_rate = $can_rate = 0;
		}
		else
		{
			$this -> view -> can_rate = $can_rate = Engine_Api::_() -> ynwiki() -> canRate($page, $viewer -> getIdentity());
		}
		$granularity = 3;
		$ids = $this -> _getParam('ids', null);
		$versions = $this -> _getParam('versions', null);
		$from_text = "";
		$to_text = "";
		if ($ids != "" || $versions != "")
		{
			$ids_array = explode(",", $ids);
			$versions_array = explode(",", $versions);
			$id1 = $ids_array[0];
			$id2 = $ids_array[1];
			$version1 = $versions_array[0];
			$version2 = $versions_array[1];
			if ($id1 > $id2)
			{
				$version_from = Engine_Api::_() -> getItem('ynwiki_revision', $id2);
				$version_to = Engine_Api::_() -> getItem('ynwiki_revision', $id1);
			}
			else
			{
				$version_from = Engine_Api::_() -> getItem('ynwiki_revision', $id1);
				$version_to = Engine_Api::_() -> getItem('ynwiki_revision', $id2);
			}
			$this -> view -> old_version = $version_from;
			$this -> view -> new_version = $version_to;
			if ($version1 > $version2)
			{
				$this -> view -> old = $version2;
				$this -> view -> new = $version1;
			}
			else
			{
				$this -> view -> old = $version1;
				$this -> view -> new = $version2;
			}
			$from_text = $version_from -> body;
			$to_text = $version_to -> body;
		}
		else
		{
			$paginator = $page -> getRevisions();
			$this -> view -> old = $paginator -> getTotalItemCount() - 1;
			$this -> view -> new = $paginator -> getTotalItemCount();
			$version_from = $page -> getLastUpdated(1);
			$version_to = Engine_Api::_() -> getItem('ynwiki_revision', $page -> revision_id);
			$this -> view -> old_version = $version_from;
			$this -> view -> new_version = $version_to;
			$from_text = $version_from -> body;
			$to_text = $version_to -> body;
		}
		$diff = '';
		$granularityStacks = array(
			FineDiff::$paragraphGranularity,
			FineDiff::$sentenceGranularity,
			FineDiff::$wordGranularity,
			FineDiff::$characterGranularity
		);
		$diff = new FineDiff($from_text, $to_text, $granularityStacks[$granularity]);
		$rendered_diff = $diff -> renderDiffToHTML();
		//$rendered_diff =  str_replace('&amp;','&', $rendered_diff);
		$rendered_diff = str_replace(array(
			"&lt;",
			"&gt;",
			'&amp;',
			'&#039;',
			'&quot;',
			'&lt;',
			'&gt;'
		), array(
			"<",
			">",
			'&',
			'\'',
			'"',
			'<',
			'>'
		), htmlspecialchars_decode($rendered_diff, ENT_NOQUOTES));
		$this -> view -> rendered_diff = $rendered_diff;
		$this -> view -> granularity = $granularity;
		$start_time = gettimeofday(true);
		$edits = $diff -> getOps();
		$exec_time = gettimeofday(true) - $start_time;
		$rendering_time = gettimeofday(true) - $start_time;
		$diff_len = strlen($diff -> getOpcodes());

		$this -> view -> exec_time = $exec_time;
		$this -> view -> endering_time = $rendering_time;
		$this -> view -> diff_len = $diff_len;
	}

	public function uploadPhotoAction()
	{
		// Disable layout
		$this -> _helper -> layout -> disableLayout();

		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$destination = "public/ynwiki/";
		if (!is_dir($destination))
		{
			mkdir($destination);
		}
		$destination = "public/ynwiki/" . $user_id . "/";
		if (!is_dir($destination))
		{
			mkdir($destination);
		}
		$upload = new Zend_File_Transfer_Adapter_Http();
		$upload -> setDestination($destination);
		$file_info = pathinfo($upload -> getFileName('userfile', false));

		$fullFilePath = $destination . time() . '.' . $file_info['extension'];
		$image = Engine_Image::factory();
		$image -> open($_FILES['userfile']['tmp_name']) -> resize(720, 720) -> write($fullFilePath);

		$this -> view -> status = true;
		$this -> view -> name = $_FILES['userfile']['name'];
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$url = $request -> getScheme() . '://' . $request -> getHttpHost() . $request -> getBaseUrl();
		$this -> view -> photo_url = $url . "/" . $fullFilePath;
	}

	public function reportAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$page = Engine_Api::_() -> getItem('ynwiki_page', $this -> _getParam('pageId'));
		if (!$page || !$viewer -> getIdentity())
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> form = $form = new Ynwiki_Form_Report();
		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getDbtable('reports', 'Ynwiki');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		$values = $form -> getValues();
		try
		{
			// Create report
			$values = array_merge($form -> getValues(), array(
				'user_id' => $viewer -> getIdentity(),
				'page_id' => $page -> page_id
			));

			$report = $table -> createRow();
			$report -> setFromArray($values);
			$report -> creation_date = date('Y-m-d H:i:s');
			$report -> modified_date = date('Y-m-d H:i:s');
			$report -> save();

			//Send message to admin
			$content = $values['content'];
			$type = $values['type'];
			$users = Ynwiki_Api_Core::getAllAdmins();
			if ($users)
			{
				foreach ($users as $user)
				{
					if ($user -> getIdentity() != $viewer -> getIdentity())
					{
						// Create conversation
						$conversation = Engine_Api::_() -> getItemTable('messages_conversation') -> send($viewer, $user, $type, $content, null);

						// Send notifications
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $conversation, 'message_new');

						// Increment messages counter
						Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('messages.creations');
					}
				}
			}

			// Commit
			$db -> commit();

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Report successfully.'))
			));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

}
