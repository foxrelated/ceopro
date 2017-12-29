<?php
class Advgroup_PollController extends Core_Controller_Action_Standard {
	public function init() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($poll_id = (int)$this -> _getParam('poll_id')) && null !== ($poll = Engine_Api::_() -> getItem('advgroup_poll', $poll_id))) {
				Engine_Api::_() -> core() -> setSubject($poll);
			} else if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id))) {
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
	}

	public function listAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_Poll_Search;
		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid()) {
			return;
		}
		//Params For Searhing
		$params['browse'] = 1;
		$params['search'] = $this -> _getParam('search', '');
		$params['closed'] = $this -> _getParam('closed', '');
		$params['order'] = $this -> _getParam('order', 'recent');
		$params['user_id'] = null;
		$params['group_id'] = $group -> group_id;

		$form -> populate(array('search' => $params['search'], 'closed' => $params['closed'], 'order' => $params['order'], 'page' => $this -> _getParam('page', 1)));
		$this -> view -> formValues = $form -> getValues();
		//Create Checking
//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if ($parent_group -> authorization() -> isAllowed(null, 'poll')) {
//				$canCreate = $group -> authorization() -> isAllowed(null, 'poll');
//			} else {
//				$canCreate = false;
//			}
//		} else {
			$canCreate = $group -> authorization() -> isAllowed(null, 'poll');
//		}
		
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'poll');
		if ($canCreate && $levelCreate) {
			$this -> view -> canCreate = true;
		} else {
			$this -> view -> canCreate = false;
		}

		//Poll List
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_poll') -> getPollsPaginator($params);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'), 1);
		$paginator -> setItemCountPerPage(10);
	}

	public function manageAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
			return $this -> renderScript("_error.tpl");
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_Poll_Search;
		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid()) {
			return;
		}
		//Params For Searhing
		$params['search'] = $this -> _getParam('search', '');
		$params['closed'] = $this -> _getParam('closed', '');
		$params['order'] = $this -> _getParam('order', 'recent');
		$params['user_id'] = $viewer -> getIdentity();
		$params['group_id'] = $group -> group_id;

		$form -> populate(array('search' => $params['search'], 'closed' => $params['closed'], 'order' => $params['order'], 'page' => $this -> _getParam('page', 1)));
		$this -> view -> formValues = $form -> getValues();

		//Create Checking
//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if ($parent_group -> authorization() -> isAllowed(null, 'poll')) {
//				$canCreate = $group -> authorization() -> isAllowed(null, 'poll');
//			} else {
//				$canCreate = false;
//			}
//		} else {
			$canCreate = $group -> authorization() -> isAllowed(null, 'poll');
//		}

		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'poll');
		if ($canCreate && $levelCreate)
			$this -> view -> canCreate = true;
		else
			$this -> view -> canCreate = false;

		//Poll List
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_poll') -> getPollsPaginator($params);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'), 1);
		$paginator -> setItemCountPerPage(10);
	}

	public function editAction() {
		//Checking viewer
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//Get Poll
		$poll_id = $this -> _getParam('poll_id', 0);
		$poll = Engine_Api::_() -> getItem('advgroup_poll', $poll_id);
		if (!$poll)
			return $this -> renderScript('_error.tpl');
		
		//Get group
		$group = $poll->getParent();
		if(!$group)
		{
			return $this->_helper->requireSubject->forward();
		}
		
		//Edit condition
		$canEdit = $group -> authorization() -> isAllowed(null, 'poll.edit');

		if (!$canEdit && !$poll -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentParent($viewer)) {
			return $this -> renderScript('_private.tpl');
		}

		$this -> view -> form = $form = new Advgroup_Form_Poll_Edit();
		$form -> removeElement('options');

		$form -> populate(array('title' => $poll -> title, 'description' => $poll -> description, 'search' => $poll -> search));

		// Check method/valid
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try {
			$values = $form -> getValues();
			$poll -> setFromArray($values);
			$poll -> save();

			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The poll has been successfully edited.');
		return $this -> _forward('success', 'utility', 'core', array('parentRefresh' => true, 'messages' => Array($this -> view -> message)));
	}

	public function deleteAction() {

		//Checking viewer
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//Get Poll
		$poll_id = $this -> _getParam('poll_id', 0);
		$poll = Engine_Api::_() -> getItem('advgroup_poll', $poll_id);
		if (!$poll)
			return $this -> renderScript('_error.tpl');

		//Edit condition
		$group = $poll -> getParent();
		$canEdit = $group -> authorization() -> isAllowed(null, 'poll.edit');
		if (!$canEdit && !$poll -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentParent($viewer)) {
			return $this -> renderScript('_private.tpl');
		}

		$this -> view -> form = $form = new Advgroup_Form_Poll_Delete;
		// Check method
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$db = $poll -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			$poll -> delete();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The poll has been successfully deleted.');
		return $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'poll', 'action' => 'manage', 'subject' => $group -> getGuid()), 'group_extended', true), 'messages' => Array($this -> view -> message)));
	}

	public function closeAction() {
		//Check viewer
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//Get Poll
		$poll_id = $this -> _getParam('poll_id', 0);
		$poll = Engine_Api::_() -> getItem('advgroup_poll', $poll_id);
		if (!$poll)
			return $this -> renderScript('_error.tpl');
		$this -> view -> poll = $poll;

		//Edit condition
		$group = $poll -> getParent();
		$canEdit = $group -> authorization() -> isAllowed(null, 'poll.edit');
		if (!$canEdit && !$poll -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentParent($viewer)) {
			return $this -> renderScript('_private.tpl');
		}

		if ($this -> _getParam('closed'))
			$this -> view -> form = $form = new Advgroup_Form_Poll_Close;
		else
			$this -> view -> form = $form = new Advgroup_Form_Poll_Unclose;

		// Check method
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		//Processing
		$table = $poll -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$poll -> closed = (bool)$this -> _getParam('closed');
			$poll -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		if ($poll -> closed)
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The poll has been closed.');
		else
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The poll has been reopened.');
		return $this -> _forward('success', 'utility', 'core', array('parentRefresh' => true, 'messages' => Array($this -> view -> message)));
	}

	public function createAction() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject('group');
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if (!$parent_group -> authorization() -> isAllowed(null, 'poll')) {
//				return $this -> _helper -> requireAuth -> forwards();
//			} else if (!$group -> authorization() -> isAllowed(null, 'poll')) {
//				return $this -> _helper -> requireAuth -> forwards();
//			}
//		} else
      if (!$group -> authorization() -> isAllowed(null, 'poll')) {
			return $this -> _helper -> requireAuth -> forwards();
		}

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Poll_Create();

		$this -> view -> options = array();
		$this -> view -> maxOptions = $max_options = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.pollmaxoptions', 15);
		$this -> view -> form = $form = new Advgroup_Form_Poll_Create();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Check options
		$options = (array)$this -> _getParam('optionsArray');
		$options = array_filter(array_map('trim', $options));
		$options = array_slice($options, 0, $max_options);
		$this -> view -> options = $options;
		if (empty($options) || !is_array($options) || count($options) < 2) {
			return $form -> addError('You must provide at least two possible answers.');
		}
		foreach ($options as $index => $option) {
			if (strlen($option) > 80) {
				$options[$index] = Engine_String::substr($option, 0, 80);
			}
		}

		// Process
		$pollTable = Engine_Api::_() -> getItemTable('advgroup_poll');
		$pollOptionsTable = Engine_Api::_() -> getDbtable('pollOptions', 'advgroup');
		$db = $pollTable -> getAdapter();
		$db -> beginTransaction();

		try {
			$values = $form -> getValues();
			$values['user_id'] = $viewer -> getIdentity();
			$values['group_id'] = $group -> group_id;

			// Create poll
			$poll = $pollTable -> createRow();
			$poll -> setFromArray($values);
			$poll -> save();

			// Create options
			$censor = new Engine_Filter_Censor();
			$html = new Engine_Filter_HtmlSpecialChars();

			foreach ($options as $option) {
				$option = $censor -> filter($html -> filter($option));
				$pollOptionsTable -> insert(array('poll_id' => $poll -> getIdentity(), 'poll_option' => $option, ));
			}
			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollback();
			throw $e;
		}
		// Process privacy
		$auth = Engine_Api::_() -> authorization() -> context;

		$roles = array('officer', 'member', 'registered', 'everyone');
		if (empty($values['auth_comment'])) {
			$values['auth_comment'] = 'registered';
		}
		$commentMax = array_search($values['auth_comment'], $roles);

		$officerList = $group -> getOfficerList();

		foreach ($roles as $i => $role) {
			if ($role === 'officer') {
				$role = $officerList;
			}
			$auth -> setAllowed($poll, $role, 'comment', ($i <= $commentMax));
		}

		// Process activity
		$db = Engine_Api::_() -> getDbTable('polls', 'advgroup') -> getAdapter();
		$db -> beginTransaction();
		try {
			$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity(Engine_Api::_() -> user() -> getViewer(), $group, 'advgroup_poll_new', $poll);
			if ($action) {
				Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $poll);
			}
			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollback();
			throw $e;
		}
		$this -> _helper -> redirector -> gotoRoute(array('controller' => 'poll', 'action' => 'manage', 'subject' => $group -> getGuid()), 'group_extended', true);
	}

	public function viewAction() {
		$poll_id = $this -> _getParam('poll_id', 0);
		$poll = Engine_Api::_() -> getItem('advgroup_poll', $poll_id);

		$viewer = Engine_Api::_() -> user() -> getViewer();
		//Check poll exists
		if (!$poll)
			return $this -> renderScript('_error.tpl');
		$this -> view -> group = $group = $poll -> getParent();

		if ($group -> is_subgroup) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			}
		} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		$this -> view -> poll = $poll;
		$this -> view -> owner = $owner = $poll -> getOwner();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> pollOptions = $poll -> getOptions();
		$this -> view -> hasVoted = $poll -> viewerVoted();
		$this -> view -> showPieChart = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.pollshowpiechart', false);
		$this -> view -> canVote = $group -> authorization() -> isAllowed($viewer, 'comment');
		$this -> view -> canChangeVote = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.pollcanchangevote', false);

		$canEdit = $group -> authorization() -> isAllowed(null, 'poll.edit');

		if (!$canEdit && !$poll -> isOwner($viewer) && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer)) {
			$this -> view -> canEdit = false;
		} else {
			$this -> view -> canEdit = true;
		}
		if (!$owner -> isSelf($viewer)) {
			$poll -> view_count++;
			$poll -> save();
		}
	}

	public function voteAction() {

		// Check viewer
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Check method
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		//Get Option And Vote Change Allowance Checking
		$option_id = $this -> _getParam('option_id');
		$canChangeVote = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.pollcanchangevote', false);

		//Get Poll
		$poll_id = $this -> _getParam('poll_id', 0);
		$poll = Engine_Api::_() -> getItem('advgroup_poll', $poll_id);

		//Poll Checking Condition
		if (!$poll) {
			$this -> view -> success = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('This poll does not seem to exist anymore.');
			return;
		}

		$group = $poll -> getParent();
		if (!$group) {
			$this -> view -> success = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('This poll does not seem to belong to anygroup.');
			return;
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'comment') -> isValid()) {
			return;
		}

		if ($poll -> closed) {
			$this -> view -> success = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('This poll is closed.');
			return;
		}

		if ($poll -> hasVoted($viewer) && !$canChangeVote) {
			$this -> view -> success = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('You have already voted on this poll, and are not permitted to change your vote.');
			return;
		}

		//Process Voting
		$db = Engine_Api::_() -> getDbtable('polls', 'advgroup') -> getAdapter();
		$db -> beginTransaction();

		try {
			$poll -> vote($viewer, $option_id);

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}

		$this -> view -> success = true;
		$pollOptions = array();
		foreach ($poll->getOptions()->toArray() as $option) {
			$option['votesTranslated'] = $this -> view -> translate(array('%s vote', '%s votes', $option['votes']), $this -> view -> locale() -> toNumber($option['votes']));
			$pollOptions[] = $option;
		}
		$this -> view -> pollOptions = $pollOptions;
		$this -> view -> votes_total = $poll -> vote_count;
	}

}
?>
