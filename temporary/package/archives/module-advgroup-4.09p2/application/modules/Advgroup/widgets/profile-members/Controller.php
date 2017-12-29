<?php

class Advgroup_Widget_ProfileMembersController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {

        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');

        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->group = $group = Engine_Api::_()->core()->getSubject('group');
        if ($group->is_subgroup && !$group->isParentGroupOwner($viewer)) {
            $parent_group = $group->getParentGroup();
            if (!$parent_group->authorization()->isAllowed($viewer, "view")) {
                return $this->setNoRender();
            } else if (!$group->authorization()->isAllowed($viewer, "view")) {
                return $this->setNoRender();
            }
        } else if (!$group->authorization()->isAllowed($viewer, "view")) {
            return $this->setNoRender();
        }

        // Get params
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->search = $search = $this->_getParam('search');
        $this->view->search_type = $search_type = $this->_getParam('search_type');
        $this->view->blacklist_enable = $blacklist_enable = $this->_getParam('blacklist_enable', false);
        $this->view->waiting = $waiting = $this->_getParam('waiting', false);

        // Prepare data
        $this->view->list = $list = $group->getOfficerList();
		$blacklist = Engine_Api::_()->getDbtable('blacklists', 'advgroup');
		// get viewer
		if ($viewer->getIdentity() && ($group->isOwner($viewer) || $list->has($viewer) || $group->isParentGroupOwner($viewer))) {

            // get waiting
            $this->view->waitingMembers = $waitingMembers = Zend_Paginator::factory($group->membership()->getMembersObjectSelect(false));
            if ($search) {
                if ($search_type == 'waiting') {
                    $select = $group->membership()->getMembersObjectSelect(false);
                    $select->where('displayname LIKE ?', '%' . $search . '%');
                    $this->view->waitingMembers = $waitingMembers = Zend_Paginator::factory($select);
                    $waiting = true;
                }
            }

            //get black list
            if ($blacklist->getBlackListMembers($group->getIdentity())) {
                $this->view->blacklistMembers = $blacklistMembers = Zend_Paginator::factory($blacklist->getBlackListMembers($group->getIdentity()));
            }

            if ($search) {
                if ($search_type == 'blacklist') {
                    $this->view->blacklistMembers = $blacklistMembers = Zend_Paginator::factory($blacklist->getBlackListMembers($group->getIdentity(), $search));
                    $blacklist_enable = true;
                }
            }
        }
	
		// if not showing waiting members, get full members or blacklist
		$select = $group->membership()->getMembersObjectSelect();
		if ($search) {
            if ($search_type == 'approved')
                $select->where('displayname LIKE ?', '%' . $search . '%');
        }
		$this->view->fullMembers = $fullMembers = Zend_Paginator::factory($select);

		// if showing waiting members, or no full members, blacklist members
				
		if (($blacklist_enable)) {

            $this->view->members = $paginator = $blacklistMembers;
            $this->view->blacklist_enable = $blacklist_enable = true;

        } elseif (($viewer->getIdentity() && ($group->isOwner($viewer) || $list->has($viewer) || $group->isParentGroupOwner($viewer))) && ($waiting || ($fullMembers->getTotalItemCount() <= 0 && $search == ''))) {

            $this->view->members = $paginator = $waitingMembers;
            $this->view->waiting = $waiting = true;
        } else {
            $this->view->members = $paginator = $fullMembers;
            $this->view->waiting = $waiting = false;
        }

		// Set item count per page and current page number
		$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
		$paginator->setCurrentPageNumber($this->_getParam('page', $page));

		// Do not render if nothing to show and no search
		if ($paginator->getTotalItemCount() <= 0 && '' == $search) {
            return $this->setNoRender();
        }

		// Add count to title if configured
		if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 && !$waiting) {
            $this->_childCount = $paginator->getTotalItemCount();
        }

		// Sub group

		$viewer = Engine_Api::_()->user()->getViewer();

		if (!Engine_Api::_()->core()->hasSubject('group')) {
            return $this->setNoRender();
        }
		$group = Engine_Api::_()->core()->getSubject('group');

		if (!$group->authorization()->isAllowed($viewer, "view")) {
            return $this->setNoRender();
        }

		$table = Engine_Api::_()->getItemTable('group');

		if ($group->is_subgroup) {
            $this->view->sub_mode = false;
            $select1 = $table->select()->where('group_id = ?', $group->parent_id);
        } else {
            $this->view->sub_mode = true;
            $select1 = $table->select()->where('parent_id = ?', $group->group_id);
        }

		$paginator1 = Zend_Paginator::factory($select1);
		$paginator1->setItemCountPerPage($this->_getParam('itemCountPerPage', 100));
		$paginator1->setCurrentPageNumber($this->_getParam('page', 1));
		
		if (count($paginator1) <= 0) {

        } else {
            $this->view->sub_groups = $paginator1;
        }

	}

    public function getChildCount()
    {
        return $this->_childCount;
    }

    public function removeAction()
    {
        echo $this->render('index');
    }

}
