<?php
class Ultimatenews_Widget_CategoryUltimatenewsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if($this->_getParam('category_id') != ''  && $this->_getParam('category_id') > 0)
        {
        	$categoryparent_id = $this->_getParam('category_id');
			$number_article = 10;

			$wide = $this->_getParam('wide');
			$narrow = $this->_getParam('narrow');
			
			if (!is_numeric($wide) || intval($wide) < 0)
				$wide = 0;
			
			if (!is_numeric($narrow) || intval($narrow) < 0)
				$narrow = 0;
			
			$number_article = $wide + $narrow;
			
			if ($number_article == 0)
				$this->setNoRender();
			
            $this->view->paginator = $paginator = Engine_Api::_()->ultimatenews()->getContentsPaginator(array(
            'categoryparent' => $categoryparent_id,'number_article' => $number_article, 'group' => 'feed','order' => 'pubDate DESC', 'is_active' => 1, 'getcommment' => true,
                ));
			$this->view->wide = $wide;
			$this->view->narrow = $narrow;
			$paginator->setItemCountPerPage(1000);
        	$paginator->setCurrentPageNumber(1);
			if(!$paginator->getTotalItemCount())
				$this->setNoRender();
        }
		else
        {
            $this->setNoRender();
        }
        $this->view->viewer = $viewer;
    }
}
?>