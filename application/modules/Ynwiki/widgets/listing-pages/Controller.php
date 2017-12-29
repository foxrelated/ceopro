<?php
class Ynwiki_Widget_ListingPagesController extends Engine_Content_Widget_Abstract
{
        
    public function indexAction()
    {       
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $values = $request->getParams();
        $this->view->formValues = array_filter($values);
        $this->view->pages = $paginator = Engine_Api::_()->ynwiki()->getPagesPaginator($values);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->ynwiki_page;
        $paginator->setItemCountPerPage($items_per_page);
        $paginator->setCurrentPageNumber($values['page']);
    }
}
?>