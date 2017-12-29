<?php
class Ynwiki_Widget_GroupProfilePagesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $page = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        if($this->_getParam('toValues') != ''  && $this->_getParam('toValues') > 0)
        {
                $pageIds = split(",",$this->_getParam('toValues'));
                $page_id = $pageIds[0];
                $page = Engine_Api::_()->getItem('ynwiki_page', $page_id);
        }
        if(!$page || !$page->authorization()->isAllowed($viewer,'view'))
        {
            $this->setNoRender();
        }
        $viewer = Engine_Api::_()->user()->getViewer();  
        $this->view->viewer = $viewer;
        $this->view->page = $page;
    }
}
?>