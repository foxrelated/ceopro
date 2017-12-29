<?php

class Ultimatenews_Widget_ListUltimatenewsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	// check permission subscribe	
    	$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->subscribe = false;
    	if(Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'subscribe'))
		{			
			$this->view->subscribe = true;
		}
		$category = $this->_getParam('category', 0);
		$category_parent = $this->_getParam('categoryparent', -1);
		$ultimatenews_search_query = $end_date = $start_date = NULL;
		$page = "1";
        if (isset($_SESSION['keysearch']))
        {
            $category = $_SESSION['keysearch']['category'];
            $ultimatenews_search_query = $_SESSION['keysearch']['searchText'];
			$category_parent = $_SESSION['keysearch']['category_parent'];
            $page = $_SESSION['keysearch']['nextpage'];
            $start_date = $_SESSION['start_date'];
            $end_date = $_SESSION['end_date'];
        }
        else if(isset($_POST['search']))
        {
            $category = $this->_getParam('category', 0);
			$category_parent = $this->_getParam('categoryparent', -1);
            $page = "1";
            if (isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
            {
                $page = $_POST['nextpage'];
            }
            $searchText = $_POST['search'];
            $ultimatenews_search_arr = explode(" ", $searchText);
            $ultimatenews_searchs = array();
            foreach ($ultimatenews_search_arr as $item)
            {
                if ($item != "")
                {
                    $ultimatenews_searchs[] = $item;
                }
            }
            $ultimatenews_search_query = implode("%", $ultimatenews_searchs);
            $ultimatenews_search_query = "%" . $ultimatenews_search_query . "%";
            $_SESSION['category'] = $category;
			$_SESSION['category_parent'] = $category_parent;
            $_SESSION['searchText'] = $ultimatenews_search_query;
        }

		 $wide = $this->_getParam('wide', 3);//number of article in wide area
		 $narrow = $this->_getParam('narrow', 7);//number of article in narrow area
		 $number_article = $wide + $narrow;//number of articles per widget
		 $categories_per_page = $this->_getParam('categories_per_page');//number of feeds per page
				
		//Long Edited
		$this->view->paginator = $paginator = Engine_Api::_()->ultimatenews()->getCategoriesByNewsFilter(array(
            'category_id' => $category, 
            'number_article' => $number_article,
            'search' => $ultimatenews_search_query, 
            'is_active' => 1, 
            'approved' => 1,
            'getcommment' => true,
            'start_date' => $start_date, 
            'end_date' => $end_date, 
            'group' => 'feed', 
            'limit' => $categories_per_page, 
            'category_parent' => $category_parent,
        ));
		
		$this->view->wide = $wide;
		$this->view->narrow = $narrow;
        $paginator->setItemCountPerPage($categories_per_page);
        $paginator->setCurrentPageNumber($page);
		
    }

}