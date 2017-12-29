<?php
class Advgroup_Widget_ProfileMp3MusicsController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
	}
 
  public function indexAction()
  {
   		$music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('mp3music');
		
		
		if (!$music_enable)
		{
			$this -> setNoRender();
		}
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		//check auth create
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$canCreate = $group -> authorization() -> isAllowed(null, 'music');
		$this -> view -> canCreate = $canCreate;

		if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer))
		{
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view"))
			{
				$this -> setNoRender();
			}
			else
			if (!$group -> authorization() -> isAllowed($viewer, "view"))
			{
				$this -> setNoRender();
			}
		}
		else
		if (!$group -> authorization() -> isAllowed($viewer, 'view'))
		{
			$this -> setNoRender();
		}
			
		//Get search condition
		$params = array();
		$params['group_id'] = $group -> getIdentity();
		$params['order'] = $this -> _getParam('order', 'recent');
		$params['ItemTable'] = 'mp3music_album';
		//Get Album paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_mapping') -> getAlbumsPaginator($params);
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 20));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
	    	 
	      $this->_childCount = $paginator->getTotalItemCount();	
	    }
		
  }
  public function getChildCount()
  {
    return $this->_childCount;
  }
}