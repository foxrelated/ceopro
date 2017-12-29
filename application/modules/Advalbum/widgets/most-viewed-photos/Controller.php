<?php
class Advalbum_Widget_MostViewedPhotosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
  		$params = $this -> _getAllParams();
		
		if(empty($params['title']))
		{
			$this -> view-> no_title = "no_title";
		}				
			$session = new Zend_Session_Namespace('mobile');
			if ($session -> mobile)
			{
				if($params['nomobile'] == 1)
				{
					return $this->setNoRender();
				}
			}
			
			$mode_grid = $mode_pinterest = 1;
			$mode_enabled = array();
			$view_mode = 'grid';
			
			if(isset($params['mode_grid']))
			{
				$mode_grid = $params['mode_grid'];
			}
			if($mode_grid)
			{
				$mode_enabled[] = 'grid';
			}			
			if(isset($params['mode_pinterest']))
			{
				$mode_pinterest = $params['mode_pinterest'];
			}
			if($mode_pinterest)
			{
				$mode_enabled[] = 'pinterest';
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

			
			$class_mode = "ynalbum-grid-view";
			switch ($view_mode) 
			{
				case 'pinterest':
					$class_mode = "ynalbum-pinterest-view";
					break;
				default:
					$class_mode = "ynalbum-grid-view";
					break;
			}
			$this -> view -> class_mode = $class_mode;
			$this -> view -> view_mode = $view_mode;
				
  		if ($this->_getParam ( 'number' ) != '' && $this->_getParam ( 'number' ) >= 0) {
			$limit = $this->_getParam ( 'number' );
		} else {
			$limit = 4;
		}

		$is_ajax = $this->_getParam ( 'ajax', 0);

		$this->view->is_ajax = $is_ajax;
		$this->view->limit = $limit;
		if (! $is_ajax) {
			$table = Engine_Api::_ ()->getDbtable ( 'photos', 'advalbum' );
			$atable = Engine_Api::_ ()->getDbtable ( 'albums', 'advalbum' );
			$Name = $table->info ( 'name' );
			$aName = $atable->info ( 'name' );
			$select = $table->select ()
			->from ( $Name )
			->joinLeft ( $aName, "$aName.album_id = $Name.album_id", '' )
			->where ( "$aName.search = ?", "1" )
			->order ("$Name.view_count DESC");

			$this->view->arr_photos = $arr_photos = $table->getAllowedPhotos( $select, $limit );
// 			if (count ( $arr_photos ) <= 0) {
// 				$this->setNoRender ();
// 			}
		}
    }
}