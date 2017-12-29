<?php
class Ynforum_Widget_ProfileAnnouncementsController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject('forum')) 
        {
            return $this->setNoRender();
        }
		
        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }
		$table = Engine_Api::_() -> getItemTable('ynforum_announcement');
		$select = $table -> select() -> where("forum_id = ?", $subject -> getIdentity()) -> where("highlight = 1");
		$announcement = $table -> fetchRow($select -> limit(1));
		$this -> view -> announcement = $announcement;
		$this -> view -> forum = $subject;
		$this -> view -> viewer = $viewer;
		if(!$subject -> checkPermission($viewer ,'forum', 'ynannoun.edit') && 
		!$subject -> checkPermission($viewer ,'forum', 'ynannoun.create')
		&& !$announcement)
		{
			return $this->setNoRender();
		}
    }
}