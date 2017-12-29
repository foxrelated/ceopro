<?php

class Ynmultilisting_Widget_RecentListingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
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

        $table = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $tableName = $table->info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('ynmultilisting', 'listings');
        } else {
            $select = $table->select()->from("$tableName", array("$tableName.*"));
        }
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        $select
            ->where('search = ?', 1)
            ->where('status = ?', 'open')
            ->where('approved_status = ?', 'approved')
            ->where('deleted = ?', '0')
            ->where('listingtype_id = ?', $listingtype ->getIdentity())
            ->order('creation_date DESC')
        ;
        $paginator = Zend_Paginator::factory($select);
        $paginator -> setCurrentPageNumber(1);
        $paginator -> setItemCountPerPage($this ->_getParam('itemCountPerPage', 12));
        $this -> view -> paginator = $paginator;
		if($paginator -> getTotalItemCount() <= 0)
		{
			return $this -> setNoRender();
		}
		
		$listingIds = array();
        foreach ($paginator as $listing){
            $listingIds[] = $listing -> getIdentity();
        }
        $this->view->listingIds = implode("_", $listingIds);
    }
}
