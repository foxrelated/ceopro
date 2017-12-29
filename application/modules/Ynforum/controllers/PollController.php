<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     LuanND
 */
class Ynforum_PollController extends Core_Controller_Action_Standard {

	public function init() {
		if (!Engine_Api::_() -> hasItemType('poll')) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		if (0 !== ($forum_id = (int)$this -> _getParam('forum_id')) && null !== ($forum = Engine_Api::_() -> getItem('ynforum_forum', $forum_id))) {
			if (!Engine_Api::_() -> core() -> hasSubject($forum -> getType())) {
				Engine_Api::_() -> core() -> setSubject($forum);
			}
			$this -> view -> forum = $forum;
			$this -> view -> navigationForums = $forum -> getForumNavigations();
			$list = $forum -> getModeratorList();
			$moderators = $list -> getAllChildren();
			$arr_temp = array();
			foreach ($moderators as $moderator) {
				if ($moderator -> getIdentity())
					$arr_temp[] = $moderator;
			}
			$this -> view -> moderators = $arr_temp;

			$categoryTable = Engine_Api::_() -> getItemTable('ynforum_category');
			$cats = $categoryTable -> fetchAll($categoryTable -> select() -> order('order ASC'));
			$categories = array();
			foreach ($cats as $cat) {
				$categories[$cat -> getIdentity()] = $cat;
			}
			$curCat = $categories[$forum -> category_id];
			$linkedCategories = array();
			do {
				$linkedCategories[] = $curCat;
				if (!$curCat -> parent_category_id) {
					break;
				}
				$curCat = $categories[$curCat -> parent_category_id];
			} while (true);
			$this -> view -> linkedCategories = $linkedCategories;
		}
	}

	public function editAction() {

		if (!$this -> _getParam('poll_id')) {
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$poll = Engine_Api::_() -> getItem('poll', $this -> _getParam('poll_id'));
		//poll auth
		if (!$this -> _helper -> requireAuth() -> setAuthParams($poll, $viewer, 'edit') -> isValid()) {
			return;
		}

		// Get form
	    $this->view->form = $form = new Poll_Form_Edit();
	    $form->removeElement('title');
	    $form->removeElement('description');
	    $form->removeElement('options');
		

		$form -> getElement('submit') -> setLabel("Save");

		// Prepare privacy
	    $auth = Engine_Api::_()->authorization()->context;
	    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
	
	    // Populate form with current settings
	    $form->search->setValue($poll->search);
	    foreach( $roles as $role ) {
	      if( 1 === $auth->isAllowed($poll, $role, 'view') ) {
	        $form->auth_view->setValue($role);
	      }
	      if( 1 === $auth->isAllowed($poll, $role, 'comment') ) {
	        $form->auth_comment->setValue($role);
	      }
	    }		

		// Check method/valid
	    if( !$this->getRequest()->isPost() ) {
	      return;
	    }
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	      return;
	    }
		
		// Process
		$pollTable = Engine_Api::_() -> getItemTable('poll');
		$pollOptionsTable = Engine_Api::_() -> getDbtable('options', 'poll');
		$db = $pollTable -> getAdapter();
		$db -> beginTransaction();

		try {
			$values = $form->getValues();

	      	// CREATE AUTH STUFF HERE
	      	if( empty($values['auth_view']) ) {
	        	$values['auth_view'] = array('everyone');
	      	}
	      	if( empty($values['auth_comment']) ) {
	        	$values['auth_comment'] = array('everyone');
	      	}
	
	      	$viewMax = array_search($values['auth_view'], $roles);
	      	$commentMax = array_search($values['auth_comment'], $roles);
	
	      	foreach( $roles as $i => $role ) {
	        	$auth->setAllowed($poll, $role, 'view', ($i <= $viewMax));
	        	$auth->setAllowed($poll, $role, 'comment', ($i <= $commentMax));
	      	}
	
	      	$poll->search = (bool) $values['search'];
	      	$poll->save();
	
	      	$db->commit();

			return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Successful')), 'layout' => 'default-simple', 'parentRefresh' => true, ));

		} catch( Exception $e ) {
			$db -> rollback();
			throw $e;
		}
	}

	public function manageAction() {
		if (!$this -> _helper -> requireSubject('forum') -> isValid()) {
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid()) {
			return;
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$forum -> checkPermission($viewer, 'forum', 'fpoll.edit')) {
			return $this -> _helper -> requireAuth() -> forward();
		}

		// get polls
		$pollTable = Engine_Api::_() -> getItemTable('poll');
		$pollTableName = $pollTable -> info('name');

		$select = $pollTable -> select() -> from($pollTableName, "$pollTableName.*") -> setIntegrityCheck(false);
		$select -> joinLeft("engine4_ynforum_highlights", "engine4_ynforum_highlights.item_id = $pollTableName.poll_id", "engine4_ynforum_highlights.*");
		$select -> where("$pollTableName.user_id = ?", $viewer -> getIdentity())
				-> where("engine4_ynforum_highlights.forum_id = ?", $forum -> getIdentity())
				-> where("engine4_ynforum_highlights.type = ?",'poll')
				-> order("$pollTableName.poll_id DESC");

		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function indexAction() {
		if (!$this -> _helper -> requireSubject('forum') -> isValid()) {
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid()) {
			return;
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		// get polls
		$pollTable = Engine_Api::_() -> getItemTable('poll');
		$pollTableName = $pollTable -> info('name');

		$select = $pollTable -> select() -> from($pollTableName, "$pollTableName.*") -> setIntegrityCheck(false);
		$select -> joinLeft("engine4_ynforum_highlights", "engine4_ynforum_highlights.item_id = $pollTableName.poll_id", "engine4_ynforum_highlights.*");
		$select -> where("engine4_ynforum_highlights.forum_id = ?", $forum -> getIdentity())
				-> where("engine4_ynforum_highlights.type = ?",'poll')
				-> order("$pollTableName.poll_id DESC");

		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function highlightAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		if (!$this -> _helper -> requireSubject('forum') -> isValid()) {
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid()) {
			return;
		}
		if (!$forum -> checkPermission($viewer, 'forum', 'fpoll.create')) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		if (!$this -> getRequest() -> isPost())
			return;

		$id = $this -> getRequest() -> getPost('poll_id', null);
		$table = Engine_Api::_() -> getDbTable('highlights', 'ynforum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$select = $table -> select() -> where("forum_id = ?", $forum -> getIdentity()) -> where('item_id = ?', $id) -> where("type = 'poll'") -> limit(1);
			$row = $table -> fetchRow($select);
			if ($row) {
				$row -> highlight = !$row -> highlight;
				$this -> view -> enabled = $row -> highlight;
				$row -> save();
			} else {
				$row = $table -> createRow();
				$row -> setFromArray(array('forum_id' => $forum -> getIdentity(), 'item_id' => $id, 'user_id' => $viewer -> getIdentity(), 'highlight' => 1, 'type' => 'poll'));
				$row -> save();
				$this -> view -> enabled = 1;
			}
			$db -> commit();
			$this -> view -> success = true;
		} catch (Exception $e) {
			$db -> rollback();
			$this -> view -> success = false;
		}
	}

	public function createAction() {
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('forum') -> isValid()) {
			return;
		}
		$forum = Engine_Api::_() -> core() -> getSubject();
		if (!$this -> _helper -> requireAuth -> setAuthParams($forum, null, 'view') -> isValid()) {
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams('poll', null, 'create') -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$parent_id = $this -> _getParam('parent_id', $this -> _getParam('forum_id'));

		if (!$forum -> checkPermission($viewer, 'forum', 'fpoll.create')) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		$_SESSION['ynforum']['parent_id'] = $parent_id;
		
		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'create', 'parent_type' => 'forum', 'subject_id' => $parent_id), 'poll_general', true);
	}

	public function closeAction() {
		if (!$this -> _getParam('poll_id')) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$poll = Engine_Api::_() -> getItem('poll', $this -> _getParam('poll_id'));
		//poll auth
		if (!$this -> _helper -> requireAuth() -> setAuthParams($poll, $viewer, 'edit') -> isValid()) {
			return;
		}

		// @todo convert this to post only

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
		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Successful')), 'layout' => 'default-simple', 'parentRefresh' => true, ));

	}

	public function deleteAction() {
		if (!$this -> _getParam('poll_id')) {
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$poll = Engine_Api::_() -> getItem('poll', $this -> _getParam('poll_id'));
		//poll auth
		if (!$this -> _helper -> requireAuth() -> setAuthParams($poll, $viewer, 'delete') -> isValid()) {
			return;
		}

		$this -> view -> form = $form = new Poll_Form_Delete();

		if (!$poll) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Poll doesn't exist or not authorized to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
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
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your poll has been deleted.');

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _(Array($this -> view -> message))), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

}
