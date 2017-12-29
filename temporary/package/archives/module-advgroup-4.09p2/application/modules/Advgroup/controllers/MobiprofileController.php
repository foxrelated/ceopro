<?php
class Advgroup_MobiprofileController extends Core_Controller_Action_Standard
{
	public function init()
	{
		// @todo this may not work with some of the content stuff in here, double-check
		$subject = null;
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			$id = $this -> _getParam('id');
			if (null !== $id)
			{
				$subject = Engine_Api::_() -> getItem('group', $id);
				if ($subject && $subject -> getIdentity())
				{
					Engine_Api::_() -> core() -> setSubject($subject);
				}
				else
					return;
			}
		}

		$this -> _helper -> requireSubject('group');
		$this -> _helper -> requireAuth() -> setNoForward() -> setAuthParams($subject, Engine_Api::_() -> user() -> getViewer(), 'view');
	}

	public function indexAction()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
			return $this -> renderScript("_error.tpl");
		$subject = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($subject -> is_subgroup && !$subject -> isParentGroupOwner($viewer))
		{
			$parent_group = $subject -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}
		// Increment view count
		if (!$subject -> getOwner() -> isSelf($viewer))
		{
			$subject -> view_count++;
			$subject -> save();
		}

		// Get styles
		$table = Engine_Api::_() -> getDbtable('styles', 'core');
		$select = $table -> select() -> where('type = ?', $subject -> getType()) -> where('id = ?', $subject -> getIdentity()) -> limit();

		$row = $table -> fetchRow($select);

		if (null !== $row && !empty($row -> style))
		{
			$this -> view -> headStyle() -> appendStyle($row -> style);
		}

		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

}
