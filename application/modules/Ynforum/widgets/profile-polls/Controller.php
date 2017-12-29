
<?php
//LUANND 
class Ynforum_Widget_ProfilePollsController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {	
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject('forum')) {
            return $this->setNoRender();
        }
        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }
		if(!Engine_Api::_() -> hasModuleBootstrap('poll'))
		{
			 return $this->setNoRender();
		}	
		
		// get polls		
		$pollTable = Engine_Api::_() -> getItemTable('poll');
		$pollTableName = $pollTable -> info('name');
		
		$select = $pollTable -> select() -> from($pollTableName, "$pollTableName.*") -> setIntegrityCheck(false);		
		$select -> joinLeft("engine4_ynforum_highlights", "engine4_ynforum_highlights.item_id = $pollTableName.poll_id" , "engine4_ynforum_highlights.*");
		$select -> where("engine4_ynforum_highlights.highlight = 1")
				-> where("engine4_ynforum_highlights.type = ?",'poll')
				-> where("engine4_ynforum_highlights.forum_id = ?", $subject -> getIdentity())
				-> order("$pollTableName.poll_id DESC");
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);	
		$itemCountPerPage = $this -> _getParam('itemCountPerPage', 5);
		$paginator -> setItemCountPerPage($itemCountPerPage);		
		$this -> view -> forum = $subject;
		$this -> view -> viewer = $viewer;
    }
}