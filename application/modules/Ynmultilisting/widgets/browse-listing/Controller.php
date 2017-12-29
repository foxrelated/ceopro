<?php
class Ynmultilisting_Widget_BrowseListingController extends Engine_Content_Widget_Abstract 
{
	public function validForm() {
		$form = new Ynmultilisting_Form_Search(array(
            'type' => 'ynmultilisting_listing'
        ));
        $listingType = Engine_Api::_()->ynmultilisting() -> getCurrentListingType();
        if ($listingType)
        {
            $categories = $listingType -> getCategories();
            if (count($categories) > 0) {
                foreach ($categories as $category) {
                    $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
                }
            }
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynmultilisting') {
            if ($controller == 'index' && ($action == 'manage' || $action=='browse')) {
                $forwardListing = false;
            }
            if ($action != 'manage') {
                $form->removeElement('status');
            }
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'browse'), 'ynmultilisting_general', true));
        }

        if (!$listingType)
        {
            $form->removeElement('category');
        }
		
		 //Setup params
        $params = $request->getParams();
        if ($form->isValid($params)) {
            $params = $form->getValues();
        } else {
            $params = array();
        }
		return $params;
		
	}
	
	public function indexAction()
	{
        $values = $this -> validForm();
        if (isset($values['category_id'])) {
            $category = Engine_Api::_()->getItem('ynmultilisting_category', $values['category_id']);
            if ($category)
                $this->view->category = $category;
        }
        if (isset($params['category'])) {
            $categoryTbl = Engine_Api::_()->getItemTable('ynmultilisting_category');
            $categorySelect = $categoryTbl->select()->where('option_id = ?', $values['category']);
            $category = $categoryTbl->fetchRow($categorySelect);
            if ($category)
                $this->view->category = $category;
        }
        $this -> view -> formValues = $values;
        $p_arr = array();
        foreach ($params as $k => $v) {
            $p_arr[] = $k;
            $p_arr[] = $v;
        }
        $params_str = implode('/', $p_arr);
        $this->view->params_str = $params_str;

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

        $page = $values['page'];
        if (!$page){
            $page = 1;
        }
		if (!isset($values['listingtype_id'])) {
			$listingtype_id = Engine_Api::_()->ynmultilisting()->getCurrentListingTypeId();
            $values['listingtype_id'] = $listingtype_id;
		}
		$paginator = Engine_Api::_() -> getItemTable('ynmultilisting_listing') -> getListingsPaginator($values);
        $paginator -> setCurrentPageNumber($page);
        $paginator -> setItemCountPerPage($this ->_getParam('itemCountPerPage', 12));
        $this -> view -> paginator = $paginator;
		
		$listingIds = array();
        foreach ($paginator as $listing){
            $listingIds[] = $listing -> getIdentity();
        }
        $this->view->listingIds = implode("_", $listingIds);
	}
}