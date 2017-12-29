<?php
class Ynadvsearch_Widget_YnGroupResultController extends Engine_Content_Widget_Abstract
{
 public function indexAction(){
 	
 		$params = $this -> _getAllParams();
 		$mode_list = $mode_grid = $mode_map = 1;
		$mode_enabled = array();
		$view_mode = 'list';
		

		if(isset($params['mode_list']))
		{
			$mode_list = $params['mode_list'];
		}
		if($mode_list)
		{
			$mode_enabled[] = 'list';
		}
		if(isset($params['mode_grid']))
		{
			$mode_grid = $params['mode_grid'];
		}
		if($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		if(isset($params['mode_map']))
		{
			$mode_map = $params['mode_map'];
		}
		if($mode_map)
		{
			$mode_enabled[] = 'map';
		}
		if(isset($params['view_mode']))
		{
			$view_mode = $params['view_mode'];
		}
		
		if($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}
		
			
		$this -> view -> mode_enabled = $mode_enabled;
		
		$class_mode = "advgroup_list-view";
		switch ($view_mode) 
		{
			case 'grid':
				$class_mode = "advgroup_grid-view";
				break;
			case 'map':
				$class_mode = "advgroup_map-view";
				break;
			default:
				$class_mode = "advgroup_list-view";
				break;
		}
		$this -> view -> class_mode = $class_mode;
		$this -> view -> view_mode = $view_mode;

				
      //Get fields for filtering group search.
     $request = Zend_Controller_Front::getInstance()->getRequest();
     $form = new Ynadvsearch_Form_YnGroupSearch();
     $form->isValid($request->getParams());
     $this->view->formValues = $params = $form->getValues();
	
     $params['search'] = '1';


      // Viewer 's friends field.
      $viewer = Engine_Api::_()->user()->getViewer();
      if( $viewer->getIdentity() && $params['view_group']) {
          $params['users'] = array();
          foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
            $params['users'][] = $memberinfo->user_id;
          }
          if(empty($params['users'])) $params['users'][] = 0;
      }
      $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('group') ->getGroupPaginator($params);

      //Set curent page
      $itemsPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.page', 9);
      $paginator->setItemCountPerPage($itemsPerPage);
      $paginator->setCurrentPageNumber($params['page']);
	  
 }
}