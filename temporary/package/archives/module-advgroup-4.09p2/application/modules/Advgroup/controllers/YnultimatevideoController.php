<?php
class Advgroup_YnultimatevideoController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
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
		//Checking Ynultimatevideo Plugin - View privacy
		$video_enable = Engine_Api::_() -> hasModuleBootstrap('ynultimatevideo');
		if (!$video_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_UltimateVideo_Search;

		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid())
		{
			return;
		}
		// Check create video authorization
		$canCreate = $group -> authorization() -> isAllowed($viewer, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'auth_video');

		if ($canCreate && $levelCreate)
		{
			$this -> view -> canCreate = true;
		}
		else
		{
			$this -> view -> canCreate = false;
		}

		//Prepare data filer
		$params = array();
		$params = $this -> _getAllParams();
		$params['parent_type'] = 'group';
		$params['parent_id'] = $group -> getIdentity();
		$params['search'] = 1;
		$params['status'] = 1;
		$params['limit'] = 12;
		$form -> populate($params);
		$this -> view -> formValues = $form -> getValues();
		
		//Get data from table Mappings

		//Get data from table video
		$tableVideo = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$select = $tableVideo -> select() 
			-> from($tableVideo -> info('name'), new Zend_Db_Expr("`video_id`"))
			-> where('parent_type = "group"') 
			-> where('parent_id = ?', $group -> getIdentity());
		$video_ids = $tableVideo -> fetchAll($select);
		
		foreach($video_ids as $video_id)
		{
			if(!in_array($video_id -> video_id, $params['ids']))
			{
				$params['ids'][] = $video_id -> video_id;
			}
		}
		
		//Get data
		$this -> view -> paginator = $paginator = $group -> getUltimateVideosPaginator($params);
		
		if (!empty($params['orderby']))
		{
			switch($params['orderby'])
			{
				case 'most_liked' :
					$this -> view -> infoCol = 'like';
					break;
				case 'most_commented' :
					$this -> view -> infoCol = 'comment';
					break;
				default :
					$this -> view -> infoCol = 'view';
					break;
			}
		}
	}

	public function manageAction()
	{
		//Checking Ynultimatevideo Plugin - View privacy
		$video_enable = Engine_Api::_() -> hasModuleBootstrap('ynultimatevideo');
		if (!$video_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_UltimateVideo_Search;

		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid())
		{
			return;
		}

		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_Video_Search;

		// Check create video authorization
		$canCreate = $group -> authorization() -> isAllowed(null, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'auth_video');

		if ($canCreate && $levelCreate)
		{
			$this -> view -> canCreate = true;
		}
		else
		{
			$this -> view -> canCreate = false;
		}

		//Prepare data filer
		$params = array();
		$params = $this -> _getAllParams();
		$params['parent_type'] = 'group';
		$params['parent_id'] = $group -> getIdentity();
		$params['user_id'] = $viewer -> getIdentity();
		$params['limit'] = 12;
		$form -> populate($params);
		$this -> view -> formValues = $form -> getValues();
		
		//Get data from table video
		$tableVideo = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$select = $tableVideo -> select() 
			-> from($tableVideo->info('name'), new Zend_Db_Expr("`video_id`"))
			-> where('parent_type = "group"') 
			-> where('parent_id = ?', $group -> getIdentity());
		$video_ids = $tableVideo -> fetchAll($select);
		
		//Merge ids
		foreach($video_ids as $video_id)
		{
			if(!in_array($video_id -> video_id, $params['ids']))
			{
				$params['ids'][] = $video_id -> video_id;
			}
		}
		
		//Get data
		$this -> view -> paginator = $paginator = $group -> getUltimateVideosPaginator($params);

		if (!empty($params['orderby']))
		{
			switch($params['orderby'])
			{
				case 'most_liked' :
					$this -> view -> infoCol = 'like';
					break;
				case 'most_commented' :
					$this -> view -> infoCol = 'comment';
					break;
				default :
					$this -> view -> infoCol = 'view';
					break;
			}
		}
		
		$this->view->group = $group;
	}
}
?>
