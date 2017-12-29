<?php
class Ynmultilisting_Widget_MostReviewedListingController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        $params = $this -> _getAllParams();
        $mode_list = $mode_grid = $mode_pin = $mode_map = 1;
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
        if(isset($params['mode_pin']))
        {
            $mode_pin = $params['mode_pin'];
        }
        if($mode_pin)
        {
            $mode_enabled[] = 'pin';
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

        $class_mode = "ynmultilisting_list-view";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "ynmultilisting_grid-view";
                break;
            case 'map':
                $class_mode = "ynmultilisting_map-view";
                break;
            case 'pin':
                $class_mode = "ynmultilisting_pin-view";
                break;
            default:
                $class_mode = "ynmultilisting_list-view";
                break;
        }
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if ($listingtype) {
            $params = array(
                'publish' => 1,
                'order' => 'view_count',
                'direction' => 'DESC'
            );
            $listings  = $listingtype->getAllListings($params);
            $paginator = Zend_Paginator::factory($listings);
            $paginator -> setCurrentPageNumber(1);
            $paginator -> setItemCountPerPage($this ->_getParam('itemCountPerPage', 12));

            if (count($listings) == 0) {
                $this->setNoRender(true);
            }
            $this -> view -> paginator = $paginator;
			
			$listingIds = array();
	        foreach ($paginator as $listing){
	            $listingIds[] = $listing -> getIdentity();
	        }
	        $this->view->listingIds = implode("_", $listingIds);
        }
        else {
            $this->setNoRender(true);
        }
	}
}